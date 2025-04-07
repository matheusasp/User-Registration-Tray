<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendRegistrationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @param EmailService $emailService
     * @return void
     */
    public function handle(EmailService $emailService)
    {
        try {
            $emailService->sendRegistrationEmail($this->user);
            Log::info('Registration email sent to user: ' . $this->user->id);
        } catch (\Exception $e) {
            Log::error('Failed to send registration email: ' . $e->getMessage());
            $this->fail($e);
        }
    }

    public function getUser()
    {
        return $this->user;
    }
}