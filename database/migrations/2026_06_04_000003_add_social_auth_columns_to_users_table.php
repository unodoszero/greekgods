<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('google_id')->nullable()->unique();
            $table->string('microsoft_id')->nullable()->unique();
            $table->text('avatar')->nullable();
            $table->string('provider', 32)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['google_id']);
            $table->dropUnique(['microsoft_id']);
            $table->dropColumn(['google_id', 'microsoft_id', 'avatar', 'provider']);
        });
    }
};
