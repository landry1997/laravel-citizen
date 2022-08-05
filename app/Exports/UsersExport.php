<?php

namespace App\Exports;

use App\Models\User as Model;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithProperties
{

    public function collection()
    {
        return Model::select('name', 'lastname','email','phone','role_id','created_at', 'ville', 'region')->where([['name', '<>', Null],['role_id', '<>', Null],['lastname', '<>', Null],['ville', '<>', Null],['region', '<>', Null]])->get();
    }
    public function headings(): array
    {
        return [
            'Name', 'Lastname','Email','Phone', 'Role', 'Inscription date', 'Region', 'City'
        ];
    }
    public function map($users): array
    {
        // This example will return 3 rows.
        // First row will have 2 column, the next 2 will have 1 column
        return [
            $users->name,
            $users->lastname,
            $users->email,
            $users->phone,
            $users->role->name,
            $users->created_at,
            $users->regions->nom,
            $users->villes->nom,
        ];
    }
    public function properties(): array
    {
        return [
            'creator'        => 'SMARTCODE GROUP',
            'company'        => 'COMAID',
            'description'    => 'liste de tous les utilisateurs de la plate-forme',
            'subject'        => 'les utilisateurs de la plate-forme',
        ];
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A:H')->getAlignment()->setWrapText(true);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 10,
            'E' => 10,
            'F' => 10,
            'G' => 15,
            'H' => 15,
        ];
    }
}
