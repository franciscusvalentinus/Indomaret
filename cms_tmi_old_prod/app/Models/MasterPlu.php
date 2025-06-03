<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPlu extends Model
{
    protected $table = "master_plu";
    protected $fillable = ['mrg_id', 'kodeplu', 'hrg_jual', 'display', 'frac_tmi', 'unit_tmi', 'qty_min', 'qty_max', 'min_dis','frac_igr', 'unit_igr', 'min_jual', 'barcode', 'tag', 'long_desc'];
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_cms');
        parent::__construct($attributes);
    }

    public static function getPlu($type, $cab)
    {
/*
        $result = MasterPlu::Distinct()
            ->Select(
                'tipe_tmi.nama as tipe', 'margin_min', 'margin_max', 'margin_saran', 'kat_namakategori',
                \DB::raw('concat(substr(kodeplu, 1, 6), 0) as kodeplu'), 'long_description' ,'display','frac_tmi',
                'unit_tmi', 'tipe_tmi.kode_tmi as tipetmi')
//            ->Join('master_margins', 'master_plu.mrg_id', '=', 'master_margin.kode_mrg')
            ->leftJoin('master_margin', function ($join) {
                $join->on('master_plu.mrg_id', '=', 'master_margin.kode_mrg');
                $join->on('master_plu.kode_igr', '=', 'master_margin.kode_igr');
            })
            ->Join('products', function ($join) {
                $join->on(\DB::raw('substr(master_plu.kodeplu, 1, 6)'), '=', \DB::raw('substr(products.prdcd, 1, 6)'));
                $join->on('master_plu.kode_igr', '=', 'products.kode_igr');
            })
            ->leftJoin('tipe_tmi', 'master_margin.kode_tmi', '=', 'tipe_tmi.kode_tmi')
            ->leftJoin('divisi', 'div_kodedivisi', '=', 'div')
            ->leftJoin('department', function ($join) {
                $join->on('master_margin.dep', '=', 'department.dep_kodedepartement');
                $join->on('divisi.div_kodedivisi', '=', 'department.dep_kodedivisi');
            })
            ->leftJoin('category', function ($join) {
                $join->on('master_margin.kat', '=', 'category.kat_kodekategori');
                $join->on('department.dep_kodedepartement', '=', 'category.kat_kodedepartement');
            })
            ->Where('tipe_tmi.kode_tmi', $type)
            ->Where('master_margin.kode_igr', $cab)
            ->get();
*/
        $result = MasterPlu::Select(
//            'tipe_tmi.nama as tipe', 'margin_min', 'margin_max', 'margin_saran', 'kat_namakategori',
//            \DB::raw('concat(substr(kodeplu, 1, 6), 0) as kodeplu'), 'long_description' ,'display','frac_tmi',
//            'unit_tmi', 'tipe_tmi.kode_tmi as tipetmi'
            'master_margin.kode_tmi as tipetmi', 'category.kat_namakategori as kat_namakategori', 'master_plu.display', 'master_plu.kodeplu',
            'master_plu.long_desc as long_description', 'master_plu.frac_igr as frac_tmi', 'master_plu.unit_tmi', 'master_margin.margin_min',
            'master_margin.margin_saran'
        )
            ->join('master_margin', function ($join){
                $join->on('master_plu.mrg_id', '=', 'master_margin.kode_mrg');
                // $join->on('master_plu.kode_igr', '=', 'master_margin.kode_igr');
            })->join('category', function ($join){
                $join->on('master_margin.kat', '=', 'category.kat_kodekategori');
                $join->on('master_margin.dep', '=', 'category.kat_kodedepartement');
            });
        if($cab !== '00'){
            $result = $result->where('master_plu.kode_igr', '=', $cab);
        }
        if($type !== '%'){
            $result = $result->where('master_margin.kode_tmi', '=', $type);
        }
            $result = $result->groupBy('master_plu.kodeplu')
            ->get();

        return $result;
    }

}
