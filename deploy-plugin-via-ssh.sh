#!/bin/bash

# Script để deploy plugin vào WordPress Docker container thông qua SSH
# Usage: ./deploy-plugin-via-ssh.sh [user@]hostname

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
SSH_HOST="${1:-}"
CONTAINER_NAME="wordpress_app"
PLUGIN_NAME="happy-market-learning"
PLUGIN_SOURCE_DIR="./happy-market-learning"
PLUGIN_DEST_DIR="/var/www/html/wp-content/plugins/${PLUGIN_NAME}"
TEMP_ARCHIVE="/tmp/${PLUGIN_NAME}.tar.gz"

# Check if SSH host is provided
if [ -z "$SSH_HOST" ]; then
    echo -e "${RED}Error: SSH host is required!${NC}"
    echo -e "${YELLOW}Usage: ./deploy-plugin-via-ssh.sh [user@]hostname${NC}"
    echo -e "${YELLOW}Example: ./deploy-plugin-via-ssh.sh user@example.com${NC}"
    exit 1
fi

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Deploying Plugin via SSH${NC}"
echo -e "${GREEN}========================================${NC}\n"

# Check if plugin source directory exists
if [ ! -d "$PLUGIN_SOURCE_DIR" ]; then
    echo -e "${RED}Error: Plugin source directory '${PLUGIN_SOURCE_DIR}' not found!${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Plugin source directory found${NC}\n"

# Create temporary archive
echo -e "${YELLOW}Creating plugin archive...${NC}"
cd "$(dirname "$0")"
tar -czf "${TEMP_ARCHIVE}" -C "$(dirname "$PLUGIN_SOURCE_DIR")" "$(basename "$PLUGIN_SOURCE_DIR")"
echo -e "${GREEN}✓ Archive created: ${TEMP_ARCHIVE}${NC}\n"

# Copy archive to remote server
echo -e "${YELLOW}Copying archive to remote server...${NC}"
scp "${TEMP_ARCHIVE}" "${SSH_HOST}:${TEMP_ARCHIVE}"
echo -e "${GREEN}✓ Archive copied to remote server${NC}\n"

# Execute deployment on remote server
echo -e "${YELLOW}Deploying plugin on remote server...${NC}"
ssh "${SSH_HOST}" bash <<EOF
set -e

# Check if container is running
if ! docker ps --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}\$"; then
    echo "Error: Container '${CONTAINER_NAME}' is not running!"
    echo "Please start the container first: docker-compose up -d"
    exit 1
fi

# Remove existing plugin
docker exec ${CONTAINER_NAME} bash -c "rm -rf ${PLUGIN_DEST_DIR}" 2>/dev/null || true

# Extract archive to container
ARCHIVE_NAME=$(basename ${TEMP_ARCHIVE})
docker cp ${TEMP_ARCHIVE} ${CONTAINER_NAME}:/tmp/${ARCHIVE_NAME}
docker exec ${CONTAINER_NAME} bash -c "mkdir -p $(dirname ${PLUGIN_DEST_DIR}) && cd $(dirname ${PLUGIN_DEST_DIR}) && tar -xzf /tmp/${ARCHIVE_NAME}"
docker exec ${CONTAINER_NAME} bash -c "rm -f /tmp/${ARCHIVE_NAME}"

# Set correct permissions
docker exec ${CONTAINER_NAME} bash -c "chown -R www-data:www-data ${PLUGIN_DEST_DIR}"
docker exec ${CONTAINER_NAME} bash -c "find ${PLUGIN_DEST_DIR} -type d -exec chmod 755 {} \\;"
docker exec ${CONTAINER_NAME} bash -c "find ${PLUGIN_DEST_DIR} -type f -exec chmod 644 {} \\;"

# Clean up temporary archive
rm -f ${TEMP_ARCHIVE}

# Verify installation
if docker exec ${CONTAINER_NAME} test -f "${PLUGIN_DEST_DIR}/happy-market-learning.php"; then
    echo "✓ Plugin deployed successfully!"
else
    echo "✗ Error: Plugin deployment failed!"
    exit 1
fi
EOF

# Clean up local temporary archive
rm -f "${TEMP_ARCHIVE}"

echo -e "\n${GREEN}========================================${NC}"
echo -e "${GREEN}Deployment Complete!${NC}"
echo -e "${GREEN}========================================${NC}\n"
echo -e "Plugin deployed to: ${SSH_HOST}"
echo -e "Container: ${CONTAINER_NAME}"
echo -e "Plugin location: ${PLUGIN_DEST_DIR}"
echo -e "\n"
