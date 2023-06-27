<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UsersImport implements ToCollection
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
            if($row[0] == "ID" || $row[1] == "Firstname" || $row[2] == "Lastname" || $row[3] == "Email" || $row[4] == "Role" || $row[5] == "Password")
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
