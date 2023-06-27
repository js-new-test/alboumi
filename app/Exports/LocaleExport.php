<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class LocaleExport implements FromCollection, WithHeadings, WithStyles
{
    protected $id;

    function __construct($id) {
        $this->id = $id;
    }

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
            'Code',           
            'Value',
            'Langauge_Id',                   
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $locale = \App\Models\Locale::select('locale.code', 'locale_details.value'
        ,'locale_details.language_id')
        /*->leftJoin('locale_details', 'locale_details.locale_id', '=', 'locale.id')*/
        ->leftJoin('locale_details', function($join){
            $join->on('locale_details.locale_id', '=', 'locale.id')
                 ->on('locale_details.language_id', '=', DB::raw($this->id));
        })
        ->where('locale.is_active', 0)
        /*->where('locale_details.language_id', $this->id)*/
        ->get();
        return $locale;
    }
}
