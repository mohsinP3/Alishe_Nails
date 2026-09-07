<?php

namespace Tests\Feature;

use App\Models\CampaignPitch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkWithUsTest extends TestCase
{
    use RefreshDatabase;

    public function test_work_with_us_page_renders_with_the_campaign_copy(): void
    {
        $response = $this->get(route('work-with-us.index'));

        $response->assertOk();
        $response->assertSee('Your Content. Our Spotlight.');
        $response->assertSee('Alishe Nails now offers paid campaign placements for creators', false);
        $response->assertSee("We're Not a Marketplace", false);
        $response->assertSee('Pitch Your Campaign');
        $response->assertSee('Homepage Feature');
        $response->assertSee('Product Page Placement');
        $response->assertSee('Social Shout-out');
        $response->assertSee('Bundle');
    }

    public function test_homepage_shows_the_work_with_us_teaser_banner(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee("Got an Audience? Let's Grow Together.", false);
        $response->assertSee('real estate that converts');
        $response->assertSee(route('work-with-us.index'));
    }

    public function test_a_creator_can_submit_a_campaign_pitch(): void
    {
        $response = $this->from(route('work-with-us.index'))->post(route('work-with-us.store'), [
            'name' => 'Ayesha Beauty',
            'handle' => '@ayesha.beauty',
            'follower_count' => 45000,
            'campaign_type' => 'homepage_feature',
            'budget_range' => '30k_75k',
            'message' => 'Launch reel for our new gel liner, targeting bridal shoppers.',
            'portfolio_links' => 'https://instagram.com/p/abc123',
            'website' => '',
        ]);

        $response->assertRedirect(route('work-with-us.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('campaign_pitches', [
            'name' => 'Ayesha Beauty',
            'handle' => '@ayesha.beauty',
            'follower_count' => 45000,
            'campaign_type' => 'homepage_feature',
            'budget_range' => '30k_75k',
            'status' => 'new',
        ]);
    }

    public function test_campaign_pitch_rejects_unknown_dropdown_values(): void
    {
        $response = $this->from(route('work-with-us.index'))->post(route('work-with-us.store'), [
            'name' => 'Ayesha Beauty',
            'handle' => '@ayesha.beauty',
            'follower_count' => 45000,
            'campaign_type' => 'not-a-real-slot',
            'budget_range' => '10k_30k',
            'message' => 'Campaign details.',
            'website' => '',
        ]);

        $response->assertSessionHasErrors('campaign_type');
        $this->assertDatabaseCount('campaign_pitches', 0);
    }

    public function test_honeypot_rejects_bot_pitch_submissions(): void
    {
        $response = $this->from(route('work-with-us.index'))->post(route('work-with-us.store'), [
            'name' => 'Bot User',
            'handle' => '@bot',
            'follower_count' => 1000,
            'campaign_type' => 'bundle',
            'budget_range' => 'under_10k',
            'message' => 'Spam message',
            'website' => 'https://example.com',
        ]);

        $response->assertSessionHasErrors('website');
        $this->assertDatabaseCount('campaign_pitches', 0);
    }
}