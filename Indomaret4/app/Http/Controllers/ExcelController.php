<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;

class ExcelController extends Controller
{
    public function showExcel()
    {
        return view('excel.show');
    }

    public function processExcel(Request $request)
    {
        $file = $request->file('excel_file');

        $data = Excel::toArray(new class implements ToArray {
            public function array(array $array)
            {
                return $array;
            }
        }, $file);

        return view('excel.show', ['data' => $data]);
    }
}
