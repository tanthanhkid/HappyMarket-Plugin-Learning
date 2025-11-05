#!/bin/bash

# Script để deploy plugin vào WordPress Docker container
# Usage: ./deploy-plugin.sh

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
CONTAINER_NAME="wordpress_app"
PLUGIN_NAME="happy-market-learning"
PLUGIN_SOURCE_DIR="./happy-market-learning"
PLUGIN_DEST_DIR="/var/www/html/wp-content/plugins/${PLUGIN_NAME}"

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Deploying HappyMarket Learning Plugin${NC}"
echo -e "${GREEN}========================================${NC}\n"

# Check if container is running
echo -e "${YELLOW}Checking Docker container...${NC}"
if ! docker ps --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}$"; then
    echo -e "${RED}Error: Container '${CONTAINER_NAME}' is not running!${NC}"
    echo -e "${YELLOW}Please start the container first: docker-compose up -d${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Container '${CONTAINER_NAME}' is running${NC}\n"

# Check if plugin source directory exists
if [ ! -d "$PLUGIN_SOURCE_DIR" ]; then
    echo -e "${RED}Error: Plugin source directory '${PLUGIN_SOURCE_DIR}' not found!${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Plugin source directory found${NC}\n"

# Remove existing plugin in container if exists
echo -e "${YELLOW}Removing existing plugin (if exists)...${NC}"
docker exec ${CONTAINER_NAME} bash -c "rm -rf ${PLUGIN_DEST_DIR}" 2>/dev/null || true
echo -e "${GREEN}✓ Cleaned up existing plugin${NC}\n"

# Copy plugin to container
echo -e "${YELLOW}Copying plugin files to container...${NC}"
docker cp ${PLUGIN_SOURCE_DIR} ${CONTAINER_NAME}:${PLUGIN_DEST_DIR}
echo -e "${GREEN}✓ Plugin files copied successfully${NC}\n"

# Set correct permissions
echo -e "${YELLOW}Setting permissions...${NC}"
docker exec ${CONTAINER_NAME} bash -c "chown -R www-data:www-data ${PLUGIN_DEST_DIR}"
docker exec ${CONTAINER_NAME} bash -c "find ${PLUGIN_DEST_DIR} -type d -exec chmod 755 {} \;"
docker exec ${CONTAINER_NAME} bash -c "find ${PLUGIN_DEST_DIR} -type f -exec chmod 644 {} \;"
echo -e "${GREEN}✓ Permissions set correctly${NC}\n"

# Verify installation
echo -e "${YELLOW}Verifying installation...${NC}"
if docker exec ${CONTAINER_NAME} test -f "${PLUGIN_DEST_DIR}/happy-market-learning.php"; then
    echo -e "${GREEN}✓ Plugin main file found${NC}"
else
    echo -e "${RED}✗ Error: Plugin main file not found!${NC}"
    exit 1
fi

# Display plugin info
echo -e "\n${GREEN}========================================${NC}"
echo -e "${GREEN}Deployment Complete!${NC}"
echo -e "${GREEN}========================================${NC}\n"
echo -e "Plugin location: ${PLUGIN_DEST_DIR}"
echo -e "Container: ${CONTAINER_NAME}"
echo -e "\n${YELLOW}Next steps:${NC}"
echo -e "1. Go to WordPress Admin: http://localhost:8080/wp-admin"
echo -e "2. Navigate to Plugins page"
echo -e "3. Activate 'HappyMarket Learning Manager' plugin"
echo -e "\n"
