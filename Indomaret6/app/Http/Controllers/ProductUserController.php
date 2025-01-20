<?php

namespace App\Http\Controllers;

use App\Imports\ProductUserImport;
use App\Models\Product;
use App\Models\ProductUser;
use App\Models\User2;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;
use Session;

use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

class ProductUserController extends Controller
{
    public function index()
    {
        $productuser = ProductUser::all();
        return view('productuser',['productuser'=>$productuser]);
    }

    public function import_excel(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('file');
            $nama_file = rand() . $file->getClientOriginalName();
            $file->move('file_productuser', $nama_file);

            $file_path = public_path('/file_productuser/' . $nama_file);
            $handle = fopen($file_path, "r");

            $dataToInsert = [];

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $kode_member = $data[0];
                $kode_produk = $data[1];

                $user = User2::where('kode_member', '=', $kode_member)->first();
                $product = Product::where('kode_produk', '=', $kode_produk)->first();

                if ($user && $product) {
                    $dataToInsert[] = [
                        'produk_id' => $product->id,
                        'user_id' => $user->id,
                        'kode_produk' => $product->kode_produk,
                        'nama_produk' => $product->nama_produk,
                        'harga_produk' => $product->harga_produk,
                        'harga_jual' => ($product->harga_produk * 25 / 100) + $product->harga_produk,
                    ];
                }
            }

            if (!empty($dataToInsert)) {
                ProductUser::insert($dataToInsert);
            }

            DB::commit();
            Session::flash('sukses', 'Data ProductUser Berhasil Diimport!');
        } catch (\Exception $e) {
            DB::rollback();
            dd('test');
            Session::flash('sukses', 'Error: ' . $e->getMessage());
        }

        return redirect('/productuser');
    }
}
