# Tài liệu Thiết kế Plugin: HappyMarket Learning Manager

## Mục lục

1. [Tổng quan Plugin](#tổng-quan-plugin)
2. [Kiến trúc Hệ thống](#kiến-trúc-hệ-thống)
3. [Cấu trúc Database](#cấu-trúc-database)
4. [Quản lý Series và Bài học](#quản-lý-series-và-bài-học)
5. [Quản lý Quảng cáo](#quản-lý-quảng-cáo)
6. [Tích hợp WooCommerce](#tích-hợp-woocommerce)
7. [Shortcodes](#shortcodes)
8. [Templates](#templates)
9. [Admin Interface Chi tiết](#admin-interface-chi-tiết)
10. [Frontend Display](#frontend-display)
11. [API & Hooks](#api--hooks)
12. [Security](#security)
13. [Performance](#performance)
14. [File Structure](#file-structure)
15. [Implementation Priority](#implementation-priority)
16. [Hướng dẫn Sử dụng](#hướng-dẫn-sử-dụng)

---

## Tổng quan Plugin

### Mục đích

Plugin **HappyMarket Learning Manager** cho phép quản lý và hiển thị các trang landing page cho các bài học video được nhúng từ YouTube. Mỗi bài học thuộc về một Series (chuỗi bài học), và các landing page có thể được cấu hình với quảng cáo và sản phẩm WooCommerce để up-sale trong quá trình học tập.

### Tính năng Chính

1. **Quản lý Series và Bài học**
   - Tạo và quản lý các chuỗi bài học (Series)
   - Quản lý các bài học video từ YouTube
   - Sắp xếp thứ tự bài học trong series
   - Hỗ trợ nhiều series với nhiều bài học

2. **Quản lý Quảng cáo**
   - Thêm quảng cáo dạng image với URL
   - Cấu hình vị trí hiển thị (sidebar, popup, inline)
   - Thiết lập quy tắc hiển thị (delay, trigger)

3. **Tích hợp WooCommerce**
   - Chọn sản phẩm từ WooCommerce để up-sale
   - Hiển thị sản phẩm tại nhiều vị trí khác nhau
   - Tích hợp với giỏ hàng WooCommerce

4. **Quyền Truy cập**
   - Hỗ trợ public (miễn phí)
   - Yêu cầu đăng nhập
   - Tích hợp với membership plugins

5. **Hiển thị Linh hoạt**
   - Shortcodes để chèn vào bất kỳ trang/post nào
   - Custom post types với template chuyên dụng
   - Responsive design

---

## Kiến trúc Hệ thống

### 1. Custom Post Types

#### hm_series
- **Mục đích**: Quản lý các chuỗi bài học
- **Slug**: `hm_series`
- **Supports**: title, editor, thumbnail, excerpt, custom-fields
- **Public**: true
- **Has Archive**: true
- **Menu Icon**: dashicons-video-alt3

#### hm_lesson
- **Mục đích**: Quản lý các bài học đơn lẻ
- **Slug**: `hm_lesson`
- **Supports**: title, editor, thumbnail, excerpt, custom-fields
- **Public**: true
- **Has Archive**: true
- **Menu Icon**: dashicons-playlist-video

### 2. Database Schema

#### Sử dụng WordPress Native
- **Post Meta**: Lưu trữ các thông tin bổ sung (meta fields)
- **Taxonomies**: Phân loại và gắn thẻ bài học
- **Post Relationships**: Sử dụng meta để liên kết lesson với series

#### Custom Tables (Tùy chọn cho tương lai)
- `wp_hm_lesson_progress`: Theo dõi tiến độ học tập của user
- `wp_hm_analytics`: Thống kê lượt xem, click quảng cáo, etc.

### 3. Dependencies

- **WordPress**: 5.0+
- **PHP**: 7.4+
- **WooCommerce**: 3.0+ (tùy chọn, nếu tích hợp sản phẩm)
- **MySQL**: 5.6+ hoặc MariaDB 10.0+

---

## Cấu trúc Database

### Meta Fields cho hm_series

| Field Name | Type | Description | Default |
|------------|------|-------------|---------|
| `_hm_series_description` | text | Mô tả chi tiết series | '' |
| `_hm_series_image` | integer | ID của attachment image đại diện | 0 |
| `_hm_series_access_type` | string | Loại truy cập: public, login, membership | 'public' |
| `_hm_series_order` | integer | Thứ tự hiển thị series | 0 |
| `_hm_series_lessons_count` | integer | Số lượng bài học (auto-calculated) | 0 |

### Meta Fields cho hm_lesson

| Field Name | Type | Description | Default |
|------------|------|-------------|---------|
| `_hm_lesson_series_id` | integer | ID của series chứa bài học | 0 |
| `_hm_lesson_youtube_url` | string | URL YouTube video đầy đủ | '' |
| `_hm_lesson_youtube_id` | string | ID video được extract từ URL | '' |
| `_hm_lesson_duration` | string | Thời lượng video (MM:SS) | '' |
| `_hm_lesson_order` | integer | Thứ tự trong series | 0 |
| `_hm_lesson_access_type` | string | Loại truy cập: public, login, membership | 'public' |
| `_hm_lesson_description` | text | Mô tả bài học | '' |
| `_hm_lesson_ads` | text (JSON) | JSON array chứa cấu hình quảng cáo | '[]' |
| `_hm_lesson_products` | text (JSON) | JSON array chứa product IDs từ WooCommerce | '[]' |
| `_hm_lesson_ad_positions` | text (JSON) | Cấu hình vị trí hiển thị quảng cáo/sản phẩm | '{}' |

### Taxonomy

#### hm_lesson_category
- **Hierarchical**: true
- **Public**: true
- **Object Types**: hm_lesson

#### hm_lesson_tag
- **Hierarchical**: false
- **Public**: true
- **Object Types**: hm_lesson

---

## Quản lý Series và Bài học

### Admin Interface cho Series

#### List Table Columns
- **Title**: Tên series (có thể edit inline)
- **Lessons Count**: Số lượng bài học trong series
- **Access Type**: Loại truy cập (public/login/membership)
- **Order**: Thứ tự hiển thị (có thể sắp xếp)
- **Date**: Ngày tạo/cập nhật

#### Meta Boxes

**1. Series Information**
- Mô tả series (rich text editor)
- Featured Image selector
- Excerpt

**2. Access Settings**
- Radio buttons: Public / Login Required / Membership Required
- Membership plugin selector (nếu có)

**3. Display Settings**
- Order number (integer input)
- Custom CSS classes

**4. Series Lessons**
- Hiển thị danh sách bài học trong series
- Drag & drop để sắp xếp thứ tự
- Quick add lesson button

### Admin Interface cho Lesson

#### List Table Columns
- **Title**: Tên bài học
- **Series**: Series chứa bài học (có filter dropdown)
- **YouTube ID**: ID video (có thumbnail preview)
- **Order**: Thứ tự trong series
- **Access Type**: Loại truy cập
- **Date**: Ngày tạo/cập nhật

#### Meta Boxes

**1. Lesson Information**
- Series Selection (dropdown với search)
- Lesson Title
- Lesson Description (rich text editor)
- Featured Image

**2. Video Settings**
- YouTube URL input (với validation và preview)
- Auto-extract YouTube ID
- Video thumbnail preview
- Duration input (optional, có thể auto-fetch với API)
- Autoplay checkbox

**3. Series Order**
- Numeric input cho thứ tự
- Auto-suggest next available number
- Drag & drop từ list view

**4. Access Settings**
- Kế thừa từ series hoặc override
- Radio buttons: Public / Login Required / Membership Required

**5. Quảng cáo Management**
- Repeater field cho quảng cáo
- Mỗi quảng cáo có:
  - Image URL (với media library picker)
  - Link URL
  - Alt Text
  - Position selector (sidebar, popup, before_video, after_video, between_video)
  - Display Rules (delay, trigger conditions)
  - Active checkbox

**6. WooCommerce Products**
- Product selector với AJAX search
- Multiple product selection
- Drag & drop để sắp xếp thứ tự
- Position selector cho từng product hoặc nhóm
- Custom display settings

---

## Quản lý Quảng cáo

### Cấu trúc Quảng cáo

Mỗi quảng cáo được lưu dưới dạng JSON object trong array:

```json
{
  "id": "ad_1234567890",
  "image_url": "https://example.com/ad.jpg",
  "link_url": "https://example.com/target",
  "alt_text": "Mô tả quảng cáo",
  "position": "sidebar",
  "display_rules": {
    "delay": 30,
    "trigger": "time",
    "video_progress": 50,
    "scroll_position": 75,
    "max_displays": 3,
    "cooldown": 3600
  },
  "active": true,
  "created_at": "2024-01-15 10:30:00",
  "updated_at": "2024-01-15 10:30:00"
}
```

### Vị trí hiển thị

#### 1. Sidebar
- **Vị trí**: Fixed sidebar bên phải hoặc trái
- **Behavior**: Hiển thị liên tục khi scroll
- **Responsive**: Ẩn trên mobile, hoặc chuyển thành sticky bottom
- **Settings**: Width, position (left/right), z-index

#### 2. Popup/Modal
- **Vị trí**: Overlay toàn màn hình hoặc centered
- **Behavior**: Hiển thị theo trigger (delay, scroll, video progress)
- **Settings**: 
  - Delay (seconds)
  - Trigger type (time, scroll, video_progress)
  - Close button
  - Auto-close after X seconds
  - Max displays per session
  - Cooldown period

#### 3. Before Video
- **Vị trí**: Trước video player
- **Behavior**: Hiển thị khi page load
- **Settings**: Height, full-width option

#### 4. After Video
- **Vị trí**: Sau video player
- **Behavior**: Hiển thị sau khi video kết thúc hoặc user pause
- **Settings**: Height, spacing

#### 5. Between Video
- **Vị trí**: Giữa các video trong series (navigation area)
- **Behavior**: Hiển thị trong phần chuyển đổi giữa các bài học
- **Settings**: Inline hoặc popup

### Display Rules

#### Trigger Types
- **time**: Sau X giây từ khi page load
- **scroll**: Khi user scroll đến X% của page
- **video_progress**: Khi video đạt X% tiến độ
- **video_end**: Khi video kết thúc
- **video_pause**: Khi user pause video

#### Rate Limiting
- **max_displays**: Số lần tối đa hiển thị trong một session
- **cooldown**: Thời gian chờ (seconds) trước khi hiển thị lại
- **per_user_limit**: Giới hạn theo user (sử dụng cookies/localStorage)

---

## Tích hợp WooCommerce

### Product Selection

#### Admin Interface
- **AJAX Search**: Tìm kiếm sản phẩm theo tên, SKU
- **Product Selector**: Multi-select với preview
- **Product Cards**: Hiển thị thumbnail, title, price
- **Drag & Drop**: Sắp xếp thứ tự sản phẩm

#### Lưu trữ
- Lưu array of product IDs trong `_hm_lesson_products`
- Format: `[123, 456, 789]`
- Có thể lưu thêm metadata: `[{"id": 123, "order": 1, "position": "sidebar"}, ...]`

### Product Display

#### Product Card Components
- **Product Image**: Thumbnail với link
- **Product Title**: Link đến product page
- **Price**: Regular price, sale price (nếu có)
- **Rating**: Star rating (nếu có)
- **Add to Cart Button**: AJAX add to cart
- **Quick View**: Modal preview (optional)

#### Vị trí hiển thị

**1. Sidebar Widget**
- Fixed sidebar với product grid
- Scrollable nếu có nhiều sản phẩm
- Responsive: collapse trên mobile

**2. After Video Section**
- Grid layout sau video player
- Responsive: 1-4 columns tùy screen size
- "Xem thêm sản phẩm" link

**3. Popup/Modal**
- Hiển thị khi video kết thúc
- Product carousel hoặc grid
- Close button và "Xem tất cả" link

**4. Inline trong Content**
- Shortcode `[hm_lesson_products]`
- Flexible positioning
- Customizable layout

### WooCommerce Integration Details

#### Hooks sử dụng
```php
// Lấy thông tin sản phẩm
wc_get_product($product_id)

// Add to cart
WC()->cart->add_to_cart($product_id)

// Product loop
woocommerce_product_loop_start()
woocommerce_product_loop_end()
```

#### AJAX Add to Cart
- Sử dụng WooCommerce AJAX endpoints
- Update cart count trong header
- Show success/error messages
- Reload cart fragment

---

## Shortcodes

### [hm_lesson_series]

Hiển thị danh sách bài học trong một series.

**Attributes:**
- `id` (required): ID của series
- `layout`: `grid` hoặc `list` (default: `grid`)
- `columns`: Số cột trong grid (default: 3)
- `show_thumbnails`: `true` hoặc `false` (default: `true`)
- `show_duration`: `true` hoặc `false` (default: `true`)
- `show_excerpt`: `true` hoặc `false` (default: `false`)

**Example:**
```
[hm_lesson_series id="123" layout="grid" columns="3" show_thumbnails="true"]
```

### [hm_lesson_video]

Hiển thị video bài học.

**Attributes:**
- `id` (required): ID của lesson
- `autoplay`: `true` hoặc `false` (default: `false`)
- `width`: Width của video player (default: 100%)
- `height`: Height của video player (default: auto)
- `controls`: `true` hoặc `false` (default: `true`)
- `show_title`: `true` hoặc `false` (default: `true`)

**Example:**
```
[hm_lesson_video id="456" autoplay="false" width="100%"]
```

### [hm_lesson_ads]

Hiển thị quảng cáo tại vị trí cụ thể.

**Attributes:**
- `lesson_id` (required): ID của lesson
- `position`: `sidebar`, `popup`, `before_video`, `after_video`, `between_video` (default: `sidebar`)
- `limit`: Số lượng quảng cáo tối đa (default: 1)

**Example:**
```
[hm_lesson_ads lesson_id="456" position="sidebar" limit="1"]
```

### [hm_lesson_products]

Hiển thị sản phẩm up-sale.

**Attributes:**
- `lesson_id` (required): ID của lesson
- `position`: `sidebar`, `after_video`, `popup`, `inline` (default: `after_video`)
- `columns`: Số cột trong grid (default: 3)
- `limit`: Số lượng sản phẩm tối đa (default: 4)
- `show_price`: `true` hoặc `false` (default: `true`)
- `show_add_to_cart`: `true` hoặc `false` (default: `true`)

**Example:**
```
[hm_lesson_products lesson_id="456" position="after_video" columns="3" limit="4"]
```

### [hm_lesson_navigation]

Hiển thị navigation (previous/next lesson).

**Attributes:**
- `lesson_id` (required): ID của lesson hiện tại
- `show_series_link`: `true` hoặc `false` (default: `true`)
- `show_thumbnails`: `true` hoặc `false` (default: `true`)

**Example:**
```
[hm_lesson_navigation lesson_id="456" show_series_link="true"]
```

---

## Templates

### Single Lesson Template

**File**: `templates/single-hm_lesson.php`

**Layout Structure:**
```
┌─────────────────────────────────────┐
│  Breadcrumb / Series Link          │
├─────────────────────────────────────┤
│  Lesson Title                       │
├─────────────────────────────────────┤
│  ┌─────────────────┬──────────────┐ │
│  │                 │              │ │
│  │  Video Player  │  Sidebar     │ │
│  │                 │  - Ads       │ │
│  │                 │  - Products  │ │
│  │                 │              │ │
│  └─────────────────┴──────────────┘ │
├─────────────────────────────────────┤
│  Before Video Ads (if any)          │
├─────────────────────────────────────┤
│  Lesson Description                  │
├─────────────────────────────────────┤
│  After Video Ads (if any)           │
├─────────────────────────────────────┤
│  Products Section                   │
├─────────────────────────────────────┤
│  Navigation (Prev/Next)             │
└─────────────────────────────────────┘
```

**Template Hierarchy:**
1. `single-hm_lesson-{slug}.php`
2. `single-hm_lesson-{id}.php`
3. `single-hm_lesson.php`
4. `single.php`
5. `singular.php`
6. `index.php`

**Template Functions:**
- `hm_get_lesson_video($lesson_id)`: Lấy video embed code
- `hm_get_lesson_series($lesson_id)`: Lấy series info
- `hm_get_lesson_ads($lesson_id, $position)`: Lấy quảng cáo
- `hm_get_lesson_products($lesson_id, $position)`: Lấy sản phẩm
- `hm_get_lesson_navigation($lesson_id)`: Lấy navigation

### Archive Templates

#### Archive Series
**File**: `templates/archive-hm_series.php`

**Layout**: Grid hoặc list view
- Series thumbnail
- Series title
- Series description (excerpt)
- Number of lessons
- Access type badge
- "View Series" link

#### Archive Lesson
**File**: `templates/archive-hm_lesson.php`

**Layout**: Grid hoặc list view
- Lesson thumbnail (video thumbnail)
- Lesson title
- Series name
- Duration
- Access type badge
- "View Lesson" link

### Template Override

Theme có thể override templates bằng cách copy vào:
- `{theme}/happy-market-learning/single-hm_lesson.php`
- `{theme}/happy-market-learning/archive-hm_series.php`
- `{theme}/happy-market-learning/archive-hm_lesson.php`

---

## Admin Interface Chi tiết

### Series Management

#### List Table Features
- **Sortable Columns**: Title, Order, Date, Lessons Count
- **Bulk Actions**: Delete, Change Access Type, Change Order
- **Quick Edit**: Title, Order, Access Type
- **Row Actions**: Edit, Quick Edit, Trash, View, Duplicate
- **Filters**: Access Type, Date Range
- **Search**: Search by title

#### Edit Screen
- **Title**: Series title
- **Permalink**: Editable slug
- **Editor**: Rich text description
- **Featured Image**: Series thumbnail
- **Excerpt**: Short description
- **Meta Boxes**: As described above

### Lesson Management

#### List Table Features
- **Sortable Columns**: Title, Series, Order, Date
- **Bulk Actions**: Delete, Change Series, Change Access Type, Change Order
- **Quick Edit**: Title, Series, Order, Access Type
- **Row Actions**: Edit, Quick Edit, Trash, View, Duplicate
- **Filters**: Series, Access Type, Category, Date Range
- **Search**: Search by title, YouTube ID

#### Edit Screen
- **Title**: Lesson title
- **Permalink**: Editable slug
- **Editor**: Rich text description
- **Featured Image**: Lesson thumbnail
- **Excerpt**: Short description
- **Meta Boxes**: As described above

#### YouTube URL Validation
- Validate URL format
- Extract video ID
- Fetch thumbnail và metadata (nếu có API key)
- Preview thumbnail trong admin
- Error handling cho invalid URLs

### Settings Page

**Menu**: Settings > HappyMarket Learning

#### General Settings Tab
- **Default Access Type**: public/login/membership
- **Default Ad Position**: sidebar/popup/after_video
- **YouTube API Key**: (optional) Để fetch video metadata
- **Enable Analytics**: Track views, clicks
- **Custom CSS**: Additional CSS

#### WooCommerce Integration Tab
- **Enable WooCommerce Integration**: Toggle
- **Default Product Position**: sidebar/after_video/popup
- **Default Product Columns**: 1-4
- **Default Product Limit**: Number
- **Show Price**: Toggle
- **Show Add to Cart**: Toggle

#### Display Settings Tab
- **Default Layout**: grid/list
- **Default Columns**: 1-6
- **Show Thumbnails**: Toggle
- **Show Duration**: Toggle
- **Show Excerpt**: Toggle
- **Pagination**: Posts per page

#### Permissions Tab
- **Required Capabilities**: 
  - Manage Series: `manage_hm_series`
  - Manage Lessons: `manage_hm_lessons`
  - Manage Settings: `manage_hm_settings`

---

## Frontend Display

### Video Player

#### YouTube Embed
- **Responsive**: 16:9 aspect ratio
- **Embed Options**:
  - `autoplay`: false (default)
  - `controls`: true
  - `modestbranding`: true
  - `rel`: 0 (hide related videos)
  - `showinfo`: 0
  - `enablejsapi`: 1 (for progress tracking)

#### Custom Features
- **Progress Tracking**: Lưu tiến độ xem (optional)
- **Next/Previous Overlay**: Hiển thị khi video gần kết thúc
- **Quality Selector**: (nếu có API)
- **Playback Speed**: 0.5x, 0.75x, 1x, 1.25x, 1.5x, 2x

#### Mobile Optimization
- Full-width on mobile
- Touch-friendly controls
- Landscape lock option

### Access Control

#### Public Access
- Không yêu cầu đăng nhập
- Hiển thị đầy đủ nội dung

#### Login Required
- Check `is_user_logged_in()`
- Redirect đến login page nếu chưa đăng nhập
- Return URL sau khi login

#### Membership Required
- Hooks cho membership plugins:
  - **MemberPress**: `mepr_user_has_access_to_post`
  - **LearnDash**: `sfwd_lms_has_access`
  - **LifterLMS**: `llms_is_user_enrolled`
  - Custom filter: `hm_lesson_access_check`

#### Redirect Logic
```php
if (!hm_check_lesson_access($lesson_id)) {
    $redirect_url = add_query_arg('redirect_to', get_permalink($lesson_id), wp_login_url());
    wp_redirect($redirect_url);
    exit;
}
```

### Quảng cáo Display

#### Sidebar Ads
- **Position**: Fixed hoặc sticky
- **Width**: Configurable (default: 300px)
- **Responsive**: Ẩn trên mobile hoặc chuyển thành bottom banner
- **Lazy Load**: Load khi sidebar vào viewport

#### Popup/Modal Ads
- **Overlay**: Dark background với opacity
- **Close Button**: X button ở góc
- **Auto-close**: Sau X seconds
- **Prevent Multiple**: Chỉ hiển thị một lần per session
- **Mobile Friendly**: Full-screen trên mobile

#### Inline Ads
- **Before Video**: Full-width banner
- **After Video**: Full-width hoặc inline với content
- **Between Videos**: Trong navigation section

#### Ad Tracking
- **Click Tracking**: Log clicks (optional analytics)
- **View Tracking**: Log impressions
- **Conversion Tracking**: Track conversions (custom)

### Product Display

#### Product Grid
- **Layout**: CSS Grid hoặc Flexbox
- **Responsive Breakpoints**:
  - Desktop: 3-4 columns
  - Tablet: 2 columns
  - Mobile: 1 column
- **Card Design**: 
  - Image với hover effect
  - Title với link
  - Price với sale highlight
  - Add to cart button
  - Rating stars (nếu có)

#### AJAX Add to Cart
- **Non-blocking**: Không reload page
- **Feedback**: Success/error messages
- **Cart Update**: Update cart count trong header
- **Animation**: Smooth transitions

#### Product Popup
- **Trigger**: Click vào product card
- **Content**: Product details, images, price, add to cart
- **Quick View**: Không cần navigate đến product page

---

## API & Hooks

### Action Hooks

#### Lesson Hooks
```php
// Trước khi hiển thị video
do_action('hm_lesson_before_video', $lesson_id, $lesson);

// Sau khi hiển thị video
do_action('hm_lesson_after_video', $lesson_id, $lesson);

// Khi lesson được view
do_action('hm_lesson_viewed', $lesson_id, $user_id);

// Khi lesson được complete (optional)
do_action('hm_lesson_completed', $lesson_id, $user_id);
```

#### Series Hooks
```php
// Trước khi hiển thị series
do_action('hm_series_before_display', $series_id, $series);

// Sau khi hiển thị series
do_action('hm_series_after_display', $series_id, $series);
```

#### Ad Hooks
```php
// Khi quảng cáo được hiển thị
do_action('hm_lesson_ad_display', $ad_data, $position, $lesson_id);

// Khi quảng cáo được click
do_action('hm_lesson_ad_click', $ad_data, $lesson_id, $user_id);
```

#### Product Hooks
```php
// Khi sản phẩm được hiển thị
do_action('hm_lesson_product_display', $product_ids, $position, $lesson_id);

// Khi sản phẩm được thêm vào cart từ lesson
do_action('hm_lesson_product_added_to_cart', $product_id, $lesson_id, $user_id);
```

### Filter Hooks

#### Access Control Filters
```php
// Filter quyền truy cập
$has_access = apply_filters('hm_lesson_access_check', $has_access, $lesson_id, $user_id);

// Filter redirect URL khi không có quyền
$redirect_url = apply_filters('hm_lesson_access_redirect', $redirect_url, $lesson_id);
```

#### Content Filters
```php
// Filter video URL
$video_url = apply_filters('hm_lesson_video_url', $video_url, $lesson_id);

// Filter video embed code
$embed_code = apply_filters('hm_lesson_video_embed', $embed_code, $lesson_id, $video_url);
```

#### Ad Filters
```php
// Filter danh sách quảng cáo
$ads = apply_filters('hm_lesson_ads', $ads, $lesson_id, $position);

// Filter ad display rules
$display_rules = apply_filters('hm_lesson_ad_display_rules', $display_rules, $ad_data);
```

#### Product Filters
```php
// Filter danh sách sản phẩm
$products = apply_filters('hm_lesson_products', $products, $lesson_id, $position);

// Filter product query args
$query_args = apply_filters('hm_lesson_product_query_args', $query_args, $lesson_id);
```

### REST API Endpoints (Optional)

#### Series Endpoints
```
GET /wp-json/hm/v1/series
GET /wp-json/hm/v1/series/{id}
```

#### Lesson Endpoints
```
GET /wp-json/hm/v1/lessons
GET /wp-json/hm/v1/lessons/{id}
GET /wp-json/hm/v1/series/{series_id}/lessons
```

#### Response Format
```json
{
  "id": 123,
  "title": "Lesson Title",
  "content": "Lesson description",
  "youtube_id": "dQw4w9WgXcQ",
  "youtube_url": "https://youtube.com/watch?v=dQw4w9WgXcQ",
  "series": {
    "id": 456,
    "title": "Series Title",
    "slug": "series-slug"
  },
  "order": 1,
  "access_type": "public",
  "ads": [...],
  "products": [...]
}
```

---

## Security

### Input Validation

#### Sanitization
- **Text Fields**: `sanitize_text_field()`
- **URLs**: `esc_url_raw()`
- **HTML Content**: `wp_kses_post()` hoặc `wp_kses()`
- **Numbers**: `absint()` hoặc `intval()`
- **JSON**: `json_encode()` với validation

#### Validation
- **YouTube URLs**: Validate format và extract ID
- **Product IDs**: Verify tồn tại và user có quyền
- **File Uploads**: Validate MIME type, file size
- **Email**: `is_email()`

### Output Escaping

- **HTML**: `esc_html()`
- **Attributes**: `esc_attr()`
- **URLs**: `esc_url()`
- **JavaScript**: `wp_json_encode()`
- **HTML Content**: `wp_kses_post()`

### Access Control

#### Capability Checks
```php
// Check user capability
if (!current_user_can('manage_hm_lessons')) {
    wp_die(__('You do not have permission to perform this action.'));
}
```

#### Nonce Verification
```php
// Verify nonce
if (!isset($_POST['hm_nonce']) || !wp_verify_nonce($_POST['hm_nonce'], 'hm_action')) {
    wp_die(__('Security check failed.'));
}
```

#### SQL Injection Prevention
- Sử dụng `$wpdb->prepare()` cho tất cả queries
- Sử dụng `WP_Query` thay vì raw SQL khi có thể
- Validate và sanitize input trước khi query

### File Security

- **Direct File Access**: Prevent với `.htaccess` hoặc `__FILE__` check
- **File Uploads**: Validate file type, size, và content
- **Path Traversal**: Validate file paths

---

## Performance

### Caching

#### Transient API
```php
// Cache video metadata
$cache_key = 'hm_video_' . $youtube_id;
$video_data = get_transient($cache_key);
if (false === $video_data) {
    $video_data = hm_fetch_video_metadata($youtube_id);
    set_transient($cache_key, $video_data, HOUR_IN_SECONDS);
}
```

#### Object Cache
- Cache series và lessons queries
- Cache product queries
- Cache ad configurations

#### Browser Caching
- Static assets (CSS, JS, images)
- Set appropriate cache headers
- Versioning assets để bust cache

### Optimization

#### Database Queries
- **Reduce Queries**: Sử dụng `get_post_meta()` với `$single = false` cho multiple meta
- **Query Optimization**: Sử dụng `WP_Query` với proper meta_query
- **Pagination**: Limit số lượng posts per page
- **Lazy Loading**: Load content khi cần

#### Asset Optimization
- **Minification**: Minify CSS và JS
- **Concatenation**: Combine multiple files
- **Defer JavaScript**: Defer non-critical JS
- **Async Loading**: Load ads và products async

#### Image Optimization
- **Lazy Loading**: Load images khi vào viewport
- **Responsive Images**: Sử dụng `srcset` và `sizes`
- **Image Compression**: Optimize image sizes
- **CDN**: Sử dụng CDN cho static assets (optional)

#### Code Optimization
- **Autoloading**: Autoload classes khi cần
- **Conditional Loading**: Chỉ load khi cần
- **Hook Optimization**: Unhook unused hooks

---

## File Structure

```
happy-market-learning/
├── includes/
│   ├── class-hm-activator.php          # Activation hooks
│   ├── class-hm-deactivator.php        # Deactivation hooks
│   ├── class-hm-core.php               # Main plugin class
│   ├── class-hm-loader.php            # Hook loader
│   ├── class-hm-i18n.php               # Internationalization
│   │
│   ├── post-types/
│   │   ├── class-hm-series.php        # Series post type
│   │   └── class-hm-lesson.php        # Lesson post type
│   │
│   ├── admin/
│   │   ├── class-hm-admin.php         # Admin functionality
│   │   ├── class-hm-meta-boxes.php    # Meta boxes
│   │   ├── class-hm-list-tables.php   # Custom list tables
│   │   ├── class-hm-settings.php      # Settings page
│   │   └── class-hm-admin-assets.php  # Admin assets
│   │
│   ├── public/
│   │   ├── class-hm-public.php        # Public functionality
│   │   ├── class-hm-shortcodes.php    # Shortcodes
│   │   ├── class-hm-templates.php     # Template functions
│   │   ├── class-hm-access-control.php # Access control
│   │   └── class-hm-public-assets.php  # Public assets
│   │
│   ├── integrations/
│   │   ├── class-hm-woocommerce.php   # WooCommerce integration
│   │   └── class-hm-membership.php    # Membership plugins integration
│   │
│   └── utils/
│       ├── class-hm-youtube.php        # YouTube utilities
│       ├── class-hm-helpers.php       # Helper functions
│       └── class-hm-ajax.php          # AJAX handlers
│
├── templates/
│   ├── single-hm_lesson.php           # Single lesson template
│   ├── archive-hm_series.php          # Series archive
│   └── archive-hm_lesson.php          # Lesson archive
│
├── assets/
│   ├── css/
│   │   ├── admin.css                  # Admin styles
│   │   └── public.css                 # Public styles
│   ├── js/
│   │   ├── admin.js                   # Admin JavaScript
│   │   └── public.js                  # Public JavaScript
│   └── images/
│       └── icon-128x128.png            # Plugin icon
│
├── languages/
│   └── happy-market-learning.pot      # Translation template
│
├── uninstall.php                       # Uninstall cleanup
├── happy-market-learning.php          # Main plugin file
└── README.md                           # Plugin documentation
```

---

## Implementation Priority

### Phase 1: Core Functionality (Week 1-2)

**Mục tiêu**: Có thể tạo và hiển thị series và lessons cơ bản

- [ ] Tạo plugin structure và main files
- [ ] Implement Custom Post Types (Series, Lesson)
- [ ] Basic admin interface (list tables, edit screens)
- [ ] Meta boxes cơ bản (Series selection, YouTube URL)
- [ ] YouTube URL validation và ID extraction
- [ ] Basic frontend display (single lesson template)
- [ ] Video embedding (YouTube iframe)
- [ ] Series-Lesson relationship
- [ ] Order management (basic)

**Deliverables**:
- Plugin có thể activate
- Có thể tạo series và lessons
- Có thể xem lesson với video

### Phase 2: Quảng cáo và Products (Week 3-4)

**Mục tiêu**: Quản lý và hiển thị quảng cáo, tích hợp WooCommerce

- [ ] Ad management system (meta boxes, repeater fields)
- [ ] Ad display positions (sidebar, popup, inline)
- [ ] Ad display rules và triggers
- [ ] WooCommerce integration (product selector)
- [ ] Product display (grid, cards)
- [ ] AJAX add to cart
- [ ] Multiple display positions cho ads và products
- [ ] Ad tracking (clicks, impressions)

**Deliverables**:
- Có thể thêm quảng cáo vào lessons
- Có thể chọn và hiển thị WooCommerce products
- Quảng cáo và products hiển thị đúng vị trí

### Phase 3: Advanced Features (Week 5-6)

**Mục tiêu**: Access control, shortcodes, templates, customization

- [ ] Access control system (public, login, membership)
- [ ] Membership plugins integration hooks
- [ ] Shortcodes implementation
- [ ] Template system và theme override
- [ ] Navigation (previous/next lesson)
- [ ] Settings page
- [ ] Archive templates
- [ ] Analytics (optional, basic tracking)

**Deliverables**:
- Access control hoạt động
- Tất cả shortcodes hoạt động
- Templates có thể override
- Settings page đầy đủ

### Phase 4: Polish (Week 7-8)

**Mục tiêu**: Optimization, security, documentation, testing

- [ ] Performance optimization (caching, queries)
- [ ] Security hardening (validation, escaping, nonces)
- [ ] Code documentation (PHPDoc)
- [ ] User documentation (README, inline help)
- [ ] Testing (unit tests, integration tests)
- [ ] Bug fixes
- [ ] Responsive design polish
- [ ] Browser compatibility testing

**Deliverables**:
- Plugin production-ready
- Documentation đầy đủ
- Tested và bug-free
- Performance optimized

---

## Hướng dẫn Sử dụng

### Cài đặt Plugin

1. Upload plugin vào `/wp-content/plugins/`
2. Activate plugin từ WordPress admin
3. Cài đặt WooCommerce (nếu muốn tích hợp sản phẩm)

### Tạo Series đầu tiên

1. Vào **HappyMarket Learning > Series**
2. Click **Add New**
3. Nhập tên series
4. Thêm mô tả (optional)
5. Chọn Featured Image
6. Cấu hình Access Type
7. Publish

### Tạo Lesson

1. Vào **HappyMarket Learning > Lessons**
2. Click **Add New**
3. Nhập tên lesson
4. Chọn Series từ dropdown
5. Nhập YouTube URL (ví dụ: `https://www.youtube.com/watch?v=dQw4w9WgXcQ`)
6. Plugin sẽ tự động extract YouTube ID
7. Thêm mô tả (optional)
8. Set Order trong series
9. Publish

### Thêm Quảng cáo

1. Edit lesson
2. Scroll đến meta box **Quảng cáo**
3. Click **Add Ad**
4. Upload hoặc nhập Image URL
5. Nhập Link URL
6. Chọn Position (sidebar, popup, etc.)
7. Cấu hình Display Rules (optional)
8. Save lesson

### Thêm WooCommerce Products

1. Edit lesson
2. Scroll đến meta box **WooCommerce Products**
3. Click **Select Products**
4. Tìm kiếm và chọn sản phẩm
5. Sắp xếp thứ tự (drag & drop)
6. Chọn Position cho sản phẩm
7. Save lesson

### Sử dụng Shortcodes

#### Hiển thị Series
Thêm vào bất kỳ page/post nào:
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

### Cấu hình Settings

1. Vào **Settings > HappyMarket Learning**
2. Cấu hình General Settings:
   - Default Access Type
   - Default Ad Position
   - YouTube API Key (optional)
3. Cấu hình WooCommerce Integration:
   - Enable integration
   - Default product position
   - Product display settings
4. Save Changes

### Template Customization

1. Copy template từ plugin:
   - `wp-content/plugins/happy-market-learning/templates/single-hm_lesson.php`
2. Paste vào theme:
   - `wp-content/themes/{your-theme}/happy-market-learning/single-hm_lesson.php`
3. Chỉnh sửa template theo nhu cầu
4. Theme sẽ tự động sử dụng template từ theme thay vì plugin

### Access Control

#### Public Access
- Mặc định, tất cả lessons là public
- Không cần cấu hình gì

#### Login Required
1. Edit lesson hoặc series
2. Chọn Access Type: **Login Required**
3. Save
4. User chưa đăng nhập sẽ được redirect đến login page

#### Membership Required
1. Cài đặt membership plugin (MemberPress, LearnDash, etc.)
2. Edit lesson hoặc series
3. Chọn Access Type: **Membership Required**
4. Plugin sẽ tự động tích hợp với membership plugin
5. Chỉ user có membership mới xem được

---

## Kết luận

Tài liệu thiết kế này cung cấp một blueprint đầy đủ cho việc phát triển plugin **HappyMarket Learning Manager**. Plugin sẽ cho phép:

- Quản lý series và bài học video một cách có tổ chức
- Tích hợp quảng cáo linh hoạt với nhiều vị trí và quy tắc hiển thị
- Tích hợp WooCommerce để up-sale sản phẩm
- Hỗ trợ nhiều loại quyền truy cập
- Cung cấp shortcodes và templates để dễ dàng tùy chỉnh

Plugin được thiết kế với các nguyên tắc:
- **Modular**: Code được tổ chức thành các modules dễ quản lý
- **Extensible**: Nhiều hooks và filters để mở rộng
- **Secure**: Tuân thủ WordPress security best practices
- **Performant**: Optimized cho performance
- **User-friendly**: Interface dễ sử dụng cho cả admin và end users

