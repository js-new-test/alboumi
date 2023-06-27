<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Auth;

class UsersExport implements FromCollection, WithHeadings, WithStyles
{
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],

            // Styling a specific cell by coordinate.
            // 'B2' => ['font' => ['italic' => true]],

            // Styling an entire column.
            // 'C'  => ['font' => ['size' => 16]],
        ];
    }
    
    public function headings(): array
    {
        return [
            'ID',
            'Firstname',
            'Lastname',
            'Email',
            'Role Title',            
        ];
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $parent_id = Auth::guard('admin')->user()->id;
        $users = \App\Models\User::select('users.id', 'users.firstname', 'users.lastname', 'users.email','roles.role_title')
            ->where('users.parent_id', '=', $parent_id)
            ->join('role_user', 'users.id', '=', 'role_user.admin_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('users.is_deleted', '!=', 1)
            ->orderBy('users.id','desc')
            ->get();
        return $users;
    }
}
