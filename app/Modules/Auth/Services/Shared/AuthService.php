<?php

namespace App\Modules\Auth\Services\Shared;

use App\Modules\Auth\Models\Token;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Auth\AuthenticationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Support\Facades\Auth; // для web авторизации

class AuthService extends BaseService
{
    /**
     * @param array $credentials <email,password>
     * @return array
     * @throws AuthenticationException
     */
    public function login(array $credentials): array
    {
        if (!$accessToken = auth()->attempt($credentials)) {
            throw new AuthenticationException('Invalid credentials');
        }

        //Auth::guard('web')->login(auth()->user()); // для web авторизации

        $refreshToken = Str::random(64);

        Token::create([
            'refresh_token' => $refreshToken,
            'user_id' => auth()->id(),
            'access_token' => $accessToken,
            'expires_at' => Carbon::now()->addMinutes((int) config('jwt.refresh_ttl')),
            'created_at' => Carbon::now(),
        ]);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'access_token_type' => 'bearer',
            'access_token_expires_in' => auth()->factory()->getTTL() * 60,
            'user' => [
                'email' => $credentials['email'],
                'isActivated' => true,
                'id' => auth()->id(),
            ]
        ];
    }

    /**
     * @param string $refreshToken
     * @return string[]
     * @throws AuthenticationException
     */
    public function logout(string $refreshToken, string $accessToken): array
    {
        $userId = $this->getUserIdByToken($accessToken);

        if (!$this->isValidRefreshToken($refreshToken)) {
            $this->triggerMaliciousActivity($userId);
        }

        $token = Token::where('refresh_token', $refreshToken)
            //->lockForUpdate()
            ->first();

        if (!$token) {
            $this->triggerMaliciousActivity($userId);
        }

        $token->delete();
        //для того чтобы при повторном запросе на обнолвение refresh_token выкидывало ошибку
        //это излишне, достаточно вместо этого delete
//        Token::where('refresh_token', $refreshToken)->update([
//            'access_token' => '',
//        ]);


        //$this->finishCurrentSession();

        // Выход из веб-сессии
//        if ($userId) {
//            Auth::guard('web')->logout();
//        }

        return ['message' => 'Successfully logged out'];
    }

    /**
     * @param string $refreshToken
     * @param string $accessToken
     * @return array
     * @throws AuthenticationException
     */
    public function refresh(string $refreshToken, string $accessToken): array
    {
        $userId = $this->getUserIdByToken($accessToken);

        if (!$this->isValidRefreshToken($refreshToken)) {
            $this->triggerMaliciousActivity($userId);
        }

        $token = Token::where('refresh_token', $refreshToken)
            //->lockForUpdate()
            ->first();

        if (!$token) {
            $this->triggerMaliciousActivity($userId);
        }

        if ($token->access_token !== $accessToken || !$token->access_token) {
            $this->triggerMaliciousActivity($token->user_id);
        }

        if ($token->expires_at->isPast()) {
            throw new AuthenticationException('Refresh token expired.');
        }

        //для того чтобы при повторном запросе на обнолвение refresh_token выкидывало ошибку

        //Эт не нужно т.к. если refresh_token не найден - значит это запрос от злоумышленника и можно делать логаут со всех устройств
//        Token::where('refresh_token', $refreshToken)->update([
//            'access_token' => '',
//        ]);



        $newRefreshToken = Str::random(64);
        $newAccessToken = auth()->refresh(null, null, null);  // Получаем новый Access Token

        Token::create([
            'refresh_token' => $newRefreshToken,
            'user_id' => $token->user_id,
            'access_token' => $newAccessToken,
            'expires_at' => Carbon::now()->addMinutes((int) config('jwt.refresh_ttl')),
            'created_at' => Carbon::now(),
        ]);

        $token->delete();

        return [
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'access_token_type' => 'bearer',
            'access_token_expires_in' => auth()->factory()->getTTL() * 60,
        ];
    }

    /**
     * @return void
     */
    private function finishCurrentSession(): void
    {
        try {// Завершаем сессию и удаляем текущий токен из контекста (в blacklist)
            //auth()->logout(); //если WT_BLACKLIST_ENABLED == true
        } catch (\Tymon\JWTAuth\Exceptions\JWTException) {}
    }

    /**
     * @param int $userId
     * @return void
     */
    private function invalidateAllUserTokens(int $userId): void
    {
        //Token::where('user_id', $userId)->update(['expires_at' => DB::raw('created_at')]);
        Token::where('user_id', $userId)->delete();

        //$this->finishCurrentSession();
    }

    /**
     * @param string $accessToken
     * @return int|null
     */
    private function getUserIdByToken(string $accessToken): ?int
    {
        $userId = null;
        try {
            $payload = JWTAuth::setToken($accessToken)->getPayload();
            $userId = (int) $payload->get('sub');
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

        }

        return $userId;
    }

    /**
     * @param string $refreshToken
     * @return bool
     */
    private function isValidRefreshToken(string $refreshToken): bool
    {
        $validator = Validator::make(
            ['refresh_token' => $refreshToken],
            ['refresh_token' => 'required|string|size:64|regex:/^[A-Za-z0-9]+$/']
        );

        return !$validator->fails();
    }

    /**
     * @param int|null $userId
     * @return void
     * @throws AuthenticationException
     */
    private function triggerMaliciousActivity(?int $userId): void
    {
        if ($userId) {
            $this->invalidateAllUserTokens($userId);
            throw new AuthenticationException(
                'Suspicion of malicious activity detected. ' .
                'For security reasons, all accounts have been logged out'
            );
        } else {
            throw new AuthenticationException('Invalid access_token');
        }
    }
}

