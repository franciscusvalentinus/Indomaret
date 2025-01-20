<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
            'nama_produk' => $row[0],
            'kode_produk' => $row[1],
            'harga_produk' => $row[2],
            'frac' => $row[3],
        ]);
    }
}
