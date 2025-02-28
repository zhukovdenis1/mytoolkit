<?php

namespace App\Modules\Auth\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Http\Requests\Shared\LoginRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use App\Modules\Auth\Services\Shared\AuthService;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromFile;
use Illuminate\Http\Request;

#[Group('Auth')]
class AuthController extends Controller
{
    /**
     * Login: get a JWT via given credentials.
     *
     * @unauthenticated
     * @param LoginRequest $request
     * @param AuthService $authService
     * @return JsonResponse
     */
    #[ResponseFromFile('scribe/422.json', status: 422, description: 'Validation error')]
    #[Response(content: '{"error": "Invalid credentials"}', status: 401)]
    public function login(LoginRequest $request, AuthService $authService): JsonResponse
    {
        return $this->response($authService->login($request->validated()));
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    #[ResponseFromFile('scribe/401.json', status: 401, description: 'Unauthenticated')]
    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @param Request $request
     * @param AuthService $authService
     * @return JsonResponse
     */
    #[Response(content: '{"error": "Different text"}', status: 401)]
    #[ResponseFromFile('scribe/422.json', status: 422, description: 'Validation error')]
    #[Response(content: '{"message": "Successfully logged out"}', status: 200)]
    public function logout(Request $request, AuthService $authService): JsonResponse
    {
        try {
            return $this->response($authService->logout(
                $request->input('refresh_token'),
                $request->bearerToken(),
            ));
        } catch (AuthenticationException $exception) {
            return $this->errorResponse($exception->getMessage(), 401);
        }
    }

    /**
     * Refresh a token.
     *
     * @param Request $request
     * @param AuthService $authService
     * @return JsonResponse
     */
    #[ResponseFromFile('scribe/422.json', status: 422, description: 'Validation error')]
    #[Response(content: '{"error": "Different text"}', status: 401)]
    public function refresh(Request $request, AuthService $authService): JsonResponse
    {
        try {
            return $this->response(
                $authService->refresh(
                    $request->input('refresh_token'),
                    $request->bearerToken(),
                ));
        } catch (AuthenticationException $exception) {
            return $this->errorResponse($exception->getMessage(), 401);
        }
    }

    public function users(Request $request, AuthService $authService): JsonResponse
    {
        $products = User::all();

        // Return Json Response
        return response()->json([
            'users' => $products
        ], 200);
    }
}
