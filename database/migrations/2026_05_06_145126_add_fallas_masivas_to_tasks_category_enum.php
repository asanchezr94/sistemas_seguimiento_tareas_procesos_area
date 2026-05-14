<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE tasks MODIFY category ENUM(
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
            'soporte',
            'fallas masivas',
            'proyecto',
            'mejora preventiva',
            'automatizacion'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tasks MODIFY category ENUM(
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
            'soporte',
            'proyecto',
            'mejora preventiva',
            'automatizacion'
        ) NOT NULL");
    }
};
