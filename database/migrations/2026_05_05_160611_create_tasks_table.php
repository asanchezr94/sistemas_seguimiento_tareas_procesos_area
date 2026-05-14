<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('user_name');
            $table->date('task_date')->default(DB::raw('CURRENT_DATE'));
            $table->string('task_name');
            $table->text('task_detail');
            $table->enum('category', [
                'opa',
                'visionamos',
                'app movil',
                'mesa de ayuda opa',
                'mesa de ayuda visionamos',
                'configuraciones',
                'proveedores',
                'tesoreria',
                'comercial',
                'bienestar',
                'credito',
                'cartera',
                'sistemas',
                'contabilidad',
                'riesgos',
                'gerencia',
            ]);
            $table->enum('priority', ['baja', 'media', 'alta']);
            $table->enum('status', ['pendiente', 'en proceso', 'terminado'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
