# RoadToSchool

[English](README.md) | **Tiếng Việt**

RoadToSchool là hệ thống quản lý học tập song ngữ dành cho học viên, giảng viên và quản trị viên. Ứng dụng hỗ trợ tìm kiếm khóa học, ghi danh và thanh toán, bài giảng video, bài kiểm tra, theo dõi tiến độ, kiểm duyệt, phân quyền và tương tác thời gian thực. Trải nghiệm Blade nguyên bản được giữ lại trên nền Laravel hiện đại.

## Chức năng chính

| Khu vực | Chức năng |
| --- | --- |
| Học viên | Đăng ký và đăng nhập, tìm kiếm/lọc khóa học, xem hồ sơ giảng viên, sử dụng giỏ hàng và thanh toán, học bài giảng, theo dõi tiến độ, làm quiz, đánh giá, bình luận, nhận thông báo và liên hệ hỗ trợ |
| Giảng viên | Xem dashboard giảng viên, tạo khóa học, thêm bài giảng video và quiz, quản lý nội dung khóa học và xem học viên đã ghi danh |
| Quản trị viên | Quản lý người dùng và giảng viên, quyền truy cập, danh mục, khóa học, duyệt bài giảng, hóa đơn, xếp hạng giảng viên và hội thoại đang chờ |
| Dùng chung | Giao diện Anh/Việt, quy trình đặt lại mật khẩu và xác minh email, giao diện Blade responsive, metadata YouTube, gợi ý khóa học và sự kiện thời gian thực bằng Reverb |

## Kiến trúc

```text
Trình duyệt
  ├─ Blade + Bootstrap + jQuery được build bằng Vite/esbuild
  ├─ HTTP/AJAX ──> Laravel routes ──> Controllers/Requests
  │                                      ├─ Eloquent ──> MySQL
  │                                      └─ Services ──> YouTube / recommendation API
  └─ Laravel Echo <── WebSocket ──> Laravel Reverb
```

Ứng dụng sử dụng Laravel MVC, không phải ứng dụng SPA. Quyền truy cập được kiểm soát bằng xác thực, middleware vai trò/quyền, kiểm tra quyền sở hữu và validation phía máy chủ. Checkout, kích hoạt hóa đơn, duyệt bài giảng, nộp quiz và các thao tác nhiều bản ghi khác sử dụng transaction khi cần đảm bảo tính nhất quán.

## Công nghệ

Các phiên bản dưới đây là baseline local đã được kiểm tra:

| Lớp | Công nghệ |
| --- | --- |
| Backend | PHP 8.5.9, Laravel 13.26.1, Eloquent ORM, Blade |
| Cơ sở dữ liệu | MySQL 8.4; SQLite in-memory cho test tự động |
| Thời gian thực | Laravel Reverb 1.11, Laravel Echo 2.4, client giao thức Pusher |
| Frontend | Bootstrap 5.3, jQuery 3.7, Axios 1.x, Sass, Vite 8.2, esbuild |
| Thư viện UI | Chart.js, DataTables, Summernote, Selectize, jQuery UI, Moment.js |
| Kiểm thử | PHPUnit 13.3 và browser smoke test bằng Playwright Core 1.62 |
| Công cụ | Composer 2.10, Node.js 24, Docker Compose |

## Chạy nhanh bằng Docker

Docker là môi trường phát triển được khuyến nghị và có khả năng tái tạo đồng nhất.

### Yêu cầu

- Docker Engine có plugin Docker Compose
- Git
- Google Chrome trên máy host nếu cần chạy browser smoke test

### Cài đặt lần đầu

```bash
git clone https://github.com/phamhoangtrang/RoadToSchool.git
cd RoadToSchool
git checkout minhle

cp .env.example .env
docker compose -f compose.local.yaml build app reverb
docker compose -f compose.local.yaml run --rm --no-deps app composer install
docker compose -f compose.local.yaml run --rm --no-deps app php artisan key:generate
docker compose -f compose.local.yaml up -d --wait database
docker compose -f compose.local.yaml run --rm --no-deps app php artisan migrate --seed
docker compose -f compose.local.yaml up -d
```

Truy cập <http://localhost:8000> sau khi các container khởi động.

### Các dịch vụ local

| Dịch vụ | Địa chỉ | Mục đích |
| --- | --- | --- |
| Ứng dụng Laravel | <http://localhost:8000> | Ứng dụng web chính |
| Laravel Reverb | `ws://localhost:8080` | Bình luận, thông báo, thảo luận và hội thoại |
| MySQL | `127.0.0.1:3307` | Cơ sở dữ liệu được expose từ container |
| Assets | Container chạy một lần | Chạy `npm ci` và tạo frontend production build |

Ứng dụng kết nối MySQL nội bộ qua `database:3306`. File frontend sinh ra được ghi vào `public/build` và được Git chủ động bỏ qua.

### Tài khoản demo

Seeder local tạo dữ liệu đại diện và các tài khoản dưới đây. Tất cả sử dụng mật khẩu `123456`.

| Vai trò | Email |
| --- | --- |
| Quản trị viên | `admin@roadtoschool.local` |
| Giảng viên | `instructor@roadtoschool.local` |
| Học viên | `student@roadtoschool.local` |

Dữ liệu demo gồm danh mục, ba khóa học đã duyệt, bài giảng, một quiz, tiến độ học viên, sản phẩm trong giỏ, bình luận và các quyền mặc định.

### Khởi động và dừng

```bash
# Khởi động hoặc build lại toàn bộ môi trường
docker compose -f compose.local.yaml up -d --build

# Kiểm tra trạng thái
docker compose -f compose.local.yaml ps

# Theo dõi log ứng dụng và realtime
docker compose -f compose.local.yaml logs -f app reverb

# Dừng container nhưng giữ volume MySQL
docker compose -f compose.local.yaml down
```

Không thêm `-v` vào `docker compose down` trừ khi bạn thực sự muốn xóa cả dữ liệu MySQL local.

## Cập nhật source đã clone

```bash
git checkout minhle
git pull --ff-only origin minhle
docker compose -f compose.local.yaml build app reverb
docker compose -f compose.local.yaml run --rm --no-deps app composer install
docker compose -f compose.local.yaml run --rm --no-deps assets
docker compose -f compose.local.yaml up -d --wait database
docker compose -f compose.local.yaml run --rm --no-deps app php artisan migrate
docker compose -f compose.local.yaml up -d
```

## Các lệnh phát triển

```bash
# Chạy Artisan hoặc Composer trong container ứng dụng
docker compose -f compose.local.yaml exec -T app php artisan about
docker compose -f compose.local.yaml exec -T app php artisan route:list
docker compose -f compose.local.yaml exec -T app composer audit

# Cài lại frontend dependency theo lockfile và production build
docker compose -f compose.local.yaml run --rm --no-deps assets

# Audit toàn bộ npm dependency
docker compose -f compose.local.yaml run --rm --no-deps assets npm audit

# Xóa cache Laravel khi phát triển
docker compose -f compose.local.yaml exec -T app php artisan optimize:clear
```

Để frontend tự reload trên máy host, cài Node.js 24 rồi chạy:

```bash
npm ci
npm run dev
```

Pipeline production build các entry chính của Vite cùng hai bundle tương thích:

- `resources/sass/app.scss` và `resources/sass/admin.scss`
- `resources/js/app.js`
- `resources/js/legacy.js`
- `resources/js/admin.js`

## Kiểm thử và kiểm tra chất lượng

### Bộ test PHP

```bash
docker compose -f compose.local.yaml exec -T app php artisan test
```

Baseline hiện tại có 49 test pass với 251 assertions. Phạm vi gồm bảo mật tài khoản, quyền route, quản trị, checkout và hóa đơn, khóa học và bài giảng, tương tác, thông báo, đánh giá, quiz, seeding, xử lý recommendation và metadata YouTube.

Test sử dụng SQLite in-memory và không thay đổi cơ sở dữ liệu MySQL trong Docker.

### Browser smoke test

Cài Node.js dependency trên host, bảo đảm Google Chrome khả dụng, khởi động ứng dụng rồi chạy:

```bash
npm ci
npm run test:browser
```

Smoke test đăng nhập lần lượt bằng tài khoản quản trị viên, giảng viên và học viên; truy cập các trang chính của từng vai trò; báo lỗi trình duyệt/HTTP; và lưu ảnh chụp vào `/tmp`. Có thể ghi đè cấu hình:

```bash
APP_URL=http://127.0.0.1:8000 \
CHROME_PATH=/usr/bin/google-chrome \
npm run test:browser
```

## Cấu hình môi trường

Sao chép `.env.example` thành `.env`. Không commit file `.env` đã điền cấu hình.

| Biến | Ý nghĩa |
| --- | --- |
| `APP_*` | URL, môi trường, ngôn ngữ, múi giờ, debug mode và khóa mã hóa của ứng dụng |
| `DB_*` | Host, port, tên database và thông tin đăng nhập MySQL |
| `BROADCAST_CONNECTION` | Đặt là `reverb` trong môi trường local đi kèm |
| `REVERB_*` | Credential ứng dụng Reverb phía server và cấu hình kết nối nội bộ |
| `VITE_REVERB_*` | Host, port, scheme và public app key của Reverb dành cho trình duyệt |
| `MAIL_*` | Kênh gửi mail; môi trường local ghi mail vào log ứng dụng |
| `RECOMMENDATION_*` | Endpoint gợi ý khóa học tùy chọn và timeout |

Sau khi đổi biến `VITE_*`, cần build lại frontend assets.

### Sự kiện thời gian thực

Reverb là broadcaster mặc định. Compose sử dụng `reverb:8080` cho kết nối phía server và `localhost:8080` cho trình duyệt. Nếu truy cập ứng dụng từ thiết bị khác hoặc deploy sau proxy, hãy cập nhật host, port và scheme trong `VITE_REVERB_*`, sau đó build lại assets.

### Dịch vụ recommendation

Recommendation được tắt an toàn khi `RECOMMENDATION_ENDPOINT` để trống. Khi bật, RoadToSchool gửi POST request dạng `application/x-www-form-urlencoded` gồm:

```text
appId=<application id đã cấu hình>
userId=<id người dùng đã xác thực>
count=<số kết quả tối đa>
```

Dịch vụ cần trả JSON có key là user ID, ví dụ:

```json
{
  "3": [1, 2, 5]
}
```

ID không hợp lệ, ID trùng, timeout và dịch vụ recommendation không khả dụng đều được xử lý mà không làm hỏng trang khóa học.

### Tích hợp YouTube

Giảng viên có thể nhập URL YouTube dạng watch, link rút gọn, embed, Shorts và live. Ứng dụng xác minh host/video ID, lấy title/description/duration và render video qua domain tăng cường riêng tư `youtube-nocookie.com`.

## Cơ sở dữ liệu và dữ liệu demo

Áp dụng migration mới mà không xóa dữ liệu:

```bash
docker compose -f compose.local.yaml exec -T app php artisan migrate
```

Tạo dữ liệu demo trên database mới:

```bash
docker compose -f compose.local.yaml exec -T app php artisan db:seed
```

Chủ động tạo lại database local:

```bash
docker compose -f compose.local.yaml exec -T app php artisan migrate:fresh --seed
```

`migrate:fresh` xóa mọi bảng cùng toàn bộ dữ liệu hiện có. Chỉ sử dụng với database local có thể bỏ đi.

## Cấu trúc dự án

```text
app/
  Events/                 Sự kiện broadcast realtime
  Http/Controllers/       Luồng admin, instructor, user và authentication
  Http/Middleware/        Xác thực và thực thi quyền
  Http/Requests/          Validation và authorization đầu vào
  Models/                 Model miền nghiệp vụ bằng Eloquent
  Services/               Tích hợp recommendation và YouTube
database/
  migrations/             Lịch sử schema cơ sở dữ liệu
  seeds/                  Seeder nền và demo local
resources/
  js/                     Entry JavaScript cho Vite và compatibility bundle
  lang/en, lang/vi/       Bản dịch tiếng Anh và tiếng Việt
  sass/                   Style ứng dụng và trang quản trị
  views/                  Blade template
routes/                   Route web, API, channel và console
tests/
  Feature, Unit/          Phạm vi PHPUnit
  browser/                Hành trình smoke test bằng Playwright
compose.local.yaml        Các dịch vụ local có thể tái tạo
Dockerfile.local          Runtime PHP 8.5 cho local
```

## Lưu ý bảo mật và tương thích

- Danh tính người dùng, giá tiền, tổng số câu quiz, quyền sở hữu notification và redirect target được xác định trên server, không tin dữ liệu do client gửi lên.
- Các thao tác với khóa học, bài giảng, giỏ hàng, hồ sơ, notification, conversation và quiz đều kiểm tra quyền sở hữu hoặc trạng thái ghi danh.
- Ảnh upload và URL YouTube được kiểm tra trước khi sử dụng; nội dung realtime do người dùng tạo được escape trước khi render.
- Các thao tác nhạy cảm trên nhiều bản ghi sử dụng transaction và có tính idempotent khi request có thể bị lặp.
- jQuery được giữ ở 3.7 vì các plugin của template gốc chưa tương thích với jQuery 4.
- Guzzle được giữ ở bản 7.x tương thích mới nhất vì Guzzle 8 yêu cầu major PSR xung đột với dependency graph Laravel/Reverb hiện tại.

## Triển khai production

`compose.local.yaml` và `Dockerfile.local` dành cho phát triển local, không phải cấu hình deploy production. Bản production tối thiểu cần:

1. Sử dụng secret và credential database riêng cho production.
2. Đặt `APP_ENV=production`, `APP_DEBUG=false` và `APP_URL` công khai.
3. Cài PHP dependency bằng `composer install --no-dev --optimize-autoloader`.
4. Build assets bằng `npm ci --ignore-scripts && npm run build`.
5. Chạy `php artisan migrate --force` trong một quy trình release có kiểm soát.
6. Cache config, route, event và view bằng `php artisan optimize`.
7. Phục vụ Laravel bằng web server và process manager dành cho production.
8. Chạy và giám sát Reverb riêng, có TLS termination cho WebSocket công khai.
9. Cấu hình mail transport thật, storage bền vững, backup, monitoring và lưu log.

Queue hiện sử dụng driver `sync`; cần bổ sung và giám sát queue backend trước khi chuyển tác vụ nặng sang background job.

## Quy trình phát triển

Phần hiện đại hóa được duy trì trên nhánh `minhle`. Hãy giữ mỗi commit tập trung vào một thay đổi, chạy các test PHP/browser phù hợp trước khi push và không commit `.env`, `vendor`, `node_modules` hoặc file sinh ra trong `public/build`.
