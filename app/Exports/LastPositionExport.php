<?php

namespace App\Exports;
use App\Models\SuiviPosition as Model;
use App\Models\DemandeSuivi as Models;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;

class LastPositionExport implements FromCollection
{
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }
    public function collection()
    {
        $detail = Models::where('user_id', $this->user_id)->OrderByDesc('id')->first();
        return Model::where('demande_suivi_id', $detail->code)->get();
    }
    public function headings(): array
    {
        return [
            'Name', 'Latitude','Longitude','Lieu', 'Emission dATE', 'Suivi id'
        ];
    }
    public function map($users): array
    {
        return [
            $users->user->name,
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
