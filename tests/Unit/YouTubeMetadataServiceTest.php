<?php

namespace Tests\Unit;

use App\Services\YouTubeMetadataService;
use Embed\Embed;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class YouTubeMetadataServiceTest extends TestCase
{
    private YouTubeMetadataService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new YouTubeMetadataService(new Embed);
    }

    #[DataProvider('youtubeUrls')]
    public function test_it_extracts_video_ids_from_supported_youtube_urls(string $url): void
    {
        $this->assertSame('PNp1prcWbkM', $this->service->videoId($url));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function youtubeUrls(): array
    {
        return [
            'watch' => ['https://www.youtube.com/watch?v=PNp1prcWbkM'],
            'short' => ['https://youtu.be/PNp1prcWbkM?t=10'],
            'embed' => ['https://www.youtube.com/embed/PNp1prcWbkM'],
            'shorts' => ['https://youtube.com/shorts/PNp1prcWbkM'],
        ];
    }

    public function test_it_rejects_non_youtube_urls(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->videoId('https://example.com/watch?v=PNp1prcWbkM');
    }

    public function test_it_formats_short_and_long_durations(): void
    {
        $this->assertSame('1:05', $this->service->formatDuration(65));
        $this->assertSame('1:01:01', $this->service->formatDuration(3661));
    }

    public function test_it_generates_a_privacy_enhanced_responsive_embed(): void
    {
        $html = $this->service->embedHtml('https://youtu.be/PNp1prcWbkM');

        $this->assertStringContainsString('class="ratio ratio-16x9"', $html);
        $this->assertStringContainsString('youtube-nocookie.com/embed/PNp1prcWbkM', $html);
    }
}
