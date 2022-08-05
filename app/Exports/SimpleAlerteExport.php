<?php

namespace App\Exports;

use App\Models\SimpleAlerte as Model;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;

class SimpleAlerteExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithProperties
{
    public function collection()
    {
        return Model::select('user_id', 'latitude','longitude','code','created_at', 'updated_at')->get();
    }
    public function headings(): array
    {
        return [
            'Name', 'Latitude','Longitude','Code', 'Simple alert date', 'Closing date'
        ];
    }
    public function map($users): array
    {
        // This example will return 3 rows.
        // First row will have 2 column, the next 2 will have 1 column
        return [
            $users->user->name,
            $users->latitude,
            $users->longitude,
            $users->code,
            $users->created_at,
            $users->updated_at,
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
            'A' => 30,
            'B' => 10,
            'C' => 10,
            'D' => 10,
            'E' => 20,
            'F' => 20,
        ];
    }
}
