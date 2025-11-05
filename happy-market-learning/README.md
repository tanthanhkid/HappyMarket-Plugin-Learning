# HappyMarket Learning Manager

WordPress plugin để quản lý và hiển thị các trang landing page cho các bài học video được nhúng từ YouTube. Plugin hỗ trợ quảng cáo và tích hợp WooCommerce để up-sale sản phẩm.

## Tính năng

- ✅ Quản lý Series và Bài học video từ YouTube
- ✅ Quảng cáo dạng image với nhiều vị trí hiển thị
- ✅ Tích hợp WooCommerce để up-sale sản phẩm
- ✅ Quyền truy cập linh hoạt (Public, Login, Membership)
- ✅ Shortcodes để chèn vào bất kỳ trang nào
- ✅ Templates có thể override từ theme
- ✅ Responsive design

## Yêu cầu

- WordPress 5.0+
- PHP 7.4+
- WooCommerce 3.0+ (tùy chọn, nếu tích hợp sản phẩm)

## Cài đặt

1. Upload plugin vào thư mục `/wp-content/plugins/`
2. Activate plugin từ WordPress admin
3. Vào **Settings > HappyMarket Learning** để cấu hình

## Sử dụng

### Tạo Series

1. Vào **Series > Add New**
2. Nhập tên và mô tả series
3. Chọn Featured Image
4. Cấu hình Access Settings
5. Publish

### Tạo Lesson

1. Vào **Lessons > Add New**
2. Chọn Series từ dropdown
3. Nhập YouTube URL
4. Thêm mô tả bài học
5. Set Order trong series
6. Thêm quảng cáo và sản phẩm (nếu có)
7. Publish

### Shortcodes

#### Hiển thị Series
```
[hm_lesson_series id="123" layout="grid" columns="3"]
```

#### Hiển thị Video
```
[hm_lesson_video id="456" autoplay="false"]
```

#### Hiển thị Quảng cáo
```
[hm_lesson_ads lesson_id="456" position="sidebar"]
```

#### Hiển thị Products
```
[hm_lesson_products lesson_id="456" position="after_video" columns="3"]
```

## Development

Xem file `DESIGN_DOCUMENT.md` để biết chi tiết về kiến trúc và thiết kế của plugin.

## License

GPL v2 or later
