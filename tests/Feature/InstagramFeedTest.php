<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\InstagramPost;
use App\Models\User;
use App\Services\InstagramFeedService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InstagramFeedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Tests never hit the real Graph API (Http::fake below); the token is
        // only needed so the service's "not configured" guard passes.
        config()->set('services.instagram.access_token', 'test-token');
        config()->set('services.instagram.business_account_id', '17841400000000000');
    }

    /** Two Graph API media nodes: one IMAGE, one VIDEO (with thumbnail). */
    private function fakeGraphApi(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response($this->graphPayload()),
        ]);
    }

    private function graphPayload(): array
    {
        return ['data' => [
            [
                'id' => '17905000000000001',
                'caption' => 'New french tip drop',
                'media_type' => 'IMAGE',
                'media_url' => 'https://scontent.cdninstagram.com/v/img1.jpg',
                'permalink' => 'https://www.instagram.com/p/ABC123/',
                'timestamp' => '2026-09-07T10:00:00+0000',
            ],
            [
                'id' => '17905000000000002',
                'caption' => 'Reel: how to apply press-ons',
                'media_type' => 'VIDEO',
                'media_url' => 'https://scontent.cdninstagram.com/v/vid1.mp4',
                'thumbnail_url' => 'https://scontent.cdninstagram.com/v/vid1_thumb.jpg',
                'permalink' => 'https://www.instagram.com/reel/XYZ789/',
                'timestamp' => '2026-09-07T09:00:00+0000',
            ],
        ]];
    }

    private function createAdmin(): Admin
    {
        return Admin::create([
            'name' => 'Store Admin',
            'email' => 'admin@example.com',
            'password' => 'AdminPass123',
        ]);
    }

    public function test_sync_saves_the_latest_posts_to_the_database(): void
    {
        $this->fakeGraphApi();

        $result = app(InstagramFeedService::class)->sync();

        $this->assertTrue($result['success']);
        $this->assertSame(2, $result['synced']);
        $this->assertDatabaseCount('instagram_posts', 2);
        $this->assertDatabaseHas('instagram_posts', [
            'instagram_id' => '17905000000000001',
            'type' => 'IMAGE',
            'permalink' => 'https://www.instagram.com/p/ABC123/',
        ]);

        // Videos must display their thumbnail poster, not the raw mp4.
        $video = InstagramPost::where('instagram_id', '17905000000000002')->first();
        $this->assertTrue($video->isVideo());
        $this->assertSame('https://scontent.cdninstagram.com/v/vid1_thumb.jpg', $video->display_url);
    }

    public function test_syncing_twice_updates_instead_of_duplicating_posts(): void
    {
        // The caption changed on Instagram between the two syncs.
        // (Http::fake() twice in one test does NOT override the first stub,
        // so consecutive API responses are modelled with Http::sequence().)
        Http::fake([
            'graph.facebook.com/*' => Http::sequence()
                ->push($this->graphPayload())
                ->push(['data' => [[
                    'id' => '17905000000000001',
                    'caption' => 'Updated caption',
                    'media_type' => 'IMAGE',
                    'media_url' => 'https://scontent.cdninstagram.com/v/img1.jpg',
                    'permalink' => 'https://www.instagram.com/p/ABC123/',
                    'timestamp' => '2026-09-07T10:00:00+0000',
                ]]]),
        ]);
        $service = app(InstagramFeedService::class);
        $service->sync();
        $service->sync();

        // Upsert, never duplicate: one row per Instagram post.
        $this->assertDatabaseCount('instagram_posts', 2);
        $this->assertSame(
            'Updated caption',
            InstagramPost::where('instagram_id', '17905000000000001')->value('caption'),
        );
    }

    public function test_sync_survives_an_api_failure_and_keeps_cached_posts(): void
    {
        // First sync succeeds, then the token expires / gets revoked —
        // the classic production failure mode.
        Http::fake([
            'graph.facebook.com/*' => Http::sequence()
                ->push($this->graphPayload())
                ->push(['error' => ['message' => 'The access token is invalid', 'code' => 190]], 401),
        ]);
        $service = app(InstagramFeedService::class);
        $service->sync();

        $result = $service->sync();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('The access token is invalid', $result['message']);
        // The cached posts that feed the homepage are untouched.
        $this->assertDatabaseCount('instagram_posts', 2);
    }

    public function test_sync_reports_when_the_token_is_not_configured(): void
    {
        config()->set('services.instagram.access_token', null);

        $result = app(InstagramFeedService::class)->sync();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('not configured', $result['message']);
        Http::assertNothingSent();
    }

    public function test_sync_skips_malformed_api_entries(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response([
                'data' => [
                    ['id' => '17905000000000009', 'media_type' => 'IMAGE'],
                    [
                        'id' => '17905000000000010',
                        'media_type' => 'IMAGE',
                        'media_url' => 'https://scontent.cdninstagram.com/v/ok.jpg',
                        'permalink' => 'https://www.instagram.com/p/OKPOST/',
                    ],
                ],
            ]),
        ]);

        $result = app(InstagramFeedService::class)->sync();

        $this->assertSame(1, $result['synced']);
        $this->assertDatabaseCount('instagram_posts', 1);
    }

    public function test_the_homepage_shows_synced_instagram_posts(): void
    {
        InstagramPost::create([
            'instagram_id' => '17905000000000001',
            'type' => 'IMAGE',
            'media_url' => 'https://scontent.cdninstagram.com/v/img1.jpg',
            'caption' => 'New french tip drop',
            'permalink' => 'https://www.instagram.com/p/ABC123/',
            'posted_at' => now(),
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('https://www.instagram.com/p/ABC123/');
        $response->assertSee('https://scontent.cdninstagram.com/v/img1.jpg', false);
        $response->assertSee('New french tip drop');
    }

    public function test_the_homepage_falls_back_to_placeholders_without_synced_posts(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('socials-grid');
        // The original static placeholder block, not an empty grid.
        $response->assertSee('Social image unavailable');
    }

    public function test_the_homepage_only_shows_active_posts(): void
    {
        InstagramPost::create([
            'instagram_id' => '1',
            'type' => 'IMAGE',
            'media_url' => 'https://scontent.cdninstagram.com/v/visible.jpg',
            'permalink' => 'https://www.instagram.com/p/VISIBLE/',
            'posted_at' => now(),
            'is_active' => true,
        ]);
        InstagramPost::create([
            'instagram_id' => '2',
            'type' => 'IMAGE',
            'media_url' => 'https://scontent.cdninstagram.com/v/hidden.jpg',
            'permalink' => 'https://www.instagram.com/p/HIDDEN/',
            'posted_at' => now(),
            'is_active' => false,
        ]);

        $response = $this->get('/');

        $response->assertSee('https://www.instagram.com/p/VISIBLE/');
        $response->assertDontSee('https://www.instagram.com/p/HIDDEN/');
    }

    public function test_the_sync_command_fetches_and_stores_posts(): void
    {
        $this->fakeGraphApi();

        $this->artisan('instagram:sync')->assertSuccessful();

        $this->assertDatabaseCount('instagram_posts', 2);
    }

    public function test_guests_cannot_trigger_a_manual_sync(): void
    {
        $this->post(route('admin.instagram.sync'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_a_customer_session_cannot_trigger_a_manual_sync(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer, 'web')
            ->post(route('admin.instagram.sync'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_an_admin_can_sync_now_from_the_settings_page(): void
    {
        $this->fakeGraphApi();
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.instagram.sync'));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Synced 2 Instagram post(s).');
        $this->assertDatabaseCount('instagram_posts', 2);
    }

    public function test_admin_sync_reports_an_error_when_the_api_fails(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response([
                'error' => ['message' => 'token expired'],
            ], 401),
        ]);
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.instagram.sync'));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Instagram API error: token expired Showing previously synced posts.');
        $this->assertDatabaseCount('instagram_posts', 0);
    }

    public function test_the_settings_page_shows_the_sync_button_and_last_sync_status(): void
    {
        Cache::put(InstagramFeedService::LAST_SYNC_CACHE_KEY, [
            'success' => true,
            'synced' => 2,
            'message' => 'Synced 2 Instagram post(s).',
            'at' => now()->toIso8601String(),
        ], now()->addHour());

        $response = $this->actingAs($this->createAdmin(), 'admin')->get(route('admin.settings.index'));

        $response->assertOk();
        $response->assertSee('Sync Now');
        $response->assertSee('Synced 2 Instagram post(s).');
        $response->assertSee(route('admin.instagram.sync'));
    }

    public function test_video_posts_render_their_thumbnail_and_a_play_badge(): void
    {
        InstagramPost::create([
            'instagram_id' => '17905000000000002',
            'type' => 'VIDEO',
            'media_url' => 'https://scontent.cdninstagram.com/v/vid1.mp4',
            'thumbnail_url' => 'https://scontent.cdninstagram.com/v/vid1_thumb.jpg',
            'permalink' => 'https://www.instagram.com/reel/XYZ789/',
            'posted_at' => now(),
        ]);

        $response = $this->get('/');

        // The gallery shows the thumbnail poster, never the raw video file.
        $response->assertSee('https://scontent.cdninstagram.com/v/vid1_thumb.jpg', false);
        $response->assertDontSee('https://scontent.cdninstagram.com/v/vid1.mp4', false);
        $response->assertSee('fa-solid fa-play', false);
    }

    public function test_the_homepage_orders_posts_newest_first(): void
    {
        InstagramPost::create([
            'instagram_id' => 'old',
            'type' => 'IMAGE',
            'media_url' => 'https://scontent.cdninstagram.com/v/old.jpg',
            'permalink' => 'https://www.instagram.com/p/OLDPOST/',
            'posted_at' => now()->subDay(),
        ]);
        InstagramPost::create([
            'instagram_id' => 'new',
            'type' => 'IMAGE',
            'media_url' => 'https://scontent.cdninstagram.com/v/new.jpg',
            'permalink' => 'https://www.instagram.com/p/NEWPOST/',
            'posted_at' => now(),
        ]);

        $response = $this->get('/');

        $response->assertSeeInOrder([
            'https://www.instagram.com/p/NEWPOST/',
            'https://www.instagram.com/p/OLDPOST/',
        ]);
    }

    public function test_the_sync_command_passes_the_limit_option_to_the_api(): void
    {
        $this->fakeGraphApi();

        $this->artisan('instagram:sync', ['--limit' => 3])->assertSuccessful();

        Http::assertSent(function ($request) {
            return (int) $request['limit'] === 3;
        });
    }

    public function test_the_sync_is_scheduled_to_run_every_six_hours(): void
    {
        // Not using expectsOutputToContain() here: multiple substrings can
        // land in a single console write chunk, which Mockery consumes only
        // once. Raw output assertions are deterministic.
        Artisan::call('schedule:list');

        $output = Artisan::output();

        $this->assertStringContainsString('0 */6 * * *', $output);
        $this->assertStringContainsString('instagram:sync', $output);
    }
}
