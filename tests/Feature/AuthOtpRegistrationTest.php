<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthOtpRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_without_cccd_and_receives_otp_in_session(): void
    {
        Mail::fake();

        $response = $this->post(route('register.post'), [
            'name' => 'Nguyễn Văn Test',
            'email' => 'testuser@example.com',
            'phone' => '0901234567',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertTrue(session()->has('pending_registration'));
        $this->assertTrue(session()->has('otp_hash'));
        $this->assertEquals(0, session('otp_attempts'));

        // User is not yet written to database until verified
        $this->assertDatabaseMissing('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    public function test_registration_validation_fails_on_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->post(route('register.post'), [
            'name' => 'Duplicate User',
            'email' => 'existing@example.com',
            'phone' => '0912345678',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_verify_otp_success_activates_user_account(): void
    {
        $plainOtp = '123456';

        $response = $this->withSession([
            'pending_registration' => [
                'name' => 'Hợp Lệ',
                'email' => 'otp@example.com',
                'password' => Hash::make('Password123!'),
                'phone' => '0912345678',
                'role' => 'customer',
            ],
            'otp_hash' => Hash::make($plainOtp),
            'otp_expires_at' => now()->addMinutes(15)->toDateTimeString(),
            'otp_attempts' => 0,
        ])->post(route('verification.verify-otp'), [
            'otp' => $plainOtp,
        ]);

        $response->assertRedirect(route('storefront.index'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'otp@example.com',
            'cccd' => null,
        ]);
        $this->assertFalse(session()->has('otp_hash'));
    }

    public function test_verify_otp_fails_with_wrong_code_and_increments_attempts(): void
    {
        $response = $this->withSession([
            'pending_registration' => [
                'name' => 'Fail User',
                'email' => 'otp_fail@example.com',
                'password' => Hash::make('Password123!'),
            ],
            'otp_hash' => Hash::make('654321'),
            'otp_expires_at' => now()->addMinutes(15)->toDateTimeString(),
            'otp_attempts' => 0,
        ])->post(route('verification.verify-otp'), [
            'otp' => '111111',
        ]);

        $response->assertSessionHasErrors(['otp']);
        $this->assertEquals(1, session('otp_attempts'));
    }

    public function test_brute_force_protection_invalidates_otp_after_5_failed_attempts(): void
    {
        $response = $this->withSession([
            'pending_registration' => [
                'name' => 'Brute Force User',
                'email' => 'bruteforce@example.com',
                'password' => Hash::make('Password123!'),
            ],
            'otp_hash' => Hash::make('999999'),
            'otp_expires_at' => now()->addMinutes(15)->toDateTimeString(),
            'otp_attempts' => 5, // 5 failed attempts already recorded
        ])->post(route('verification.verify-otp'), [
            'otp' => '000000',
        ]);

        $response->assertSessionHasErrors(['otp']);
        $this->assertNull(session('otp_hash'));
    }

    public function test_resend_otp_enforces_60_second_cooldown(): void
    {
        Mail::fake();

        // Resend request when cooldown has not expired yet
        $response = $this->withSession([
            'pending_registration' => [
                'name' => 'Resend User',
                'email' => 'resend@example.com',
                'password' => Hash::make('Password123!'),
            ],
            'otp_email' => 'resend@example.com',
            'otp_last_sent_at' => now()->subSeconds(20)->toDateTimeString(), // Sent 20s ago
        ])->post(route('verification.send'));

        $response->assertSessionHasErrors(['otp']);
    }
}
