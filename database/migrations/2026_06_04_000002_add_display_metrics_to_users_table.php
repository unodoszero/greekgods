<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->text('height_value')->nullable()->after('height');
            $table->text('height_unit')->nullable()->after('height_value');
            $table->text('weight_value')->nullable()->after('weight');
            $table->text('weight_unit')->nullable()->after('weight_value');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['height_value', 'height_unit', 'weight_value', 'weight_unit']);
        });
    }
};
