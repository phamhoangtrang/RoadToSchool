<?php

namespace Tests\Unit;

use App\Services\CourseRecommendationService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CourseRecommendationServiceTest extends TestCase
{
    public function test_it_uses_the_current_user_and_sanitizes_recommendations(): void
    {
        config()->set('services.recommendation.endpoint', 'https://recommendation.test/items');
        config()->set('services.recommendation.app_id', 7);
        Http::fake([
            'recommendation.test/*' => Http::response([
                '42' => [3, '2', 3, null, -1, 'invalid'],
            ]),
        ]);

        $courseIds = app(CourseRecommendationService::class)->courseIdsFor(42, 10);

        $this->assertSame([3, 2], $courseIds);
        Http::assertSent(fn (Request $request) => $request['appId'] === 7
            && $request['userId'] === 42
            && $request['count'] === 10);
    }

    public function test_it_returns_an_empty_list_when_the_service_is_disabled(): void
    {
        config()->set('services.recommendation.endpoint');
        Http::preventStrayRequests();

        $this->assertSame([], app(CourseRecommendationService::class)->courseIdsFor(42));
        Http::assertNothingSent();
    }

    public function test_it_fails_gracefully_when_the_service_is_unavailable(): void
    {
        config()->set('services.recommendation.endpoint', 'https://recommendation.test/items');
        Http::fake([
            'recommendation.test/*' => Http::response([], 503),
        ]);

        $this->assertSame([], app(CourseRecommendationService::class)->courseIdsFor(42));
    }
}
