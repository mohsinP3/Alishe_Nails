<?php

namespace Tests\Feature;

use App\Mail\CustomDesignMail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CustomDesignTest extends TestCase
{
    use WithFaker;

    public function test_custom_design_page_renders(): void
    {
        $response = $this->get(route('custom-design.create'));

        $response->assertOk();
        $response->assertSee('Book a Custom Design');
        $response->assertSee('Send Design Request');
    }

    public function test_custom_design_submission_sends_email_and_redirects_with_success(): void
    {
        Mail::fake();

        $response = $this->from(route('custom-design.create'))->post(route('custom-design.store'), [
            'name' => 'Ali Khan',
            'email' => 'ali@example.com',
            'phone' => '+92 300 1234567',
            'style' => 'almond chrome',
            'budget' => 'PKR 5,000',
            'details' => 'Soft pink base with chrome French tips and a pearl accent for a wedding look.',
            'website' => '',
        ]);

        $response->assertRedirect(route('custom-design.create'));
        $response->assertSessionHas('success', 'Your design request is on its way. We will get back to you with a quote soon.');

        Mail::assertSent(CustomDesignMail::class, function (CustomDesignMail $mail) {
            return $mail->hasTo(config('services.admin.notification_email'))
                && $mail->data['email'] === 'ali@example.com'
                && $mail->data['style'] === 'almond chrome';
        });
    }

    public function test_honeypot_rejects_bot_custom_design_submissions(): void
    {
        $response = $this->from(route('custom-design.create'))->post(route('custom-design.store'), [
            'name' => 'Bot User',
            'email' => 'bot@example.com',
            'phone' => '123456',
            'style' => 'almond',
            'details' => 'Spam details',
            'website' => 'https://example.com',
        ]);

        $response->assertSessionHasErrors('website');
    }
}
