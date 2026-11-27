<?php
/**
 * Bộ câu hỏi kiểm tra chat AI (chạy tay, gọi Gemini thật).
 *
 * Chạy:   php tests/Manual/ai_qa.php          (tất cả câu hỏi)
 *         php tests/Manual/ai_qa.php 3        (chỉ câu số 3)
 *
 * Mỗi câu có "must" (từ khóa BẮT BUỘC có trong câu trả lời) và "must_not"
 * (từ khóa KHÔNG được có, thường là thông tin bịa). Kết quả PASS/FAIL chỉ là
 * gợi ý, bạn nên đọc nội dung trả lời để đánh giá.
 */

chdir(dirname(__DIR__, 2));
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Course;

$svc = new App\Services\OpenAiChatService;

// Lấy giá thật từ database để so sánh (không gõ cứng)
$fmt = function ($n) { return number_format((float)$n, 0, ',', '.'); };
$ml = Course::where('title', 'like', '%Machine Learning%')->where('is_accepted', 1)->first();
$html = Course::where('title', 'like', '%HTML5%')->where('is_accepted', 1)->first();
$git = Course::where('title', 'like', '%Git%')->where('is_accepted', 1)->first();
$price = function ($c) use ($fmt) {
    return $c ? $fmt($c->promotion_price > 0 ? $c->promotion_price : $c->origin_price) : 'KHONG-CO';
};

$cases = [
    ['q' => 'Khóa học C nâng cao giá bao nhiêu?',                 'must' => ['100.000'],        'must_not' => ['300.000']],
    ['q' => 'Machine Learning giá bao nhiêu?',                    'must' => [$price($ml)],      'must_not' => ['300.000']],
    ['q' => 'Khóa HTML5 CSS3 Responsive giá bao nhiêu?',          'must' => [$price($html)],    'must_not' => ['399.000']],
    ['q' => 'Khóa Git và GitHub giá bao nhiêu và giá gốc?',       'must' => [$price($git)],     'must_not' => ['499.000']],
    ['q' => 'Có khóa Digital Marketing nào không?',               'must' => ['Marketing', 'Tiếp thị'], 'any' => true, 'must_not' => []],
    ['q' => 'Có khóa học nấu ăn không?',                          'must' => ['chưa tìm thấy', 'không có', 'chưa có'], 'any' => true, 'must_not' => ['VNĐ']],
    ['q' => 'Bên mình có hoàn tiền không?',                       'must' => ['chưa có thông tin', 'quản trị viên'], 'any' => true, 'must_not' => ['hoàn tiền trong', '7 ngày', '30 ngày']],
    ['q' => 'Thanh toán bằng tiền mặt được không?',               'must' => ['không'],          'must_not' => []],
    ['q' => 'Thanh toán như thế nào?',                            'must' => ['QR'],             'must_not' => ['thẻ tín dụng', 'Momo', 'ZaloPay']],
    ['q' => 'Chuyển khoản xong bao lâu thì vào học được?',        'must' => ['1-3', '1 - 3', '1 đến 3'],   'any' => true, 'must_not' => []],
    ['q' => 'Học xong có chứng chỉ không?',                       'must' => ['chưa có thông tin', 'quản trị viên'], 'any' => true, 'must_not' => ['có cấp chứng chỉ']],
    ['q' => 'Có khuyến mãi giảm giá gì không?',                   'must' => [],                 'must_not' => ['30%', '60%']],
    ['q' => 'Khóa học rẻ nhất giá bao nhiêu?',                    'must' => ['100.000'],        'must_not' => []],
    ['q' => 'Cho mình lộ trình học lập trình web',                'must' => ['Web'],            'must_not' => []],
    ['q' => 'Tôi chuyển khoản rồi mà chưa thấy khóa học',         'must' => ['RTS'],            'must_not' => []],
];

$only = isset($argv[1]) ? (int)$argv[1] : null;
$pass = $fail = 0;

foreach ($cases as $i => $c) {
    $n = $i + 1;
    if ($only && $only !== $n) continue;

    $t = microtime(true);
    $reply = $svc->reply($c['q'], [], null);
    $sec = round(microtime(true) - $t, 1);
    $low = mb_strtolower($reply, 'UTF-8');

    $has = function ($kw) use ($low) { return $kw !== '' && mb_strpos($low, mb_strtolower($kw, 'UTF-8')) !== false; };
    $okMust = empty($c['must']);
    if (!$okMust) {
        $hits = array_filter($c['must'], $has);
        $okMust = !empty($c['any']) ? count($hits) > 0 : count($hits) === count($c['must']);
    }
    $bad = array_values(array_filter($c['must_not'], $has));
    $ok = $okMust && empty($bad);
    $ok ? $pass++ : $fail++;

    echo ($ok ? 'PASS' : 'FAIL') . " #{$n} ({$sec}s) {$c['q']}\n";
    if (!$ok) {
        if (!$okMust) echo "   thiếu: " . implode(' | ', $c['must']) . "\n";
        if ($bad) echo "   không được có: " . implode(', ', $bad) . "\n";
    }
    echo '   > ' . str_replace("\n", "\n     ", mb_substr(trim($reply), 0, 500)) . "\n\n";
}

echo "Kết quả: {$pass} đạt, {$fail} chưa đạt\n";
