<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CustomersExport implements FromCollection, WithHeadings, WithStyles
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
            'Mobile',
            'Address',
            'IP Address',
            'OS Name',
            'Browser Name',
            'Browser Version',
            'Status',          
            'Registered On'            
        ];
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {        
        // search words
        $search = array("CR0000", "CR000", "CR00", "CR0", "CR");
        // DB::enableQueryLog();
        $customers = \App\Models\Customer::select('customers.customer_unique_id', 'customers.first_name', 'customers.last_name',
                     'customers.email','customers.mobile',DB::raw('CONCAT(ca.address_1, " ", ca.address_2," ",ca.city," ",ca.state," ",ca.country," ",ca.pincode ) AS address')
                     ,'customers.ip_address','customers.os_name','customers.browser_name','customers.browser_version'
                     ,DB::raw('(CASE WHEN is_active = 1 THEN "Active" END) as status'),'customers.created_at')
                    ->leftjoin('customer_address as ca','ca.customer_id','=','customers.id')
                    // ->where('is_active','=', 1)
                    ->orderBy("customers.id", "desc")
                    ->get()
                    ->map(function ($customer) use ($search){
                        $customer->customer_unique_id = str_replace($search, '', $customer->customer_unique_id);        
                        return $customer;
                    });  
                    // dd(DB::getQueryLog());
                    // dd($customers);
        return $customers;
    }
}
