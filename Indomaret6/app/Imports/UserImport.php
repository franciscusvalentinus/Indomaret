<?php

namespace App\Imports;

use App\Models\User2;
use Maatwebsite\Excel\Concerns\ToModel;

class UserImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User2([
            'username' => $row[0],
            'kode_member' => $row[1],
            'email' => $row[2],
            'no_telepon' => $row[3],
        ]);
    }
}
