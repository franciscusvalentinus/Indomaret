<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User2;

use Session;

use App\Imports\UserImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        $user = User2::all();
        return view('user',['user'=>$user]);
    }

    public function import_excel(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $file = $request->file('file');

        $nama_file = rand().$file->getClientOriginalName();

        $file->move('file_user',$nama_file);

        Excel::import(new UserImport, public_path('/file_user/'.$nama_file));

        Session::flash('sukses','Data User Berhasil Diimport!');

        return redirect('/user');
    }
}
