<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Throwable;

class CourseRecommendationService
{
    /**
     * @return list<int>
     */
    public function courseIdsFor(int $userId, int $count = 10): array
    {
        $endpoint = config('services.recommendation.endpoint');

        if (! is_string($endpoint) || $endpoint === '') {
            return [];
        }

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->connectTimeout((int) config('services.recommendation.connect_timeout', 1))
                ->timeout((int) config('services.recommendation.timeout', 3))
                ->post($endpoint, [
                    'appId' => (int) config('services.recommendation.app_id', 1),
                    'userId' => $userId,
                    'count' => $count,
                ])
                ->throw();

            $courseIds = $response->json((string) $userId, []);

            if (! is_array($courseIds)) {
                return [];
            }

            return collect($courseIds)
                ->filter(fn ($courseId) => is_numeric($courseId) && (int) $courseId > 0)
                ->map(fn ($courseId) => (int) $courseId)
                ->unique()
                ->take($count)
                ->values()
                ->all();
        } catch (ConnectionException) {
            return [];
        } catch (Throwable $exception) {
            report($exception);

            return [];
        }
    }
}
