<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuthSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as GoogleUser;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): SymfonyRedirectResponse|RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            report($exception);

            return $this->failGoogleLogin('Login Google gagal saat mengambil data dari Google.', [
                'stage' => 'socialite_user',
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
                'hint' => 'Pastikan membuka login dari http://localhost:8000/login dan redirect URI Google Console memakai /auth/google/callback.',
                'configured_redirect_uri' => config('services.google.redirect'),
            ]);
        }

        $email = Str::lower((string) $googleUser->getEmail());

        if ($email === '') {
            return $this->failGoogleLogin('Akun Google tidak mengirimkan email.', [
                'stage' => 'google_email',
                'google_id_present' => (string) $googleUser->getId() !== '',
            ]);
        }

        try {
            $user = $this->findOrCreateAllowedUser($googleUser, $email);
        } catch (Throwable $exception) {
            report($exception);

            return $this->failGoogleLogin('Login Google gagal saat membuat atau menghubungkan akun.', [
                ...$this->googleEmailDebug($email),
                'stage' => 'find_or_create_user',
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        }

        if (! $user) {
            return $this->failGoogleLogin('Email Google belum memenuhi aturan login sementara. Gunakan email student.itera.ac.id yang mengandung NIM pola 1xx140 atau email if.itera.ac.id.', [
                ...$this->googleEmailDebug($email),
                'stage' => 'email_rule',
            ]);
        }

        if (Schema::hasColumn('users', 'is_active') && ! $user->is_active) {
            return $this->failGoogleLogin('Akun ini sedang nonaktif. Hubungi admin HMIF.', [
                ...$this->googleEmailDebug($email),
                'stage' => 'inactive_user',
                'user_id' => $user->id,
            ]);
        }

        try {
            $this->syncGoogleProfile($user, $googleUser);
        } catch (Throwable $exception) {
            report($exception);

            return $this->failGoogleLogin('Login Google gagal saat menyimpan profil Google.', [
                ...$this->googleEmailDebug($email),
                'stage' => 'sync_google_profile',
                'user_id' => $user->id,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        }

        return AuthSession::login($request, $user);
    }

    private function failGoogleLogin(string $message, array $debug = []): RedirectResponse
    {
        $redirect = redirect()->route('login')
            ->withErrors(['identifier' => $message]);

        if (! app()->environment(['local', 'testing'])) {
            return $redirect;
        }

        return $redirect->with('google_login_debug', [
            ...$debug,
            'app_url' => config('app.url'),
            'login_url' => route('login'),
        ]);
    }

    private function googleEmailDebug(string $email): array
    {
        [$localPart, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $parsedNim = $this->allowedStudentNim($email);

        return [
            'email' => $email,
            'local_part' => $localPart,
            'domain' => $domain,
            'parsed_nim' => $parsedNim,
            'is_student_domain' => $domain === 'student.itera.ac.id',
            'is_if_domain' => $this->isIfIteraEmail($email),
            'passes_student_rule' => $parsedNim !== null,
            'passes_if_rule' => $this->isIfIteraEmail($email),
            'expected_student_format' => 'nama.1xx140xxx@student.itera.ac.id',
        ];
    }

    private function findOrCreateAllowedUser(GoogleUser $googleUser, string $email): ?User
    {
        $user = $this->findRegisteredUser($googleUser, $email);

        if ($user) {
            return $user;
        }

        return $this->createAllowedGoogleUser($googleUser, $email);
    }

    private function findRegisteredUser(GoogleUser $googleUser, string $email): ?User
    {
        $googleId = (string) $googleUser->getId();

        if ($googleId !== '' && Schema::hasColumn('users', 'google_id')) {
            $user = User::query()->where('google_id', $googleId)->first();

            if ($user) {
                return $user;
            }
        }

        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user) {
            return $user;
        }

        $nim = $this->allowedStudentNim($email);

        if ($nim && Schema::hasColumn('users', 'nim')) {
            return User::query()->where('nim', $nim)->first();
        }

        return null;
    }

    private function createAllowedGoogleUser(GoogleUser $googleUser, string $email): ?User
    {
        $nim = $this->allowedStudentNim($email);

        if (! $nim && ! $this->isIfIteraEmail($email)) {
            return null;
        }

        $user = new User;
        $user->forceFill([
            'name' => $this->googleDisplayName($googleUser, $email),
            'email' => $email,
            'password' => Str::random(48),
            'role' => 'member',
            'email_verified_at' => now(),
        ]);

        if ($nim && Schema::hasColumn('users', 'nim')) {
            $user->nim = $nim;
        }

        if (Schema::hasColumn('users', 'is_active')) {
            $user->is_active = true;
        }

        if (Schema::hasColumn('users', 'google_id') && (string) $googleUser->getId() !== '') {
            $user->google_id = (string) $googleUser->getId();
        }

        if (Schema::hasColumn('users', 'google_avatar')) {
            $user->google_avatar = $googleUser->getAvatar();
        }

        $user->save();

        return $user;
    }

    private function allowedStudentNim(string $email): ?string
    {
        [$localPart, $domain] = array_pad(explode('@', $email, 2), 2, '');

        if ($domain !== 'student.itera.ac.id') {
            return null;
        }

        if (preg_match('/(?:^|\D)(1\d{2}140\d+)(?:\D|$)/', $localPart, $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }

    private function isIfIteraEmail(string $email): bool
    {
        return Str::endsWith($email, '@if.itera.ac.id');
    }

    private function googleDisplayName(GoogleUser $googleUser, string $email): string
    {
        $name = trim((string) $googleUser->getName());

        if ($name !== '') {
            return $name;
        }

        [$localPart] = explode('@', $email, 2);

        return Str::headline(str_replace(['.', '_', '-'], ' ', $localPart));
    }

    private function syncGoogleProfile(User $user, GoogleUser $googleUser): void
    {
        $updates = [];
        $googleId = (string) $googleUser->getId();
        $avatar = $googleUser->getAvatar();

        if ($googleId !== '' && Schema::hasColumn('users', 'google_id') && $user->google_id !== $googleId) {
            $updates['google_id'] = $googleId;
        }

        if ($avatar && Schema::hasColumn('users', 'google_avatar') && $user->google_avatar !== $avatar) {
            $updates['google_avatar'] = $avatar;
        }

        if (! $user->email_verified_at) {
            $updates['email_verified_at'] = now();
        }

        if ($updates !== []) {
            $user->forceFill($updates)->save();
        }
    }
}
