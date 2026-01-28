<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Formulario;

class DesactivarFormularios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'formularios:desactivar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Desactiva automáticamente los formularios cuya fecha de cierre ya pasó';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $formularios = Formulario::where('activo', 1)
            ->whereNotNull('fecha_fin')
            ->where('fecha_fin', '<', now())
            ->get();

        foreach ($formularios as $formulario) {
            $formulario->update(['activo' => 0]);
            $this->info("Formulario {$formulario->id} desactivado (fecha de cierre: {$formulario->fecha_fin}).");
        }

        $this->info('Revisión completada. Formularios vencidos desactivados.');
    }
}