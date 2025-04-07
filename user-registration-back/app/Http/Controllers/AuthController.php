<?php

namespace App\Http\Controllers;

use App\Services\GoogleAuthService;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $googleAuthService;
    protected $userRepository;

    /**
     * AuthController constructor.
     *
     * @param GoogleAuthService $googleAuthService
     * @param UserRepository $userRepository
     */
    public function __construct(
        GoogleAuthService $googleAuthService,
        UserRepository $userRepository
    ) {
        $this->googleAuthService = $googleAuthService;
        $this->userRepository = $userRepository;
    }

    /**
     * Get Google OAuth URL.
     *
     * @return JsonResponse
     */
    public function getGoogleAuthUrl(): JsonResponse
    {
        try {
            $authUrl = $this->googleAuthService->getAuthUrl();
            return response()->json(['url' => $authUrl]);
        } catch (\Exception $e) {
            Log::error('Failed to get Google Auth URL: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate authentication URL'], 500);
        }
    }

    /**
     * Handle Google OAuth callback.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleGoogleCallback(Request $request): JsonResponse
    {
        try {
            if (!$request->has('code')) {
                return response()->json(['error' => 'Authorization code is missing'], 400);
            }

            $result = $this->googleAuthService->handleCallback($request->code);

            if (!$result['success']) {
                return response()->json(['error' => $result['message']], 400);
            }

            return response()->json([
                'success' => true,
                'user' => $result['user'],
                'message' => 'Authentication successful'
            ]);
        } catch (\Exception $e) {
            Log::error('Google callback error: ' . $e->getMessage());
            return response()->json(['error' => 'Authentication failed'], 500);
        }
    }
}