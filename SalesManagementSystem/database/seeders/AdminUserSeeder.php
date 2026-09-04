<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'System Admin',
                'email'    => 'admin@crm360.com',
                'password' => Hash::make('Admin@123'),
                'role'     => User::ROLE_ADMIN,
                'status'   => User::STATUS_ACTIVE,
                'phone'    => '+1 555-000-0001',
            ],
            [
                'name'     => 'Sarah Manager',
                'email'    => 'manager@crm360.com',
                'password' => Hash::make('Manager@123'),
                'role'     => User::ROLE_MANAGER,
                'status'   => User::STATUS_ACTIVE,
                'phone'    => '+1 555-000-0002',
            ],
            [
                'name'     => 'John Sales',
                'email'    => 'sales@crm360.com',
                'password' => Hash::make('Sales@123'),
                'role'     => User::ROLE_SALES_EXECUTIVE,
                'status'   => User::STATUS_ACTIVE,
                'phone'    => '+1 555-000-0003',
            ],
            [
                'name'     => 'Emma Support',
                'email'    => 'support@crm360.com',
                'password' => Hash::make('Support@123'),
                'role'     => User::ROLE_SUPPORT_AGENT,
                'status'   => User::STATUS_ACTIVE,
                'phone'    => '+1 555-000-0004',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('Demo users seeded:');
        $this->command->table(
            ['Name', 'Email', 'Password', 'Role'],
            [
                ['System Admin',  'admin@crm360.com',   'Admin@123',   'admin'],
                ['Sarah Manager', 'manager@crm360.com', 'Manager@123', 'manager'],
                ['John Sales',    'sales@crm360.com',   'Sales@123',   'sales_executive'],
                ['Emma Support',  'support@crm360.com', 'Support@123', 'support_agent'],
            ]
        );
    }
}
