<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\UserInvited;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InviteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:invite {user_id : The ID of the user to invite}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually send an invitation email to an existing user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = $this->argument('user_id');

        // Find the user
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return Command::FAILURE;
        }

        // Generate invitation token
        $invitationToken = Str::random(64);

        // Update user with invitation token and timestamp
        $user->invitation_token = $invitationToken;
        $user->invitation_sent_at = now();
        $user->save();

        // Send invitation notification
        try {
            $user->notify(new UserInvited($invitationToken));

            $this->info("✓ Invitation email sent successfully to {$user->email}!");
            $this->table(
                ['Field', 'Value'],
                [
                    ['User ID', $user->id],
                    ['Name', $user->name],
                    ['Email', $user->email],
                    ['Invitation Token', $invitationToken],
                    ['Invitation Sent At', $user->invitation_sent_at->format('Y-m-d H:i:s')],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to send invitation email: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

