<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->command?->warn(
                'Demo data seeding is allowed only locally.'
            );

            return;
        }

        $password = 'Password123!';

        $users = [
            [
                'username' => 'superadmin',
                'name' => 'Super',
                'surname' => 'Administrator',
                'email' => 'superadmin@example.test',
                'roles' => ['super_admin'],
            ],
            [
                'username' => 'user1',
                'name' => 'Marko',
                'surname' => 'Markovic',
                'email' => 'user1@example.test',
                'roles' => ['user'],
            ],
            [
                'username' => 'user2',
                'name' => 'Ana',
                'surname' => 'Anic',
                'email' => 'user2@example.test',
                'roles' => ['user'],
            ],
            [
                'username' => 'multiuser',
                'name' => 'Ivan',
                'surname' => 'Ivic',
                'email' => 'multiuser@example.test',
                'roles' => ['user', 'admin'],
            ],
        ];

        foreach ($users as $data) {
            $roles = $data['roles'];

            unset($data['roles']);

            $user = User::updateOrCreate(
                ['username' => $data['username']],
                array_merge($data, [
                    'password' => $password,
                    'address' => 'Demo address',
                    'quota_bytes' => 3 * 1024 * 1024 * 1024,
                ])
            );

            $roleIds = Role::whereIn('name', $roles)
                ->pluck('id')
                ->all();

            $user->roles()->sync($roleIds);
        }

        $this->createDemoFiles(
            User::where('username', 'user1')->firstOrFail(),
            6
        );

        $this->createDemoFiles(
            User::where('username', 'user2')->firstOrFail(),
            2
        );
    }

    private function createDemoFiles(User $user, int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            $originalName = "document-{$i}.txt";
            $storagePath = "users/{$user->id}/demo-document-{$i}.txt";

            $content = "ORQA Cloud demo document {$i}\n";
            $content .= "Owner: {$user->username}\n";

            Storage::disk('cloud')->put($storagePath, $content);

            File::updateOrCreate(
                [
                    'storage_path' => $storagePath,
                ],
                [
                    'user_id' => $user->id,
                    'disk' => 'cloud',
                    'original_name' => $originalName,
                    'mime_type' => 'text/plain',
                    'extension' => 'txt',
                    'size_bytes' => strlen($content),
                ]
            );
        }
    }
}
