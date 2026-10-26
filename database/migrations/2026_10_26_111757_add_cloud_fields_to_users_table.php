
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique();
            $table->string('surname')->nullable();
            $table->string('address')->nullable();

            // 3 GiB per user, expressed in bytes
            $table->unsignedBigInteger('quota_bytes')
                ->default(3221225472);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'surname',
                'address',
                'quota_bytes',
            ]);
        });
    }
};
