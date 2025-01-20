<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

use Session;

use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::all();
        return view('product',['product'=>$product]);
    }

    public function import_excel(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx',
        ]);

        $file = $request->file('file');

        $nama_file = rand().$file->getClientOriginalName();

        $file->move('file_product', $nama_file);

        $import = new ProductImport;
        $importedData = Excel::toArray($import, public_path('/file_product/'.$nama_file));

        foreach ($importedData[0] as $row) {
            $kode_produk = $row['1'];

            $existingProduct = Product::where('kode_produk', $kode_produk)->first();

            if (!$existingProduct) {
                $newProduct = new Product([
                    'nama_produk' => $row[0],
                    'kode_produk' => $kode_produk,
                    'harga_produk' => $row[2],
                    'frac' => $row[3]
                ]);

                $newProduct->save();
            } else {
                Session::flash('warning', 'Data dengan kode_produk '.$kode_produk.' sudah pernah diupload.');
            }
        }

        Session::flash('sukses', 'Data Product Berhasil Diimport!');

        return redirect('/product');
    }
}
