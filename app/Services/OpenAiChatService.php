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
            // Thử model chính, nếu bị treo/lỗi thì thử lại bằng model dự phòng
            $models = array_values(array_unique([
                $this->geminiModel ?: 'gemini-3.1-flash-lite',
                'gemini-flash-lite-latest',
            ]));
            foreach ($models as $model) {
                try {
                    $geminiReply = $this->callGeminiApi($userMessage, $recentMessages, $currentUser, $model);
                    if (!empty($geminiReply)) {
                        return $geminiReply;
                    }
                } catch (\Exception $e) {
                    Log::error("Gemini API Error ({$model}): " . $e->getMessage());
                }
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
    protected function callGeminiApi($userMessage, array $recentMessages, $currentUser, $modelOverride = null)
    {
        $systemInstruction = $this->buildSystemContext($currentUser, $userMessage);
        $modelName = $modelOverride ?: ($this->geminiModel ?: 'gemini-3.1-flash-lite');
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
                    'temperature' => 0.25,
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
                    'temperature' => 0.25,
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
            ->timeout(7)
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
            'temperature' => 0.25,
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
     * Từ khóa tìm kiếm: bỏ từ dừng, giữ cụm 1-2 từ + từ khóa chủ đề đã nhận diện
     */
    protected function searchTerms($message)
    {
        $msg = mb_strtolower($message, 'UTF-8');
        $stop = ['cho', 'mình', 'em', 'tôi', 'hỏi', 'về', 'các', 'những', 'có', 'không', 'nào', 'gì', 'là', 'và', 'của',
            'một', 'khóa', 'học', 'khoá', 'bao', 'nhiêu', 'giá', 'tiền', 'học phí', 'muốn', 'cần', 'bạn', 'ơi', 'ạ', 'với',
            'thì', 'được', 'để', 'cái', 'này', 'kia', 'xin', 'chào', 'hãy', 'tư', 'vấn', 'giúp', 'nhé', 'nha', 'đang', 'cần',
            'rẻ', 'nhất', 'đắt', 'mắc', 'thấp', 'cao', 'nhiều', 'ít', 'hiện', 'tại', 'đây', 'bên', 'mình', 'bao', 'lâu'];
        $clean = preg_replace('/[^\p{L}\p{N}\+\#\s]/u', ' ', $msg);
        $words = array_values(array_filter(preg_split('/\s+/u', trim($clean)), function ($w) use ($stop) {
            return $w !== '' && !in_array($w, $stop, true);
        }));

        $terms = [];
        foreach ($words as $i => $w) {
            if (mb_strlen($w) >= 2 || $w === 'c') {
                $terms[] = $w;
            }
            if (isset($words[$i + 1])) {
                $terms[] = $w . ' ' . $words[$i + 1];
            }
        }
        return array_values(array_unique(array_merge($terms, $this->extractKeywords($message))));
    }

    /**
     * Chấm điểm các khóa học theo ý hỏi: khớp tên (x3), danh mục (x2), mô tả (x1)
     */
    protected function retrieveCourses($userMessage, $limit = 8)
    {
        $terms = $this->searchTerms($userMessage);
        $courses = Course::where('is_accepted', 1)->with(['user', 'category'])->get();
        if (empty($terms)) {
            return collect();
        }

        $match = function ($haystack, $term) {
            return preg_match('/(?<![\p{L}\p{N}])' . preg_quote($term, '/') . '(?![\p{L}\p{N}])/u', $haystack) === 1;
        };

        $scored = $courses->map(function ($c) use ($terms, $match) {
            $title = mb_strtolower($c->title, 'UTF-8');
            $cat = mb_strtolower(($c->category ? $c->category->title . ' ' . $c->category->vi_title : ''), 'UTF-8');
            $desc = mb_strtolower(strip_tags((string)$c->description), 'UTF-8');
            $score = 0;
            foreach ($terms as $t) {
                $weight = str_contains($t, ' ') ? 2 : 1;
                if ($match($title, $t)) $score += 3 * $weight;
                if ($match($cat, $t)) $score += 2 * $weight;
                if ($match($desc, $t)) $score += 1 * $weight;
            }
            $c->match_score = $score;
            return $c;
        })->filter(function ($c) {
            return $c->match_score > 0;
        })->sortByDesc(function ($c) {
            return [$c->match_score, $c->seller];
        });

        return $scored->take($limit)->values();
    }

    protected function formatCourseLine($c)
    {
        $levels = [1 => 'Cơ bản', 2 => 'Trung cấp', 3 => 'Nâng cao'];
        $price = $c->origin_price == 0 ? 'Miễn phí' : number_format($c->origin_price, 0, ',', '.') . ' VNĐ';
        if ($c->promotion_price > 0 && $c->promotion_price < $c->origin_price) {
            $price = number_format($c->promotion_price, 0, ',', '.') . ' VNĐ (giá gốc ' . number_format($c->origin_price, 0, ',', '.') . ' VNĐ)';
        }
        $desc = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string)$c->description))));
        $desc = mb_strlen($desc) > 220 ? mb_substr($desc, 0, 220) . '...' : $desc;
        $category = $c->category ? ($c->category->vi_title ?: $c->category->title) : 'Tổng hợp';
        $instructor = $c->user ? $c->user->name : 'Đội ngũ R2S';

        return "- \"{$c->title}\" | Danh mục: {$category} | Giảng viên: {$instructor}"
            . " | Cấp độ: " . ($levels[$c->level] ?? 'Mọi cấp độ')
            . " | {$c->lecture_numbers} bài giảng | {$c->duration} phút"
            . " | {$c->seller} học viên | Đánh giá: {$c->course_rate}/5"
            . " | Học phí: {$price}"
            . ($desc !== '' ? " | Mô tả: {$desc}" : '');
    }

    /**
     * Tạo System Context từ dữ liệu thật của RoadToSchool (không có giá/khuyến mãi gõ cứng)
     */
    protected function buildSystemContext($currentUser, $userMessage = '')
    {
        $rawName = $currentUser ? $currentUser->name : 'bạn';
        $userName = (in_array($rawName, ['Quản trị viên Hệ thống', 'admin', 'Admin', 'super_admin']) || mb_stripos($rawName, 'Quản trị') !== false) ? 'bạn' : $rawName;

        // 1. Khóa học liên quan trực tiếp tới câu hỏi
        $matched = $this->retrieveCourses($userMessage, 8);

        // 2. Khi hỏi chung chung: bổ sung vài khóa tiêu biểu (bán chạy + rẻ nhất)
        $extra = collect();
        if ($matched->count() < 4) {
            $popular = Course::where('is_accepted', 1)->with(['user', 'category'])->orderBy('seller', 'desc')->take(4)->get();
            $cheapest = Course::where('is_accepted', 1)->with(['user', 'category'])
                ->whereRaw('IF(promotion_price > 0, promotion_price, origin_price) > 0')
                ->orderByRaw('IF(promotion_price > 0, promotion_price, origin_price) asc')->take(3)->get();
            $extra = $popular->merge($cheapest);
        }
        // 2b. Hỏi theo mức giá (rẻ nhất / đắt nhất / miễn phí): luôn đưa đúng nhóm đã sắp xếp theo giá
        $msgLow = mb_strtolower($userMessage, 'UTF-8');
        $priceExpr = 'IF(promotion_price > 0, promotion_price, origin_price)';
        $priceSections = '';
        if (preg_match('/(rẻ|thấp nhất|giá thấp|tiết kiệm|ít tiền|miễn phí|free)/u', $msgLow)) {
            $cheap = Course::where('is_accepted', 1)->with(['user', 'category'])
                ->orderByRaw("{$priceExpr} asc")->take(5)->get();
            $priceSections .= "\nKHÓA HỌC HỌC PHÍ THẤP NHẤT (đã sắp xếp từ thấp đến cao, dòng đầu là rẻ nhất):\n"
                . $cheap->map(function ($c) { return $this->formatCourseLine($c); })->implode("\n") . "\n";
        }
        if (preg_match('/(đắt|mắc|cao nhất|giá cao)/u', $msgLow)) {
            $pricey = Course::where('is_accepted', 1)->with(['user', 'category'])
                ->orderByRaw("{$priceExpr} desc")->take(5)->get();
            $priceSections .= "\nKHÓA HỌC HỌC PHÍ CAO NHẤT (đã sắp xếp từ cao xuống thấp, dòng đầu là đắt nhất):\n"
                . $pricey->map(function ($c) { return $this->formatCourseLine($c); })->implode("\n") . "\n";
        }

        $allCourses = $matched->merge($extra)->unique('id');
        $courseListText = $allCourses->map(function ($c) {
            return $this->formatCourseLine($c);
        })->implode("\n");
        if ($courseListText === '') {
            $courseListText = '(Không tìm thấy khóa học nào khớp với câu hỏi.)';
        }

        // 3. Thống kê hệ thống (từ database)
        $stats = Course::where('is_accepted', 1)
            ->selectRaw('COUNT(*) total, MIN(IF(promotion_price > 0, promotion_price, origin_price)) min_price, MAX(IF(promotion_price > 0, promotion_price, origin_price)) max_price')
            ->first();
        $categoryNames = \App\Models\Category::where('parent_id', 0)->get()->map(function ($c) {
            return $c->vi_title ?: $c->title;
        })->implode(', ');

        // 4. Đơn hàng gần đây của chính học viên đang chat
        $ordersText = 'Học viên chưa đăng nhập hoặc chưa có đơn hàng nào.';
        if ($currentUser) {
            $bills = \App\Models\Bill::where('user_id', $currentUser->id)->with('billCourses.course')->latest()->take(5)->get();
            if ($bills->isNotEmpty()) {
                $ordersText = $bills->map(function ($b) {
                    $titles = $b->billCourses->map(function ($bc) {
                        return $bc->course ? $bc->course->title : '(khóa đã xóa)';
                    })->implode('; ');
                    $status = \App\Models\Bill::$status[$b->status] ?? 'Không rõ';
                    return "- Đơn RTS{$b->id} | Trạng thái: {$status} | Tổng: " . number_format($b->total_amount, 0, ',', '.') . " VNĐ | Khóa học: {$titles}";
                })->implode("\n");
            }
        }

        $bank = config('services.vietqr.bank_id', 'MB');
        $stk = config('services.vietqr.account_no', '44401287290165');
        $name = config('services.vietqr.account_name', 'PHAM HOANG TRANG');
        $minPrice = number_format((float)$stats->min_price, 0, ',', '.');
        $maxPrice = number_format((float)$stats->max_price, 0, ',', '.');

        return <<<EOT
BẠN LÀ TRỢ LÝ AI CỦA NỀN TẢNG HỌC TRỰC TUYẾN ROADTOSCHOOL (R2S).

QUY TẮC BẮT BUỘC:
1. Xưng "em", gọi học viên là "bạn" (hoặc tên "{$userName}"). Thân thiện, ngắn gọn, rõ ràng, dùng gạch đầu dòng khi liệt kê.
2. CHỈ dùng thông tin trong các mục DỮ LIỆU bên dưới. KHÔNG tự bịa tên khóa học, giá, khuyến mãi, chính sách hay số liệu. Mọi mức giá phải lấy đúng từ danh sách khóa học.
3. Nếu dữ liệu bên dưới không có câu trả lời (ví dụ: hoàn tiền, chứng chỉ, khuyến mãi, thông tin ngoài hệ thống), hãy nói thẳng "hiện em chưa có thông tin này" và mời học viên nhắn rõ vấn đề để quản trị viên hỗ trợ trực tiếp. Tuyệt đối không đoán.
4. Khi liệt kê khóa học, đánh số 1, 2, 3 từ đầu. Không nêu mã ID nội bộ. Nếu có nhiều khóa trùng tên, phân biệt bằng giảng viên và học phí.
5. Chỉ giới thiệu khóa học khớp với câu hỏi; nếu danh sách bên dưới không có khóa phù hợp, nói rõ là chưa tìm thấy thay vì gợi ý khóa không liên quan.
6. Câu hỏi về đơn hàng: chỉ dùng mục "ĐƠN HÀNG GẦN ĐÂY CỦA HỌC VIÊN". Không có đơn đó thì nói chưa thấy đơn và nhờ học viên cung cấp mã đơn (RTS...).

DỮ LIỆU HỆ THỐNG:
- Hiện có {$stats->total} khóa học đang mở, học phí từ {$minPrice} đến {$maxPrice} VNĐ.
- Danh mục chính: {$categoryNames}.
- Cách thanh toán: chỉ chuyển khoản QR (VietQR), KHÔNG nhận tiền mặt hay COD. Ngân hàng {$bank}, số tài khoản {$stk}, chủ tài khoản {$name}. Nội dung chuyển khoản: RTS + mã đơn (mã QR tự điền sẵn).
- Quy trình: thêm khóa học vào Giỏ hàng, bấm "Thanh toán", quét mã QR bằng app ngân hàng. Hệ thống SePay tự kích hoạt trong khoảng 1-3 phút; khóa học xuất hiện ở "Khóa học của tôi" (bấm ảnh đại diện góc trên bên phải).
- Nếu quá 3-5 phút chưa thấy khóa học: học viên nhắn mã đơn RTS hoặc ảnh biên lai trong khung chat này để quản trị viên kiểm tra thủ công.

KHÓA HỌC LIÊN QUAN ĐẾN CÂU HỎI (lấy từ database):
{$courseListText}
{$priceSections}
ĐƠN HÀNG GẦN ĐÂY CỦA HỌC VIÊN:
{$ordersText}
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
            return "Dạ em đã ghi nhận nội dung của {$userName}. Cuộc trò chuyện này sẽ được chuyển để Quản trị viên xem và phản hồi trực tiếp trong khung chat. Bạn vui lòng mô tả chi tiết vấn đề (mã đơn RTS, thời gian, ảnh biên lai nếu có) để được hỗ trợ nhanh hơn nhé!";
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
