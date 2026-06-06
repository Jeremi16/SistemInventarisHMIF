<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as GoogleUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class GoogleOAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_shows_google_login_button(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Masuk dengan Google')
            ->assertSee(route('auth.google.redirect'), false);
    }

    public function test_google_redirect_sends_user_to_google_provider(): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect()->away('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        $this->get(route('auth.google.redirect'))
            ->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_registered_member_can_login_with_google(): void
    {
        $member = User::factory()->unverified()->create([
            'name' => 'Ayu Pratiwi',
            'email' => 'Ayu@HMIF.ITERA.AC.ID',
            'nim' => '121140001',
            'role' => 'member',
            'google_id' => null,
            'google_avatar' => null,
        ]);

        $this->mockGoogleCallback(
            id: 'google-ayu-001',
            email: 'ayu@hmif.itera.ac.id',
            avatar: 'https://lh3.googleusercontent.com/a/ayu'
        );

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('member.dashboard'))
            ->assertSessionHas('user.name', 'Ayu Pratiwi')
            ->assertSessionHas('user.nim', '121140001')
            ->assertSessionHas('user.role', 'member');

        $this->assertAuthenticatedAs($member);
        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'google_id' => 'google-ayu-001',
            'google_avatar' => 'https://lh3.googleusercontent.com/a/ayu',
        ]);
        $this->assertNotNull($member->fresh()->email_verified_at);
    }

    public function test_registered_admin_google_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'name' => 'Sherizka',
            'email' => 'sherizka@hmif.itera.ac.id',
            'nim' => '121140002',
            'role' => 'admin',
        ]);

        $this->mockGoogleCallback(
            id: 'google-admin-001',
            email: 'sherizka@hmif.itera.ac.id'
        );

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('user.role', 'admin');

        $this->assertAuthenticatedAs($admin);
    }

    public function test_unregistered_student_1xx140_email_is_auto_registered_as_member(): void
    {
        $this->mockGoogleCallback(
            id: 'google-student-001',
            email: 'jeremi.121140195@student.itera.ac.id',
            avatar: 'https://lh3.googleusercontent.com/a/student',
            name: 'Mahasiswa IF'
        );

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('member.dashboard'))
            ->assertSessionHas('user.name', 'Mahasiswa IF')
            ->assertSessionHas('user.nim', '121140195')
            ->assertSessionHas('user.role', 'member');

        $user = User::query()->where('email', 'jeremi.121140195@student.itera.ac.id')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertSame('121140195', $user->nim);
        $this->assertSame('member', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertSame('google-student-001', $user->google_id);
        $this->assertSame('https://lh3.googleusercontent.com/a/student', $user->google_avatar);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_student_email_can_link_to_existing_user_by_nim(): void
    {
        $member = User::factory()->create([
            'name' => 'Jeremi',
            'email' => 'jeremi@example.com',
            'nim' => '123140195',
            'role' => 'member',
            'google_id' => null,
        ]);

        $this->mockGoogleCallback(
            id: 'google-student-existing-001',
            email: 'jeremi.123140195@student.itera.ac.id',
            name: 'Jeremi Student'
        );

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('member.dashboard'))
            ->assertSessionHas('user.nim', '123140195');

        $this->assertAuthenticatedAs($member);
        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'email' => 'jeremi@example.com',
            'nim' => '123140195',
            'google_id' => 'google-student-existing-001',
        ]);
    }

    public function test_unregistered_if_itera_email_is_auto_registered_as_member(): void
    {
        $this->mockGoogleCallback(
            id: 'google-if-001',
            email: 'dosen@if.itera.ac.id',
            name: 'Dosen IF'
        );

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('member.dashboard'))
            ->assertSessionHas('user.name', 'Dosen IF')
            ->assertSessionHas('user.role', 'member');

        $user = User::query()->where('email', 'dosen@if.itera.ac.id')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->nim);
        $this->assertSame('member', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertSame('google-if-001', $user->google_id);
    }

    public function test_google_login_rejects_unallowed_unregistered_email(): void
    {
        $this->mockGoogleCallback(
            id: 'google-unknown-001',
            email: 'jeremi.120130001@student.itera.ac.id'
        );

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['identifier'])
            ->assertSessionHas('google_login_debug', function (array $debug) {
                return $debug['email'] === 'jeremi.120130001@student.itera.ac.id'
                    && $debug['parsed_nim'] === null
                    && $debug['passes_student_rule'] === false
                    && $debug['stage'] === 'email_rule';
            });

        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'email' => 'jeremi.120130001@student.itera.ac.id',
        ]);
    }

    public function test_google_login_rejects_inactive_account(): void
    {
        User::factory()->create([
            'email' => 'inactive@hmif.itera.ac.id',
            'role' => 'member',
            'is_active' => false,
        ]);

        $this->mockGoogleCallback(
            id: 'google-inactive-001',
            email: 'inactive@hmif.itera.ac.id'
        );

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['identifier']);

        $this->assertGuest();
    }

    private function mockGoogleCallback(string $id, string $email, ?string $avatar = null, string $name = 'Google User'): void
    {
        $googleUser = Mockery::mock(GoogleUser::class);
        $googleUser->shouldReceive('getId')->andReturn($id)->byDefault();
        $googleUser->shouldReceive('getEmail')->andReturn($email)->byDefault();
        $googleUser->shouldReceive('getAvatar')->andReturn($avatar)->byDefault();
        $googleUser->shouldReceive('getName')->andReturn($name)->byDefault();
        $googleUser->shouldReceive('getNickname')->andReturn(null)->byDefault();

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);
    }
}
