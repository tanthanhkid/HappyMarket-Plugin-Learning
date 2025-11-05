# Hướng dẫn Deploy Plugin

Có 2 cách để deploy plugin vào WordPress Docker container:

## Cách 1: Deploy Local (Docker trên máy local)

Sử dụng script `deploy-plugin.sh` để copy plugin trực tiếp vào container local:

```bash
./deploy-plugin.sh
```

Script này sẽ:
1. Kiểm tra container `wordpress_app` có đang chạy không
2. Xóa plugin cũ (nếu có)
3. Copy plugin mới vào container
4. Set permissions đúng
5. Verify installation

## Cách 2: Deploy qua SSH (Remote Server)

Sử dụng script `deploy-plugin-via-ssh.sh` để deploy plugin vào server remote qua SSH:

```bash
./deploy-plugin-via-ssh.sh user@example.com
```

Hoặc với IP:
```bash
./deploy-plugin-via-ssh.sh root@192.168.1.100
```

Script này sẽ:
1. Tạo archive của plugin
2. Copy archive lên remote server qua SCP
3. Extract và deploy plugin vào container trên remote server
4. Set permissions và verify installation

### Yêu cầu cho SSH deployment:
- SSH access đến server
- Docker và docker-compose đã cài đặt trên remote server
- Container `wordpress_app` đang chạy trên remote server

## Sau khi Deploy

1. Truy cập WordPress Admin: http://localhost:8080/wp-admin (hoặc URL tương ứng)
2. Vào **Plugins** > **Installed Plugins**
3. Tìm **HappyMarket Learning Manager**
4. Click **Activate**

## Troubleshooting

### Container không chạy
```bash
docker-compose up -d
```

### Xem logs container
```bash
docker logs wordpress_app
```

### Kiểm tra plugin đã được copy chưa
```bash
docker exec wordpress_app ls -la /var/www/html/wp-content/plugins/
```

### Xóa và deploy lại
```bash
docker exec wordpress_app rm -rf /var/www/html/wp-content/plugins/happy-market-learning
./deploy-plugin.sh
```
