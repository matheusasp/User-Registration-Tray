<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Jobs\SendRegistrationEmail;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\EmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $userRepository;
    protected $emailService;

    /**
     * UserController constructor.
     *
     * @param UserRepository $userRepository
     * @param EmailService $emailService
     */
    public function __construct(
        UserRepository $userRepository,
        EmailService $emailService
    ) {
        $this->userRepository = $userRepository;
        $this->emailService = $emailService;
    }

    /**
     * Get list of users with filtering.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['name', 'cpf']);
            $perPage = $request->input('per_page', 15);
            
            $users = $this->userRepository->getFilteredUsers($filters, $perPage);
            
            return response()->json($users);
        } catch (\Exception $e) {
            Log::error('Failed to fetch users: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch users'], 500);
        }
    }

    /**
     * Complete user registration.
     *
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function completeRegistration(UserRequest $request): JsonResponse
    {

        try {
            $userId = $request->input('user_id');
            $user = $this->userRepository->findById($userId);
            
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $userData = $request->only(['name', 'birth_date', 'cpf']);
            $user = $this->userRepository->createOrUpdate($userData, $user);
            
            SendRegistrationEmail::dispatch($user);
            
            return response()->json([
                'success' => true,
                'user' => $user,
                'message' => 'Registration completed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to complete registration: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to complete registration'], 500);
        }
    }

    /**
     * Send registration email directly.
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function sendEmail(int $userId): JsonResponse
    {
        try {
            $user = $this->userRepository->findById($userId);
            
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            
            $result = $this->emailService->sendRegistrationEmail($user);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email sent successfully'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send email'], 500);
        }
    }
}