<?php

namespace App\Imports;

use App\Models\ProductUser;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductUserImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ProductUser([
            'kode_member' => $row[0],
            'kode_produk' => $row[1],
        ]);
    }
}
