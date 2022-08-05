<?php

namespace App\Exports;

use App\Models\SuiviPosition as Model;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;

class PositionsExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithProperties
{
    public function __construct($suivi_id)
    {
        $this->suivi_id = $suivi_id;
    }
    public function collection()
    {
        return Model::where('demande_suivi_id', $this->suivi_id)->where('user_id','<>', Null)->get();
    }
    public function headings(): array
    {
        return [
            'Email', 'Latitude','Longitude','Lieu', 'Emission dATE', 'Suivi id'
        ];
    }
    public function map($users): array
    {
        // This example will return 3 rows.
        // First row will have 2 column, the next 2 will have 1 column
        return [
            $users->user->email,
            $users->latitude,
            $users->longitude,
            $users->nom_lieu,
            $users->created_at,
            $users->demande_suivi_id,
        ];
    }
    public function properties(): array
    {
        return [
            'creator'        => 'SMARTCODE GROUP',
            'company'        => 'COMAID',
            'description'    => 'liste de toutes les demandes de suivi',
            'subject'        => 'Demandes de suivi',
        ];
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A:F')->getAlignment()->setWrapText(true);
    }
    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 15,
            'C' => 15,
            'D' => 20,
            'E' => 20,
            'F' => 20,
        ];
    }
}
