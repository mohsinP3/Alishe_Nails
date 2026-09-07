<?php

namespace App\Services;

use App\Models\InstagramPost;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Syncs the latest posts of the business Instagram account into the local
 * instagram_posts table via the Instagram Graph API (Meta).
 *
 * Two token flavours are supported through config('services.instagram'):
 *  - "Instagram API with Facebook Login" (default): api_base
 *    https://graph.facebook.com + INSTAGRAM_BUSINESS_ACCOUNT_ID set.
 *  - "Instagram API with Instagram Login": set INSTAGRAM_API_BASE to
 *    https://graph.instagram.com and leave the account id empty (uses /me).
 *
 * Failure philosophy: sync problems NEVER break the website. The homepage
 * reads only from the database (which acts as the cache), and every API
 * failure is swallowed into a reported status + log entry.
 */
class InstagramFeedService
{
    public const LAST_SYNC_CACHE_KEY = 'instagram.last_sync';

    /**
     * Fetch the latest media and upsert it into the database.
     *
     * @return array{success: bool, synced: int, message: string, at: string}
     */
    public function sync(?int $limit = null): array
    {
        $config = config('services.instagram');
        $token = $config['access_token'] ?? null;

        if (! $token) {
            return $this->finish(false, 'Instagram access token is not configured (set INSTAGRAM_ACCESS_TOKEN in .env).');
        }

        $limit ??= (int) ($config['fetch_limit'] ?? 12);

        $account = filled($config['business_account_id'] ?? null)
            ? (string) $config['business_account_id']
            : 'me';

        $endpoint = sprintf(
            '%s/%s/%s/media',
            rtrim((string) $config['api_base'], '/'),
            (string) $config['graph_version'],
            $account,
        );

        try {
            $response = Http::timeout(15)->get($endpoint, [
                'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp',
                'access_token' => $token,
                'limit' => $limit,
            ]);
        } catch (ConnectionException $e) {
            Log::warning('Instagram feed sync could not reach the Graph API.', ['error' => $e->getMessage()]);

            return $this->finish(false, 'Could not reach the Instagram API (connection timed out). Showing cached posts.');
        } catch (Throwable $e) {
            Log::error('Instagram feed sync failed unexpectedly.', ['error' => $e->getMessage()]);

            return $this->finish(false, 'Instagram sync failed unexpectedly. Showing cached posts.');
        }

        if ($response->failed()) {
            // e.g. expired token => error.code 190. Details go to the log,
            // the admin only gets a safe, token-free message.
            $apiMessage = (string) ($response->json('error.message') ?? 'HTTP '.$response->status());
            Log::warning('Instagram Graph API rejected the feed request.', [
                'status' => $response->status(),
                'error' => $response->json('error'),
            ]);

            return $this->finish(false, "Instagram API error: {$apiMessage} Showing previously synced posts.");
        }

        $synced = 0;

        foreach ((array) $response->json('data', []) as $item) {
            // Guard against malformed entries so one bad row can't kill the run.
            if (blank($item['id'] ?? null) || blank($item['media_url'] ?? null) || blank($item['permalink'] ?? null)) {
                continue;
            }

            InstagramPost::updateOrCreate(
                ['instagram_id' => (string) $item['id']],
                [
                    'type' => (string) ($item['media_type'] ?? 'IMAGE'),
                    'media_url' => (string) $item['media_url'],
                    'thumbnail_url' => $item['thumbnail_url'] ?? null,
                    'caption' => $item['caption'] ?? null,
                    'permalink' => (string) $item['permalink'],
                    'posted_at' => $item['timestamp'] ?? null,
                ],
            );

            $synced++;
        }

        return $this->finish(true, "Synced {$synced} Instagram post(s).", $synced);
    }

    /**
     * The homepage gallery feed — database only, never the API, never throws.
     * This is the fallback layer: when the token expires or Meta is down,
     * the last successfully synced posts simply keep showing.
     *
     * @return Collection<int, InstagramPost>
     */
    public function latestFeed(int $limit = 8): Collection
    {
        try {
            return InstagramPost::feed($limit)->get();
        } catch (Throwable $e) {
            Log::error('Unable to load the Instagram feed from the database.', ['error' => $e->getMessage()]);

            return collect();
        }
    }

    /** Status of the most recent sync attempt (success or failure), for the admin panel. */
    public function lastSync(): ?array
    {
        return Cache::get(self::LAST_SYNC_CACHE_KEY);
    }

    /**
     * @return array{success: bool, synced: int, message: string, at: string}
     */
    private function finish(bool $success, string $message, int $synced = 0): array
    {
        $status = [
            'success' => $success,
            'synced' => $synced,
            'message' => $message,
            'at' => now()->toIso8601String(),
        ];

        Cache::put(self::LAST_SYNC_CACHE_KEY, $status, now()->addDays(7));

        return $status;
    }
}
