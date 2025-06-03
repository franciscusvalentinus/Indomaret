<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function MemberView(){
        ini_set('memory_limit', '-1');
        $memberFormat="";
        $MemberBinaan = \DB::table('master_toko')
            ->Get();

        foreach($MemberBinaan as $index => $row) {
            $memberFormat .= "<option style='font-size: 12px;width:500px;' value='" . $row->id . "'>" . $row->kode_member . " --> " . $row->nama . "</option>";
        }

        return $memberFormat;
    }
}
