<?php

namespace App\Support\Umami;

use App\Support\Umami\DataTransferObjects\StatsResult;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use function env;
use function now;

class UmamiClient
{
    public function __construct(
        protected string $token,
    )
    {
    }

    public static function login(string $user, string $password): self
    {
        $response = Http::post(config('umami.url') . '/auth/login', [
            'username' => $user,
            'password' => $password,
        ]);

        if(!$response->ok()) {
            throw new Exception($response->body());
        }

        return new self($response->json('token'));
    }

    public function getStats(int $id, Carbon $start, Carbon $end): StatsResult
    {
        return new StatsResult(...$this->getClient()
            ->get("/website/{$id}/stats", [
                'start_at' => $start->timestamp * 1000,
                'end_at' => $end->timestamp * 1000,
            ])->json());
    }

    public function getMetrics(int $id, string $type, Carbon $start, Carbon $end): array
    {
        $result = $this->getClient()
            ->get("website/{$id}/metrics", [
                'start_at' => $start->timestamp * 1000,
                'end_at' => $end->timestamp * 1000,
                'type' => $type,
            ])->json();

        $referrals = collect($result)
            ->mapWithKeys(fn($data) => [
                $data['x'] => $data['y']
            ])
            ->reject(fn($_, $x) => $x === "");

        $urls = [];

        foreach ($referrals as $url => $count) {
            $url = (string)Str::of($url)
                ->after('//')
                ->before('/');

            if (array_key_exists($url, $urls)) {
                $urls[$url] += $count;
            } else {
                $urls[$url] = $count;
            }
        }

        return collect($urls)
            ->sortByDesc(fn ($_, $x) => $_)
            ->toArray();
    }

    protected function getClient(): PendingRequest
    {
        return Http::withCookies([
            'umami.auth' => $this->token,
        ], (string)Str::of(config('umami.url'))->after('//')->before('/'))
            ->baseUrl(config('umami.url'));
    }
}