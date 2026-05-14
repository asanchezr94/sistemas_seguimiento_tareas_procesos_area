<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE tasks MODIFY status ENUM(
            'pendiente',
            'en proceso',
            'escalamiento',
            'terminado'
        ) NOT NULL DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tasks MODIFY status ENUM(
            'pendiente',
            'en proceso',
            'terminado'
        ) NOT NULL DEFAULT 'pendiente'");
    }
};
