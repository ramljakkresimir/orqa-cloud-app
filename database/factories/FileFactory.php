<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends Factory<File>
 */
class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition(): array
    {
        $content = "ORQA Cloud test document.\n";

        return [
            'user_id' => User::factory(),
            'original_name' => fake()->word() . '.txt',
            'disk' => 'cloud',
            'storage_path' => 'pending-' . Str::uuid() . '.txt',
            'mime_type' => 'text/plain',
            'extension' => 'txt',
            'size_bytes' => strlen($content),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (File $file) {
            $path = 'users/'.$file->user_id.'/'
                . Str::uuid().'.txt';

            $file->update([
                'disk' => 'cloud',
                'storage_path' => $path,
            ]);

            Storage::disk('cloud')->put(
                $path,
                "ORQA Cloud test document.\n"
            );
        });
    }
}