<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\GoogleLoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Google\Client as GoogleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    private function findOrCreateGoogleUser(string $googleId, string $email, string $name): User
    {
        $user = User::where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        if (!$user) {
            return User::create([
                'name' => $name,
                'email' => $email,
                'google_id' => $googleId,
                'password' => Str::random(40),
                'role' => 'hoc_sinh',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        if (!$user->google_id) {
            $user->google_id = $googleId;
        }

        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
        }

        if ($user->name !== $name) {
            $user->name = $name;
        }

        $user->save();

        return $user;
    }

    private function issueAuthToken(User $user): string
    {
        $user->tokens()->delete();
        return $user->createToken('auth_token')->plainTextToken;
    }

    /**
     * Đăng ký tài khoản (chỉ cho học sinh)
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'role' => 'hoc_sinh',
                'is_active' => true,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ], 201);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi hệ thống, vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Đăng nhập
     */
    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email hoặc mật khẩu không chính xác',
                ], 401);
            }

            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tài khoản của bạn đã bị vô hiệu hóa',
                ], 403);
            }

            // Xoá token cũ để tránh tích lũy
            $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi hệ thống, vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Dang nhap bang Google (SPA sends Google ID token)
     */
    public function googleLogin(GoogleLoginRequest $request)
    {
        try {
            $clientId = config('services.google.client_id');
            if (!$clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google login chua duoc cau hinh tren server.',
                ], 500);
            }

            $googleClient = new GoogleClient(['client_id' => $clientId]);
            $payload = $googleClient->verifyIdToken($request->credential);

            if (!$payload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google token khong hop le.',
                ], 401);
            }

            $googleId = (string) ($payload['sub'] ?? '');
            $email = strtolower(trim((string) ($payload['email'] ?? '')));
            $name = trim((string) ($payload['name'] ?? 'Nguoi dung Google'));

            if (!$googleId || !$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Khong the lay thong tin tai khoan Google.',
                ], 422);
            }

            $user = $this->findOrCreateGoogleUser($googleId, $email, $name);

            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tai khoan cua ban da bi vo hieu hoa',
                ], 403);
            }

            $token = $this->issueAuthToken($user);

            return response()->json([
                'success' => true,
                'message' => 'Dang nhap Google thanh cong',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ], 200);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Da xay ra loi he thong, vui long thu lai.',
            ], 500);
        }
    }

    /**
     * Redirect user to Google OAuth consent screen.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    /**
     * Handle Google callback and redirect back to FE with Sanctum token.
     */
    public function handleGoogleCallback()
    {
        $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/');

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $googleId = (string) $googleUser->getId();
            $email = strtolower(trim((string) $googleUser->getEmail()));
            $name = trim((string) ($googleUser->getName() ?: 'Nguoi dung Google'));

            if (!$googleId || !$email) {
                return redirect()->away($frontendUrl . '/login?error=google_missing_profile');
            }

            $user = $this->findOrCreateGoogleUser($googleId, $email, $name);
            if (!$user->is_active) {
                return redirect()->away($frontendUrl . '/login?error=account_disabled');
            }

            $token = $this->issueAuthToken($user);
            $encodedUser = rtrim(strtr(base64_encode(json_encode([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ], JSON_UNESCAPED_UNICODE)), '+/', '-_'), '=');

            $query = http_build_query([
                'token' => $token,
                'user' => $encodedUser,
            ]);

            return redirect()->away($frontendUrl . '/auth/google/callback?' . $query);
        } catch (\Throwable $e) {
            report($e);
            return redirect()->away($frontendUrl . '/login?error=google_oauth_failed');
        }
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chưa xác thực.',
                ], 401);
            }

            $currentToken = $user->currentAccessToken();
            if ($currentToken) {
                $currentToken->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Đăng xuất thành công',
            ], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi hệ thống, vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Lấy thông tin người dùng hiện tại
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'isActive' => $user->is_active,
                'createdAt' => $user->created_at,
            ],
        ], 200);
    }
}
