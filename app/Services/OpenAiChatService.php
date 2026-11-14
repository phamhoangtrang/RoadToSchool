<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiChatService
{
    protected $geminiApiKey;
    protected $geminiModel;
    protected $openAiApiKey;
    protected $openAiModel;

    public function __construct()
    {
        $this->geminiApiKey = config('services.gemini.api_key');
        $this->geminiModel = config('services.gemini.model', 'gemini-1.5-flash');
        $this->openAiApiKey = config('services.openai.api_key');
        $this->openAiModel = config('services.openai.model', 'gpt-4o-mini');
    }

    /**
     * Tạo câu trả lời thông minh từ AI cho câu hỏi của người dùng
     */
    public function reply($userMessage, array $recentMessages = [], $currentUser = null)
    {
        // 1. Ưu tiên hàng đầu: Google Gemini API (Miễn phí, cực thông minh, tiếng Việt xuất sắc)
        if (!empty($this->geminiApiKey) && trim($this->geminiApiKey) !== '') {
            try {
                $geminiReply = $this->callGeminiApi($userMessage, $recentMessages, $currentUser);
                if (!empty($geminiReply)) {
                    return $geminiReply;
                }
            } catch (\Exception $e) {
                Log::error('Gemini API Error: ' . $e->getMessage());
            }
        }

        // 2. Kế tiếp: OpenAI API (nếu có key)
        if (!empty($this->openAiApiKey) && trim($this->openAiApiKey) !== '') {
            try {
                $openAiReply = $this->callOpenAiApi($userMessage, $recentMessages, $currentUser);
                if (!empty($openAiReply)) {
                    return $openAiReply;
                }
            } catch (\Exception $e) {
                Log::error('OpenAI API Error: ' . $e->getMessage());
            }
        }

        // 3. Fallback cục bộ khi chưa điền API Key
        return $this->smartLocalReply($userMessage, $currentUser);
    }

    /**
     * Gọi Google Gemini API chính hãng
     */
    protected function callGeminiApi($userMessage, array $recentMessages, $currentUser)
    {
        $systemInstruction = $this->buildSystemContext($currentUser, $userMessage);
        $modelName = $this->geminiModel ?: 'gemini-3.1-flash-lite';
        // API key gửi qua header (không đặt trong URL để không bị ghi vào log)
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent";

        $contents = [];

        // Chuẩn hóa lịch sử hội thoại đảm bảo chuẩn Google Generative Language API:
        // 1. Lượt đầu tiên phải là role 'user'
        // 2. Các lượt phải luân phiên 'user' -> 'model' -> 'user' -> 'model'
        $historyTurns = [];
        $lastRole = null;
        foreach (array_slice($recentMessages, -6) as $msg) {
            $role = ($msg['is_admin'] ?? false) ? 'model' : 'user';
            $text = trim($msg['content'] ?? '');
            if ($text === '') continue;

            if ($lastRole === null) {
                if ($role !== 'user') continue;
                $historyTurns[] = ['role' => 'user', 'parts' => [['text' => $text]]];
                $lastRole = 'user';
            } elseif ($lastRole === $role) {
                $lastIdx = count($historyTurns) - 1;
                $historyTurns[$lastIdx]['parts'][0]['text'] .= "\n" . $text;
            } else {
                $historyTurns[] = ['role' => $role, 'parts' => [['text' => $text]]];
                $lastRole = $role;
            }
        }

        // Nếu lượt cuối trong lịch sử là 'user', bỏ đi để lượt hỏi mới của user không bị trùng role
        if ($lastRole === 'user') {
            array_pop($historyTurns);
        }

        $contents = $historyTurns;

        // Lưu ý: Dòng model Gemma không hỗ trợ trường system_instruction riêng trong API v1beta (gây lỗi 500)
        // Cần gộp trực tiếp chỉ dẫn hệ thống vào prompt của lượt hỏi
        $isGemma = str_contains($modelName, 'gemma');

        if ($isGemma) {
            $fullPrompt = "BẠN LÀ TRỢ LÝ AI ROADTOSCHOOL. HÃY TUÂN THỦ CÁC QUY TẮC VÀ DỮ LIỆU SAU:\n" .
                $systemInstruction . "\n\n" .
                "CÂU HỎI CỦA HỌC VIÊN:\n" . $userMessage;

            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $fullPrompt]]
            ];

            $payload = [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1000,
                ]
            ];
        } else {
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $userMessage]]
            ];

            $payload = [
                'system_instruction' => [
                    'parts' => [
                        ['text' => $systemInstruction]
                    ]
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1000,
                ]
            ];
        }

        $response = Http::withoutVerifying()
            ->withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $this->geminiApiKey,
            ])
            ->connectTimeout(5)
            ->timeout(12)
            ->post($url, $payload);

        if ($response->successful()) {
            $data = $response->json();
            $candidates = $data['candidates'] ?? [];
            if (!empty($candidates)) {
                $parts = $candidates[0]['content']['parts'] ?? [];
                // Lấy phần text thực tế (bỏ qua phần thought nếu có)
                $finalText = '';
                foreach ($parts as $p) {
                    if (empty($p['thought']) && !empty($p['text'])) {
                        $finalText = trim($p['text']);
                        break;
                    }
                }
                if ($finalText === '' && !empty($parts)) {
                    $finalText = trim(end($parts)['text'] ?? '');
                }
                if (!empty($finalText)) {
                    return $finalText;
                }
            }
        }

        Log::warning('Gemini API Response: ' . $response->body());
        return null;
    }

    /**
     * Gọi OpenAI Chat Completions API
     */
    protected function callOpenAiApi($userMessage, array $recentMessages, $currentUser)
    {
        $systemContext = $this->buildSystemContext($currentUser, $userMessage);

        $messages = [
            [
                'role' => 'system',
                'content' => $systemContext
            ]
        ];

        foreach (array_slice($recentMessages, -6) as $msg) {
            $role = ($msg['is_admin'] ?? false) ? 'assistant' : 'user';
            $messages[] = [
                'role' => $role,
                'content' => $msg['content'] ?? ''
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $this->openAiApiKey,
            'Content-Type' => 'application/json',
        ])->timeout(25)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $this->openAiModel,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 800,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return trim($data['choices'][0]['message']['content'] ?? '');
        }

        Log::warning('OpenAI API Response failed: ' . $response->body());
        return null;
    }

    /**
     * Trích xuất các từ khóa tìm kiếm khóa học phù hợp với câu hỏi của học viên
     */
    protected function extractKeywords($message)
    {
        $msg = mb_strtolower($message, 'UTF-8');
        $keywords = [];

        if (preg_match('/\b(c\+\+|cpp)\b/u', $msg)) {
            $keywords = array_merge($keywords, ['c++', 'cpp', 'hướng đối tượng', 'oop']);
        } elseif (preg_match('/(^|\s|[,\.!?])c(\s|[,\.!?]|$)/u', $msg) || preg_match('/(ngôn ngữ c|lập trình c|khóa học c|nâng cao c|c nâng cao)/u', $msg)) {
            $keywords = array_merge($keywords, ['c nâng cao', 'ngôn ngữ lập trình', 'lập trình c', 'c']);
        }

        if (preg_match('/(nâng cao|chuyên sâu|master)/u', $msg)) {
            $keywords[] = 'nâng cao';
        }

        if (preg_match('/(python|ai|trí tuệ nhân tạo|machine learning|data|dữ liệu)/u', $msg)) {
            $keywords = array_merge($keywords, ['python', 'ai', 'trí tuệ nhân tạo', 'dữ liệu', 'máy học']);
        }

        if (preg_match('/(web|frontend|backend|php|laravel|react|javascript|js|html|css)/u', $msg)) {
            $keywords = array_merge($keywords, ['web', 'php', 'laravel', 'react', 'javascript', 'html']);
        }

        if (preg_match('/(thiết kế|design|ui|ux|figma|photoshop)/u', $msg)) {
            $keywords = array_merge($keywords, ['thiết kế', 'figma', 'photoshop', 'ui/ux']);
        }

        if (preg_match('/(database|cơ sở dữ liệu|sql|mysql)/u', $msg)) {
            $keywords = array_merge($keywords, ['cơ sở dữ liệu', 'mysql', 'sql']);
        }

        if (preg_match('/(mobile|di động|flutter|android|ios)/u', $msg)) {
            $keywords = array_merge($keywords, ['flutter', 'mobile', 'di động']);
        }

        if (preg_match('/(game|unity)/u', $msg)) {
            $keywords = array_merge($keywords, ['game', 'unity']);
        }

        return array_unique($keywords);
    }

    /**
     * Tạo System Context phong phú, chính xác từ Database thực tế của RoadToSchool
     */
    protected function buildSystemContext($currentUser, $userMessage = '')
    {
        $rawName = $currentUser ? $currentUser->name : 'bạn';
        $userName = (in_array($rawName, ['Quản trị viên Hệ thống', 'admin', 'Admin', 'super_admin']) || mb_stripos($rawName, 'Quản trị') !== false) ? 'bạn' : $rawName;

        // 1. Tìm các khóa học phù hợp với câu hỏi của học viên
        $keywords = $this->extractKeywords($userMessage);
        $matchedCourses = collect();

        if (!empty($keywords)) {
            $matchedCourses = Course::where('is_accepted', 1)
                ->with(['user', 'category'])
                ->where(function ($q) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $q->orWhere('title', 'like', "%{$kw}%");
                    }
                })
                ->take(12)
                ->get();
        }

        // 2. Lấy thêm top các khóa học có học phí ưu đãi tốt nhất (rẻ nhất)
        $cheapestCourses = Course::where('is_accepted', 1)
            ->with(['user', 'category'])
            ->where('promotion_price', '>', 0)
            ->orderBy('promotion_price', 'asc')
            ->take(6)
            ->get();

        // 3. Lấy thêm top các khóa học bán chạy / tiêu biểu
        $popularCourses = Course::where('is_accepted', 1)
            ->with(['user', 'category'])
            ->orderBy('seller', 'desc')
            ->take(6)
            ->get();

        // Gộp lại và loại bỏ trùng lặp
        $allCourses = $matchedCourses->merge($cheapestCourses)->merge($popularCourses)->unique('id');

        $courseListText = "";
        foreach ($allCourses as $c) {
            $priceText = $c->origin_price == 0 ? "Miễn phí" : number_format($c->origin_price, 0, ',', '.') . " VNĐ";
            if ($c->promotion_price > 0 && $c->promotion_price < $c->origin_price) {
                $priceText = number_format($c->promotion_price, 0, ',', '.') . " VNĐ (Giá gốc: " . number_format($c->origin_price, 0, ',', '.') . " VNĐ)";
            }
            $instructor = $c->user ? $c->user->name : "Đội ngũ R2S";
            $category = $c->category ? ($c->category->vi_title ?: $c->category->title) : "Tổng hợp";
            $courseListText .= "- Khóa học [ID: {$c->id}]: \"{$c->title}\" | Danh mục: {$category} | Giảng viên: {$instructor} | Học phí: {$priceText}\n";
        }

        $bank = config('services.vietqr.bank_id', 'MB');
        $stk = config('services.vietqr.account_no', '44401287290165');
        $name = config('services.vietqr.account_name', 'PHAM HOANG TRANG');

        return <<<EOT
BẠN LÀ TRỢ LÝ AI CHUYÊN NGHIỆP CỦA NỀN TẢNG HỌC LẬP TRÌNH TRỰC TUYẾN ROADTOSCHOOL (R2S).
Nhiệm vụ của bạn là tư vấn tận tình, chuyên nghiệp, chính xác 100% dựa trên dữ liệu hệ thống thực tế bên dưới.

QUY TẮC GIAO TIẾP VỚI HỌC VIÊN:
1. Luôn xưng hô "Em" và gọi học viên là "bạn" (hoặc tên "{$userName}"). Tuyệt đối không gọi học viên là "Quản trị viên Hệ thống" hay "Admin".
2. Giọng điệu thân thiện, rõ ràng, mạch lạc, dùng bullet point và in đậm các thông tin quan trọng.
3. TUYỆT ĐỐI CHỈ SỬ DỤNG THÔNG TIN KHÓA HỌC VÀ HỌC PHÍ THỰC TẾ TRONG DANH SÁCH BÊN DƯỚI. Không tự ý bịa đặt tên khóa học hay học phí giả.
4. Khi liệt kê khóa học, đánh số thứ tự 1, 2, 3... từ đầu danh sách. TUYỆT ĐỐI không nêu mã ID nội bộ của khóa học trong câu trả lời.

CẨM NANG NGHIỆP VỤ 4 CHỦ ĐỀ CHÍNH:

1. THÔNG TIN ĐƠN HÀNG & KÍCH HOẠT:
- Định dạng mã đơn hàng: Bắt đầu bằng tiền tố "RTS" kèm ID đơn (Ví dụ: RTS12, RTS45).
- Tự động kích hoạt: Sau khi học viên hoàn tất chuyển khoản VietQR, hệ thống SePay tự động kiểm tra và kích hoạt đơn hàng trong 1-3 phút.
- Cách vào học sau khi mua: Bấm vào Avatar tài khoản ở góc trên bên phải -> Chọn "Khóa học của tôi" (hoặc truy cập /courses/{userId}/my_course) để vào học ngay tất cả bài giảng và bài tập.
- Hỗ trợ khi chuyển khoản xong chưa thấy khóa học: Nếu sau 3-5 phút chưa thấy khóa học trong "Khóa học của tôi", hướng dẫn học viên gửi Mã đơn hàng (RTS...) hoặc ảnh chụp bill chuyển khoản vào khung chat này để Admin kích hoạt thủ công ngay.

2. GIÁ, HỌC PHÍ & CHƯƠNG TRÌNH KHUYẾN MÃI:
- Khung học phí: Cực kỳ ưu đãi cho sinh viên, chỉ dao động từ 100.000 VNĐ đến 1.890.000 VNĐ.
- Top các khóa học học phí rẻ nhất:
  * "Khóa học C nâng cao": 100.000 VNĐ (giá gốc: 150.000 VNĐ, giảng viên ThS. Huỳnh Thanh Mai).
  * "Trí tuệ Nhân tạo & Machine Learning": 200.000 VNĐ (giá gốc: 300.000 VNĐ).
  * "HTML5, CSS3 và Responsive Web Design": 249.000 VNĐ (giá gốc: 399.000 VNĐ).
  * "Git & GitHub - Quản lý mã nguồn": 299.000 VNĐ (giá gốc: 499.000 VNĐ).
- Khuyến mãi: Hệ thống đang có chương trình giảm giá từ 30% đến 60% khi thanh toán trực tuyến.
- Học thử: Học viên hoàn toàn có thể xem trước các bài giảng mở miễn phí (Preview) ở mỗi khóa học trước khi quyết định mua.

3. KHÓA HỌC & LỘ TRÌNH ĐÀO TẠO:
- Khi học viên hỏi về C, C++ hoặc "các khóa nâng cao C":
  * Hãy liệt kê đầy đủ các khóa học C & Ngôn ngữ lập trình trong danh sách (đặc biệt: "Khóa học C nâng cao" - 100.000đ do ThS. Huỳnh Thanh Mai giảng dạy; "Bí quyết và Kỹ năng Thực hành Ngôn ngữ Lập trình Đỉnh cao" - 470.000đ; "Cẩm nang Toàn diện về Ngôn ngữ Lập trình từ A đến Z" - 600.000đ; "Khóa học Ngôn ngữ Lập trình từ Cơ bản đến Nâng cao" - 1.710.000đ).
  * Đưa ra lộ trình học C nâng cao rõ ràng: Giai đoạn 1 (Cơ bản & Cú pháp), Giai đoạn 2 (Con trỏ, Quản lý bộ nhớ Heap/Stack, Struct), Giai đoạn 3 (Cấu trúc dữ liệu giải thuật nâng cao & tối ưu hiệu năng).
- Với các chủ đề khác (Python/AI, Web Fullstack, UI/UX, MySQL...): Giới thiệu các khóa học thực tế kèm theo lộ trình các giai đoạn cụ thể.

4. PHƯƠNG THỨC THANH TOÁN (VIETQR):
- RoadToSchool KHÔNG hỗ trợ thanh toán bằng TIỀN MẶT hoặc COD (vì đây là nền tảng đào tạo online 100%).
- Hỗ trợ thanh toán trực tuyến an toàn qua Quét mã VietQR (chuyển khoản 24/7):
  * Ngân hàng: {$bank} (Ngân hàng Quân Đội)
  * Số tài khoản: {$stk}
  * Chủ tài khoản: {$name}
  * Nội dung chuyển khoản: RTS <Mã đơn hàng> (hoặc quét mã VietQR tự động điền sẵn).
- Các bước: Thêm khóa học vào Giỏ hàng -> Bấm "Thanh toán" -> Dùng App ngân hàng quét mã QR -> Khóa học được kích hoạt sau 1-3 phút.

DANH SÁCH KHÓA HỌC THỰC TẾ ĐANG CÓ TRÊN HỆ THỐNG ROADTOSCHOOL:
{$courseListText}
EOT;
    }

    /**
     * Bộ tư vấn chuyên sâu, thông minh về Đơn hàng, Giá cả, Khóa học và Thanh toán
     * (Hoạt động độc lập và làm fallback vững chắc khi không dùng AI API)
     */
    protected function smartLocalReply($userMessage, $currentUser)
    {
        $rawMsg = trim($userMessage);
        $msg = mb_strtolower($rawMsg, 'UTF-8');
        $rawName = $currentUser ? $currentUser->name : 'bạn';
        $userName = (in_array($rawName, ['Quản trị viên Hệ thống', 'admin', 'Admin', 'super_admin']) || mb_stripos($rawName, 'Quản trị') !== false) ? 'bạn' : $rawName;

        $bank = config('services.vietqr.bank_id', 'MB');
        $stk = config('services.vietqr.account_no', '44401287290165');
        $name = config('services.vietqr.account_name', 'PHAM HOANG TRANG');

        // 1. Chào hỏi ban đầu
        if (preg_match('/^(chào|hello|hi|alo|hé lô|cho em hỏi|xin chào)/u', $msg) && mb_strlen($msg) < 30) {
            return "Dạ chào {$userName}! Em là Trợ lý AI của RoadToSchool. Em rất sẵn lòng hỗ trợ bạn hôm nay:\n\n" .
                "• 📚 **Khóa học & Lộ trình**: Tư vấn học lập trình C/C++, Python, Web Fullstack, UI/UX, v.v.\n" .
                "• 💰 **Học phí & Giá cả**: Báo giá chi tiết, các khóa học ưu đãi rẻ nhất chỉ từ 100.000 VNĐ.\n" .
                "• 💳 **Thanh toán VietQR**: Hướng dẫn quét mã QR, thanh toán tự động qua SePay trong 1-3 phút.\n" .
                "• 📦 **Thông tin đơn hàng**: Tra cứu mã đơn RTS, hướng dẫn vào mục 'Khóa học của tôi'.\n\n" .
                "Bạn đang quan tâm đến nội dung nào để em hỗ trợ chi tiết nhé ạ?";
        }

        // 2. THÔNG TIN ĐƠN HÀNG & KÍCH HOẠT & VÀO HỌC
        if (preg_match('/(đơn hàng|mã đơn|hóa đơn|vào học ở đâu|khóa học của tôi|chưa kích hoạt|chưa vào học|chuyển khoản rồi mà chưa|chuyển khoản xong|kích hoạt khóa học|lịch sử đơn|rts)/u', $msg)) {
            return "Dạ về **Thông tin đơn hàng & Kích hoạt khóa học** trên RoadToSchool, em xin hướng dẫn cụ thể như sau ạ:\n\n" .
                "1. **Mã đơn hàng**: Mỗi đơn hàng có mã định dạng **RTS** + Mã số (Ví dụ: `RTS123`, `RTS456`). Bạn có thể xem trên trang thanh toán hoặc trong chi tiết đơn hàng.\n" .
                "2. **Thời gian kích hoạt**: Hệ thống thanh toán SePay sẽ tự động đối soát và kích hoạt đơn hàng trong vòng **1 - 3 phút** ngay sau khi bạn chuyển khoản thành công.\n" .
                "3. **Cách vào học ngay sau khi mua**:\n" .
                "   • Bước 1: Nhấn vào **Ảnh đại diện (Avatar)** ở góc trên bên phải màn hình.\n" .
                "   • Bước 2: Chọn mục **'Khóa học của tôi'** (hoặc truy cập `/courses/{id}/my_course`).\n" .
                "   • Bước 3: Nhấp vào khóa học đã kích hoạt để bắt đầu xem video bài giảng và làm bài tập trắc nghiệm.\n" .
                "4. **Hỗ trợ khi chưa vào học được**: Nếu sau 3 - 5 phút chuyển khoản mà bạn chưa thấy khóa học trong 'Khóa học của tôi', bạn chỉ cần **nhắn mã đơn hàng (RTS...) hoặc gửi ảnh chụp biên lai chuyển khoản** ngay trong khung chat này. Quản trị viên trực ban sẽ kiểm tra và kích hoạt thủ công cho bạn ngay lập tức ạ!";
        }

        // 3. THANH TOÁN & VIETQR & TIỀN MẶT
        if (preg_match('/(tiền mặt|cod|thanh toán trực tiếp)/u', $msg)) {
            return "Dạ chào {$userName}, hiện tại hệ thống **RoadToSchool chưa hỗ trợ thanh toán bằng tiền mặt** hay ship COD ạ, do đây là nền tảng đào tạo lập trình trực tuyến 100%.\n\n" .
                "Để đăng ký và vào học ngay, bạn có thể thực hiện thanh toán online cực kỳ nhanh chóng và an toàn qua **Chuyển khoản Ngân hàng (Mã VietQR)**:\n" .
                "1. Chọn khóa học -> Bấm **'Mua ngay'** hoặc thêm vào Giỏ hàng -> Chọn **'Thanh toán'**.\n" .
                "2. Mở App ngân hàng bất kỳ và quét mã VietQR hiển thị trên màn hình:\n" .
                "   • Ngân hàng: **{$bank}** (Ngân hàng Quân Đội)\n" .
                "   • Số tài khoản: **{$stk}**\n" .
                "   • Chủ tài khoản: **{$name}**\n" .
                "3. Sau khi bạn chuyển khoản thành công, hệ thống SePay sẽ tự động kích hoạt khóa học vào mục **'Khóa học của tôi'** chỉ sau **1 - 3 phút** thôi ạ!";
        }

        if (preg_match('/(thanh toán|chuyển khoản|quét mã|vietqr|sepay|ngân hàng|stk|số tài khoản|mb bank|nạp tiền|cách mua)/u', $msg)) {
            return "Dạ quy trình thanh toán trên RoadToSchool hoàn toàn tự động và diễn ra trong 3 bước rất nhanh chóng:\n\n" .
                "1. **Chọn khóa học**: Nhấn **'Mua ngay'** hoặc thêm vào Giỏ hàng -> Chọn **'Thanh toán'**.\n" .
                "2. **Quét mã VietQR**: Dùng App ngân hàng bất kỳ quét mã QR hiển thị trên màn hình:\n" .
                "   • Ngân hàng: **{$bank}** (Ngân hàng Quân Đội)\n" .
                "   • Số tài khoản: **{$stk}**\n" .
                "   • Chủ tài khoản: **{$name}**\n" .
                "   • Nội dung chuyển khoản: Cú pháp `RTS <Mã đơn hàng>` (sẽ được tự động điền sẵn khi bạn quét mã QR).\n" .
                "3. **Tự động kích hoạt**: Hệ thống SePay sẽ tự động đối soát và mở khóa học vào mục **'Khóa học của tôi'** chỉ sau **1 - 3 phút** mà bạn không cần phải chờ duyệt thủ công ạ!\n\n" .
                "Nếu bạn gặp bất kỳ trở ngại nào trong quá trình thanh toán, cứ nhắn ngay tại đây để em hỗ trợ nhé!";
        }

        // 4. GIÁ CẢ & HỌC PHÍ & KHUYẾN MÃI & RẺ NHẤT
        if (preg_match('/(rẻ nhất|khóa học rẻ|bao nhiêu tiền|học phí bao nhiêu|giá bao nhiêu|khoảng bao nhiêu|khuyến mãi|giảm giá|ưu đãi|sale|học thử|miễn phí|free)/u', $msg) && !preg_match('/(c|c\+\+|python|web|java)/u', $msg)) {
            return "Dạ về chính sách **Học phí & Chương trình Ưu đãi** tại RoadToSchool:\n\n" .
                "• **Khung học phí chung**: Các khóa học tại RoadToSchool có mức học phí rất ưu đãi cho sinh viên và người đi làm, dao động từ **100.000 VNĐ** đến **1.890.000 VNĐ** tùy theo lộ trình và thời lượng.\n" .
                "• **Top các khóa học học phí tiết kiệm nhất hiện nay**:\n" .
                "  1. **Khóa học C nâng cao**: Chỉ **100.000 VNĐ** (giá gốc: 150.000 VNĐ) - Giảng viên ThS. Huỳnh Thanh Mai.\n" .
                "  2. **Trí tuệ Nhân tạo & Machine Learning**: **200.000 VNĐ** (giá gốc: 300.000 VNĐ).\n" .
                "  3. **HTML5, CSS3 và Responsive Web Design**: **249.000 VNĐ** (giá gốc: 399.000 VNĐ).\n" .
                "  4. **Git & GitHub - Quản lý mã nguồn**: **299.000 VNĐ** (giá gốc: 499.000 VNĐ).\n" .
                "• **Chương trình khuyến mãi**: Hệ thống đang áp dụng giảm giá từ **30% đến 60%** khi thanh toán trực tuyến.\n" .
                "• **Chính sách học thử**: Ở mỗi khóa học, bạn hoàn toàn có thể xem trước các video bài giảng mở công khai (Preview) trước khi quyết định đăng ký ạ!\n\n" .
                "Bạn muốn tìm hiểu học phí của môn học hoặc công nghệ cụ thể nào (C/C++, Python, Web, Mobile...)?";
        }

        // 5. GẶP ADMIN / HỖ TRỢ KỸ THUẬT / KHIẾU NẠI
        if (preg_match('/(gặp admin|quản trị|người thật|khiếu nại|hoàn tiền|lỗi|sai tiền|hotline|tổng đài)/u', $msg)) {
            return "Dạ em đã ghi nhận yêu cầu của {$userName}. Hệ thống đã gửi thông báo đến Quản trị viên trực ban. Bạn vui lòng để lại Số điện thoại hoặc mô tả chi tiết vấn đề gặp phải, Admin người thật sẽ tiếp nhận và nhắn tin hỗ trợ trực tiếp với bạn ngay trong khung chat này trong ít phút tới nhé!";
        }

        // 6. TƯ VẤN KHÓA HỌC & LỘ TRÌNH CHI TIẾT
        // Lấy danh sách khóa học thực tế trên sàn
        $allCourses = Course::with(['user', 'category'])
            ->where('is_accepted', 1)
            ->get();

        $topicTitle = '';
        $matchedCourses = collect();
        $roadmapText = '';

        // Trường hợp hỏi về C / C++ / C nâng cao
        if (preg_match('/(c nâng cao|nâng cao c)/u', $msg) || (preg_match('/(^|\s|[,\.!?])c(\s|[,\.!?]|$)/u', $msg) && preg_match('/(nâng cao|học phí|giá)/u', $msg))) {
            $topicTitle = 'Khóa học C Nâng cao & Ngôn ngữ Lập trình Chuyên sâu';
            $matchedCourses = $allCourses->filter(function ($c) {
                return mb_stripos($c->title, 'C nâng cao') !== false ||
                    mb_stripos($c->title, 'Ngôn ngữ lập trình') !== false ||
                    mb_stripos($c->title, 'Lập trình C') !== false;
            })->sortBy('promotion_price');

            $roadmapText = "• **Giai đoạn 1 (Cơ bản vững chắc)**: Cú pháp C, kiểu dữ liệu, toán tử, mảng 1 chiều/2 chiều và các hàm xử lý chuỗi.\n" .
                "• **Giai đoạn 2 (Trọng tâm C Nâng cao)**: Con trỏ chuyên sâu (Pointers, Pointer to Pointer, Function Pointer), quản lý và cấp phát bộ nhớ động (malloc, calloc, realloc, free), Struct và Union.\n" .
                "• **Giai đoạn 3 (Cấu trúc dữ liệu & Ứng dụng)**: Danh sách liên kết đơn/đôi (Linked List), Ngăn xếp (Stack), Hàng đợi (Queue), Cây nhị phân (Binary Tree), thao tác File nhị phân và tối ưu bộ nhớ hệ thống.";
        } elseif (preg_match('/\b(c\+\+|cpp)\b/u', $msg) || preg_match('/(khóa học c\+\+|lập trình c\+\+)/u', $msg)) {
            $topicTitle = 'Lập trình C++ & Hướng đối tượng (OOP)';
            $matchedCourses = $allCourses->filter(function ($c) {
                return mb_stripos($c->title, 'C++') !== false || mb_stripos($c->title, 'OOP') !== false || mb_stripos($c->title, 'Ngôn ngữ lập trình') !== false;
            })->sortBy('promotion_price');

            $roadmapText = "• **Giai đoạn 1**: Cú pháp C++, biến, tham chiếu, con trỏ và cấp phát bộ nhớ động.\n" .
                "• **Giai đoạn 2**: Lập trình hướng đối tượng (OOP): Đóng gói (Encapsulation), Kế thừa (Inheritance), Đa hình (Polymorphism) và trừu tượng (Abstraction).\n" .
                "• **Giai đoạn 3**: Thư viện chuẩn STL (vector, map, set, algorithm), cấu trúc dữ liệu và giải thuật thực chiến.";
        } elseif (preg_match('/(^|\s|[,\.!?])c(\s|[,\.!?]|$)/u', $msg) || preg_match('/(khóa học c|lập trình c|ngôn ngữ c)/u', $msg)) {
            $topicTitle = 'Ngôn ngữ lập trình C (Nền tảng khoa học máy tính)';
            $matchedCourses = $allCourses->filter(function ($c) {
                return mb_stripos($c->title, 'c nâng cao') !== false || mb_stripos($c->title, 'Ngôn ngữ lập trình') !== false || mb_stripos($c->title, 'C++') !== false;
            })->sortBy('promotion_price');

            $roadmapText = "• **Giai đoạn 1 (Căn bản)**: Cấu trúc chương trình C, kiểu dữ liệu, câu lệnh điều kiện if/else, switch-case, vòng lặp for/while.\n" .
                "• **Giai đoạn 2 (Trọng tâm)**: Con trỏ (Pointers), mảng, bộ nhớ Heap/Stack và Struct.\n" .
                "• **Giai đoạn 3 (Nâng cao)**: Xử lý file, cấu trúc dữ liệu danh sách liên kết (Linked List), Stack, Queue và đồ án mini.";
        } elseif (preg_match('/(python|ai|trí tuệ nhân tạo|data|máy học)/u', $msg)) {
            $topicTitle = 'Lập trình Python & Trí tuệ nhân tạo (AI/Data)';
            $matchedCourses = $allCourses->filter(function ($c) {
                return mb_stripos($c->title, 'Python') !== false || mb_stripos($c->title, 'AI') !== false || mb_stripos($c->title, 'Trí tuệ') !== false || mb_stripos($c->title, 'Dữ liệu') !== false;
            })->sortBy('promotion_price');

            $roadmapText = "• **Giai đoạn 1**: Cú pháp Python căn bản, cấu trúc dữ liệu List, Tuple, Dict, Set và lập trình OOP.\n" .
                "• **Giai đoạn 2**: Thư viện phân tích dữ liệu chuyên nghiệp: Numpy, Pandas, Matplotlib và trực quan hóa dữ liệu.\n" .
                "• **Giai đoạn 3**: Machine Learning (Scikit-Learn), Deep Learning cơ bản và xây dựng mô hình dự đoán thực tế.";
        } elseif (preg_match('/(web|frontend|backend|php|laravel|react|javascript|js|html|css)/u', $msg)) {
            $topicTitle = 'Phát triển Web Fullstack (Frontend & Backend)';
            $matchedCourses = $allCourses->filter(function ($c) {
                return mb_stripos($c->title, 'Web') !== false || mb_stripos($c->title, 'HTML') !== false || mb_stripos($c->title, 'PHP') !== false || mb_stripos($c->title, 'Laravel') !== false || mb_stripos($c->title, 'Git') !== false;
            })->sortBy('promotion_price');

            $roadmapText = "• **Giai đoạn 1 (Frontend)**: Xây dựng giao diện web Responsive chuẩn UX với HTML5, CSS3, JavaScript và ReactJS.\n" .
                "• **Giai đoạn 2 (Backend)**: Lập trình máy chủ với PHP & Framework Laravel chuyên sâu, thiết kế CSDL MySQL, chuẩn hóa RESTful API.\n" .
                "• **Giai đoạn 3 (Dự án thực tế)**: Triển khai website thương mại điện tử hoàn chỉnh, tích hợp giỏ hàng và cổng thanh toán tự động VietQR.";
        } elseif (preg_match('/(thiết kế|design|ui|ux|figma|photoshop|đồ họa)/u', $msg)) {
            $topicTitle = 'Thiết kế đồ họa & Giao diện người dùng (UI/UX)';
            $matchedCourses = $allCourses->filter(function ($c) {
                return mb_stripos($c->title, 'Thiết kế') !== false || mb_stripos($c->title, 'Figma') !== false || mb_stripos($c->title, 'Photoshop') !== false || mb_stripos($c->title, 'UI') !== false;
            })->sortBy('promotion_price');

            $roadmapText = "• **Tư duy thẩm mỹ**: Bố cục, Typography, phân cấp thị giác và lý thuyết màu sắc trong thiết kế số.\n" .
                "• **Công cụ thành thạo**: Thiết kế Wireframe, UI App/Web chuyên nghiệp trên Figma, xử lý ảnh với Photoshop.\n" .
                "• **Dự án thực tế**: Tạo Prototype tương tác hoàn chỉnh và bàn giao Design System chuẩn cho lập trình viên.";
        }

        // Nếu không khớp từ khóa chuyên biệt, lấy các khóa học tiêu biểu & rẻ nhất
        if ($matchedCourses->isEmpty()) {
            $matchedCourses = $allCourses->sortBy('promotion_price')->take(4);
            $topicTitle = 'Khóa học tiêu biểu tại RoadToSchool';
            $roadmapText = "• RoadToSchool cung cấp lộ trình đào tạo bài bản từ Lập trình nền tảng, Web, Di động, AI đến Thiết kế đồ họa với sự hướng dẫn của các giảng viên nhiều năm kinh nghiệm.";
        }

        // Xây dựng danh sách khóa học kèm học phí chi tiết
        $courseInfo = "";
        foreach ($matchedCourses->take(4) as $idx => $course) {
            $author = $course->user ? $course->user->name : 'Giảng viên R2S';
            $price = $course->promotion_price > 0 ? number_format($course->promotion_price, 0, ',', '.') . ' VNĐ' : ($course->origin_price > 0 ? number_format($course->origin_price, 0, ',', '.') . ' VNĐ' : 'Miễn phí');
            $originPrice = ($course->origin_price > $course->promotion_price && $course->promotion_price > 0) ? ' *(Giá gốc: ' . number_format($course->origin_price, 0, ',', '.') . ' VNĐ)*' : '';
            $courseInfo .= ($idx + 1) . ". **{$course->title}**\n" .
                "   • Giảng viên: {$author}\n" .
                "   • Học phí ưu đãi: **{$price}**{$originPrice}\n";
        }

        $reply = "Dạ chào {$userName}! Về **{$topicTitle}**, hiện tại trên hệ thống RoadToSchool có các khóa học với học phí chi tiết như sau ạ:\n\n" .
            $courseInfo . "\n" .
            "📍 **Lộ trình học tập đề xuất**:\n" .
            $roadmapText . "\n\n" .
            "💡 **Cách đăng ký**: Bạn có thể tìm khóa học trên trang chủ -> nhấn **'Mua ngay'** -> quét mã VietQR để được hệ thống SePay tự động kích hoạt vào học ngay trong 1-3 phút nhé. Bạn có cần em tư vấn thêm chi tiết bài giảng của khóa học nào không ạ?";

        return $reply;
    }
}
