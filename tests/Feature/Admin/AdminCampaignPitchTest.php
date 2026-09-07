<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\CampaignPitch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCampaignPitchTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Admin
    {
        return Admin::create([
            'name' => 'Store Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('AdminPass123'),
        ]);
    }

    private function pitch(array $overrides = []): CampaignPitch
    {
        return CampaignPitch::create(array_merge([
            'name' => 'Ayesha Beauty',
            'handle' => '@ayesha.beauty',
            'follower_count' => 45000,
            'campaign_type' => 'homepage_feature',
            'budget_range' => '30k_75k',
            'message' => 'Launch reel for our new gel liner.',
            'status' => 'new',
        ], $overrides));
    }

    public function test_guests_are_redirected_from_the_campaign_pitch_inbox(): void
    {
        $this->get(route('admin.campaign-pitches.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_sees_pitches_in_the_inbox(): void
    {
        $this->pitch();

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.campaign-pitches.index'))
            ->assertOk()
            ->assertSee('Ayesha Beauty')
            ->assertSee('@ayesha.beauty')
            ->assertSee('Homepage Feature')
            ->assertSee('PKR 30,000 – 75,000');
    }

    public function test_admin_can_filter_pitches_by_status(): void
    {
        $this->pitch(['name' => 'Fresh Pitch', 'status' => 'new']);
        $this->pitch(['name' => 'Closed Pitch', 'status' => 'closed']);

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.campaign-pitches.index', ['status' => 'closed']))
            ->assertOk()
            ->assertSee('Closed Pitch')
            ->assertDontSee('Fresh Pitch');
    }

    public function test_admin_can_mark_a_pitch_as_contacted(): void
    {
        $pitch = $this->pitch();

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.campaign-pitches.status', $pitch), ['status' => 'contacted'])
            ->assertRedirect();

        $this->assertSame('contacted', $pitch->fresh()->status);
    }

    public function test_admin_can_close_a_pitch(): void
    {
        $pitch = $this->pitch();

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.campaign-pitches.status', $pitch), ['status' => 'closed'])
            ->assertRedirect();

        $this->assertSame('closed', $pitch->fresh()->status);
    }

    public function test_status_update_rejects_unknown_statuses(): void
    {
        $pitch = $this->pitch();

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.campaign-pitches.status', $pitch), ['status' => 'archived'])
            ->assertSessionHasErrors('status');

        $this->assertSame('new', $pitch->fresh()->status);
    }

    public function test_admin_can_delete_a_pitch(): void
    {
        $pitch = $this->pitch();

        $this->actingAs($this->admin(), 'admin')
            ->delete(route('admin.campaign-pitches.destroy', $pitch))
            ->assertRedirect();

        $this->assertDatabaseCount('campaign_pitches', 0);
    }
}