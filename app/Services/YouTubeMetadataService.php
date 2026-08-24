<?php

namespace App\Services;

use Embed\Embed;
use InvalidArgumentException;
use RuntimeException;

class YouTubeMetadataService
{
    public function __construct(private readonly Embed $embed) {}

    /**
     * @return array{title: string, description: string, duration: string}
     */
    public function metadata(string $url): array
    {
        $this->videoId($url);

        $info = $this->embed->get($url);
        $body = (string) $info->getResponse()->getBody();

        if (! preg_match('/"lengthSeconds":"(?<seconds>\d+)"/', $body, $matches)) {
            throw new RuntimeException('YouTube did not provide the video duration.');
        }

        return [
            'title' => (string) ($info->title ?? ''),
            'description' => (string) ($info->description ?? ''),
            'duration' => $this->formatDuration((int) $matches['seconds']),
        ];
    }

    public function embedHtml(string $url): string
    {
        $videoId = $this->videoId($url);

        return sprintf(
            '<div class="ratio ratio-16x9"><iframe src="https://www.youtube-nocookie.com/embed/%s" title="YouTube video player" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div>',
            $videoId,
        );
    }

    public function videoId(string $url): string
    {
        $parts = parse_url(trim($url));
        $host = strtolower($parts['host'] ?? '');
        $host = preg_replace('/^www\./', '', $host);
        $path = trim($parts['path'] ?? '', '/');
        $videoId = null;

        if ($host === 'youtu.be') {
            $videoId = explode('/', $path)[0] ?? null;
        } elseif (in_array($host, ['youtube.com', 'm.youtube.com', 'music.youtube.com'], true)) {
            if ($path === 'watch') {
                parse_str($parts['query'] ?? '', $query);
                $videoId = $query['v'] ?? null;
            } elseif (preg_match('#^(?:embed|shorts|live)/([A-Za-z0-9_-]{11})#', $path, $matches)) {
                $videoId = $matches[1];
            }
        }

        if (! is_string($videoId) || ! preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId)) {
            throw new InvalidArgumentException('Please enter a valid YouTube video URL.');
        }

        return $videoId;
    }

    public function formatDuration(int $seconds): string
    {
        if ($seconds < 0) {
            throw new InvalidArgumentException('Video duration cannot be negative.');
        }

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        return $hours > 0
            ? sprintf('%d:%02d:%02d', $hours, $minutes, $remainingSeconds)
            : sprintf('%d:%02d', $minutes, $remainingSeconds);
    }
}
