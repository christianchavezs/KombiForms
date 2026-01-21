<?php

namespace App\Exports;

use App\Models\RespuestasIndividuales;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RespuestasExport implements FromCollection, WithHeadings, WithStyles
{
    protected $formularioId;

    public function __construct($formularioId)
    {
        $this->formularioId = $formularioId;
    }

    // Datos que se exportan
    public function collection()
    {
        return RespuestasIndividuales::whereHas('respuesta', function ($q) {
            $q->where('formulario_id', $this->formularioId);
        })->get(['respuesta_id','pregunta_id','opcion_id','texto_respuesta','valor_numerico','valor_fecha','valor_hora','creado_en']);
    }

    // Encabezados bonitos
    public function headings(): array
    {
        return [
            'ID Respuesta',
            'Pregunta',
            'Opción',
            'Texto',
            'Valor Numérico',
            'Fecha',
            'Hora',
            'Fecha de creación'
        ];
    }

    // Estilos
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '4F81BD']]
            ],
        ];
    }
}