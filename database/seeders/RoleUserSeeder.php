<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\SiswaProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = collect(['admin', 'guru', 'siswa'])
            ->mapWithKeys(fn (string $name): array => [
                $name => Role::query()->firstOrCreate(['name' => $name]),
            ]);

        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        foreach ([
            'admin' => ['name' => 'Admin Nextask', 'email' => 'admin@nextask.test'],
            'guru' => ['name' => 'Guru Nextask', 'email' => 'guru@nextask.test'],
            'siswa' => ['name' => 'Siswa Nextask', 'email' => 'siswa@nextask.test'],
        ] as $roleName => $account) {
            $user = User::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'role_id' => $roles[$roleName]->getKey(),
                    'password' => Hash::make('password'),
                ],
            );

            if ($roleName === 'siswa') {
                SiswaProfile::query()->updateOrCreate(
                    ['user_id' => $user->getKey()],
                    ['nisn' => '1234567890'],
                );
            }
        }
    }
}
