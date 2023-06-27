<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CustomersImport implements ToCollection
{   
    private $common = array();         

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {    
        $data = [];
        foreach ($rows as $row) 
        {
            if($row[0] == "First name" || $row[1] == "Last name" || $row[2] == "Gender" || $row[3] == "Email" || $row[4] == "Mobile" || $row[5] == "Password")
            {
                continue;
            }            
            $data[] = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5]);
        }

        $this->common = $data;                   
        
    }

    public function getCommon(): array
    {
        return $this->common;
    }
}
