<?php

namespace Tests\Feature;

use App\Mail\OtpMail;
use App\Models\Business;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\ShopTestCase;

class AuthOtpTest extends ShopTestCase
{
    /** Pulls the 6-digit code out of the faked email. */
    private function sentCode(): string
    {
        $code = null;
        Mail::assertSent(OtpMail::class, function (OtpMail $mail) use (&$code) {
            $code = $mail->token;
            return true;
        });

        return $code;
    }

    // ---------- password reset ----------

    public function test_password_cannot_be_reset_without_verifying_the_code(): void
    {
        Mail::fake();
        $victim = User::factory()->create(['email' => 'victim@example.com']);
        $oldHash = $victim->password;

        $this->post('/send-otp', ['email' => 'victim@example.com']);

        // The old bug: skip the code step and post a new password straight away
        $this->post('/reset-password', [
            'password' => 'hacked-password-1',
            'password_confirmation' => 'hacked-password-1',
        ])->assertRedirect('/forget-password');

        $this->assertSame($oldHash, $victim->fresh()->password);
    }

    public function test_password_reset_works_with_the_correct_code(): void
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'real@example.com']);

        $this->post('/send-otp', ['email' => 'real@example.com']);
        $code = $this->sentCode();

        $this->post('/verify-otp', ['otp' => $code])->assertRedirect('/reset-password');
        $this->post('/reset-password', [
            'password' => 'brand-new-pass-1',
            'password_confirmation' => 'brand-new-pass-1',
        ])->assertRedirect('/login');

        $this->assertTrue(Hash::check('brand-new-pass-1', $user->fresh()->password));
    }

    public function test_wrong_code_does_not_unlock_the_reset(): void
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'real@example.com']);
        $oldHash = $user->password;

        $this->post('/send-otp', ['email' => 'real@example.com']);

        $this->post('/verify-otp', ['otp' => '000000'])->assertRedirect('/verify-otp');
        $this->post('/reset-password', [
            'password' => 'brand-new-pass-1',
            'password_confirmation' => 'brand-new-pass-1',
        ])->assertRedirect('/forget-password');

        $this->assertSame($oldHash, $user->fresh()->password);
    }

    public function test_unknown_email_gets_the_same_answer_and_no_mail(): void
    {
        Mail::fake();

        $this->post('/send-otp', ['email' => 'nobody@example.com'])->assertRedirect('/verify-otp');

        Mail::assertNothingSent();
    }

    // ---------- OTP rules ----------

    public function test_code_is_locked_after_five_wrong_attempts(): void
    {
        $otp = app(OtpService::class);
        $code = $otp->issue('verify_email', 'a@example.com');

        for ($i = 0; $i < OtpService::MAX_ATTEMPTS; $i++) {
            $this->assertFalse($otp->verify('verify_email', 'a@example.com', '000000'));
        }

        // Even the right code no longer works
        $this->assertFalse($otp->verify('verify_email', 'a@example.com', $code));
    }

    public function test_code_works_only_once(): void
    {
        $otp = app(OtpService::class);
        $code = $otp->issue('verify_email', 'a@example.com');

        $this->assertTrue($otp->verify('verify_email', 'a@example.com', $code));
        $this->assertFalse($otp->verify('verify_email', 'a@example.com', $code));
    }

    public function test_code_is_not_stored_in_plain_text(): void
    {
        $otp = app(OtpService::class);
        $code = $otp->issue('verify_email', 'a@example.com');

        $key = 'otp:verify_email:' . sha1('a@example.com');
        $stored = Cache::get($key);

        $this->assertNotSame($code, $stored['hash']);
        $this->assertStringNotContainsString($code, json_encode($stored));
    }

    public function test_resend_is_blocked_during_the_cooldown(): void
    {
        $otp = app(OtpService::class);

        $this->assertIsString($otp->issue('verify_email', 'a@example.com'));
        $this->assertFalse($otp->issue('verify_email', 'a@example.com'));
    }

    public function test_codes_for_different_purposes_do_not_mix(): void
    {
        $otp = app(OtpService::class);
        $code = $otp->issue('reset_password', 'a@example.com');

        $this->assertFalse($otp->verify('verify_email', 'a@example.com', $code));
    }

    // ---------- email verification ----------

    public function test_email_gets_verified_with_the_correct_code(): void
    {
        Mail::fake();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->post(route('verification.send'))->assertSessionHas('status');
        $code = $this->sentCode();

        $this->actingAs($user)->post(route('verification.verify'), ['otp' => $code])
            ->assertRedirect(route('welcome'));

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_email_stays_unverified_with_a_wrong_code(): void
    {
        Mail::fake();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->post(route('verification.send'));
        $this->actingAs($user)->post(route('verification.verify'), ['otp' => '123456'])
            ->assertSessionHasErrors('otp');

        $this->assertNull($user->fresh()->email_verified_at);
    }

    // ---------- gating: verify first, then set up the shop ----------

    public function test_unverified_users_are_sent_to_verification(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->get(route('customers'))->assertRedirect(route('verification.notice'));
    }

    public function test_verified_users_without_a_shop_are_sent_to_onboarding(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('customers'))->assertRedirect(route('onboarding'));
        $this->actingAs($user)->get(route('welcome'))->assertRedirect(route('onboarding'));
    }

    public function test_onboarding_creates_the_shop_and_unlocks_the_app(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('onboarding.store'), [
            'name' => 'My New Shop',
            'business_type' => 'pharmacy',
            'phone' => '9800000000',
            'address' => 'Dhangadhi',
        ])->assertRedirect(route('welcome'));

        $this->assertSame('My New Shop', Business::where('owner_id', $user->id)->value('name'));
        $this->actingAs($user)->get(route('customers'))->assertOk();
    }

    public function test_onboarding_rejects_an_unknown_business_type(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('onboarding.store'), [
            'name' => 'Shop', 'business_type' => 'spaceship',
        ])->assertSessionHasErrors('business_type');

        $this->assertSame(0, Business::count());
    }

    public function test_user_cannot_create_a_second_shop_through_onboarding(): void
    {
        [$user] = $this->shop();

        $this->actingAs($user)->post(route('onboarding.store'), [
            'name' => 'Second Shop', 'business_type' => 'other',
        ])->assertRedirect(route('welcome'));

        $this->assertSame(1, Business::where('owner_id', $user->id)->count());
    }
}