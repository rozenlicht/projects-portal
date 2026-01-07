<?php

namespace App\Console\Commands;

use App\Models\Group;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;

class CreateAdministrator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interactively create an Administrator account';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Creating a new Administrator account...');
        $this->newLine();

        // Get name
        $name = $this->ask('Name');
        if (empty($name)) {
            $this->error('Name is required.');
            return Command::FAILURE;
        }

        // Get email
        $email = $this->ask('Email address');
        if (empty($email)) {
            $this->error('Email is required.');
            return Command::FAILURE;
        }

        // Validate email format and uniqueness
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            $this->error('Invalid email or email already exists.');
            foreach ($validator->errors()->all() as $error) {
                $this->line("  - {$error}");
            }
            return Command::FAILURE;
        }

        // Get password
        $password = $this->secret('Password');
        if (empty($password)) {
            $this->error('Password is required.');
            return Command::FAILURE;
        }

        // Confirm password
        $passwordConfirmation = $this->secret('Confirm password');
        if ($password !== $passwordConfirmation) {
            $this->error('Passwords do not match.');
            return Command::FAILURE;
        }

        // Validate password strength (minimum 8 characters)
        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters long.');
            return Command::FAILURE;
        }

        // Get optional group
        $group = null;
        $groups = Group::orderBy('name')->get();
        if ($groups->isNotEmpty()) {
            $this->newLine();
            $this->info('Available groups:');
            $groupChoices = ['None'];
            foreach ($groups as $g) {
                $groupChoices[] = $g->name;
            }
            
            $selectedGroup = $this->choice('Select a group (optional)', $groupChoices, 0);
            if ($selectedGroup !== 'None') {
                $group = $groups->firstWhere('name', $selectedGroup);
            }
        }

        // Create the user
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'group_id' => $group?->id,
            ]);

            // Assign Administrator role
            $administratorRole = Role::firstOrCreate(['name' => 'Administrator']);
            $user->assignRole($administratorRole);

            $this->newLine();
            $this->info('✓ Administrator account created successfully!');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Name', $user->name],
                    ['Email', $user->email],
                    ['Group', $user->group?->name ?? 'None'],
                    ['Role', 'Administrator'],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to create administrator account: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
