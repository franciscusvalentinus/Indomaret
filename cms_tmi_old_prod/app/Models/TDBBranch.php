<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TDBBranch extends Model
{
    protected $table = "branches";
    protected $fillable =
    [
        'id',
        'code',
        'name',
        'created_at',
        'updated_at'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }


    public function products()
    {
        return $this->hasMany('App\Models\TDBProduct','branch_id');
    }

    public function users()
    {
        return $this->hasMany('App\Models\TDBUser','branch_id');
    }

    public function scopeConnectToHeader($query, $cabang, $toko, $kasir)
    {
        return $query->join('users','users.branch_id','=','branches.id')
                ->join('operators','operators.user_id','=','users.id')
                ->join('shift_reports','shift_reports.operator_id','=','operators.id')
                ->join('trx_headers','trx_headers.shift_id','=','shift_reports.id')
//                ->where(function($query) use($cabang){
//                    foreach($cabang as $item){
//                        $query->orWhere('branches.id', 'LIKE', $item);
//                    }
//                })
//                ->where(function($query) use($toko){
//                    foreach($toko as $item){
//                        $query->orWhere('users.id','LIKE',$item);
//                    }
//                })
                ->where('branches.id','LIKE',$cabang)
                ->where('users.id','LIKE',$toko);
//                ->where('operators.id','LIKE',$kasir);
    }

    public function scopeConnectToHeaderWOCashier($query, $cabang, $toko)
    {
//        dd($this->connection);
//        return $this->connection;
        return $query->join('users','users.branch_id','=','branches.id')
            ->join('trx_headers','trx_headers.user_id','=','users.id')
//                ->where(function($query) use($cabang){
//                    foreach($cabang as $item){
//                        $query->orWhere('branches.id', 'LIKE', $item);
//                    }
//                })
//                ->where(function($query) use($toko){
//                    foreach($toko as $item){
//                        $query->orWhere('users.id','LIKE',$item);
//                    }
//                })
            ->where('branches.id','LIKE',$cabang)
            ->where('users.id','LIKE',$toko);
    }
    public function scopeConnectToHeaderWithTmiType($query, $cabang, $toko)
{
    $data = $query->join('users','users.branch_id','=','branches.id')
        ->join('trx_headers','trx_headers.user_id','=','users.id')
        ->join('tmi_types','users.tmi_type_id','=','tmi_types.id')
//                ->where(function($query) use($cabang){
//                    foreach($cabang as $item){
//                        $query->orWhere('branches.id', 'LIKE', $item);
//                    }
//                })
//                ->where(function($query) use($toko){
//                    foreach($toko as $item){
//                        $query->orWhere('users.id','LIKE',$item);
//                    }
//                })
        ->where('branches.id','LIKE',$cabang)
        ->where('users.id','LIKE',$toko);
    return $data;
}

    public function scopeConnectDetailToDiv($query, $cabang, $toko)
    {
        return $query->join('users','users.branch_id','=','branches.id')
            ->join('products','products.branch_id','=','branches.id')
            ->join('categories','products.category_id','=','categories.id')
            ->join('trx_headers','trx_headers.user_id','=','users.id')
            ->join('user_products', function ($join) {
                $join->on('user_products.user_id','=','users.id');
                $join->on('user_products.product_id','=','products.id');
            })
            ->join('trx_details', function ($join) {
                $join->on('trx_details.trx_header_id', '=', 'trx_headers.id');
                $join->on('trx_details.user_product_id', '=', 'user_products.id');
            })
            ->where('branches.id','LIKE',$cabang)
            ->where('users.id','LIKE',$toko);
    //            ->where(function($query) use($cabang){
    //                foreach($cabang as $item){
    //                    $query->orWhere('branches.id', 'LIKE', $item);
    //                }
    //            })
    //            ->where(function($query) use($toko){
    //                foreach($toko as $item){
    //                    $query->orWhere('users.id','LIKE',$item);
    //                }
    //            });
    }

    public function scopeConnectUserProductToDiv($query, $cabang, $toko)
    {

        //QUERY TANGGAL 15 Juli 2020
        return $query->join('users','users.branch_id','=','branches.id')
            ->join('user_products', 'user_products.user_id', '=', 'users.id')
            ->leftJoin('products', 'user_products.product_id', '=', 'products.id')
//            ->join('products','products.branch_id','=','branches.id')
            ->leftJoin('categories','products.category_id','=','categories.id')
//            ->join('user_products', function ($join) {
//                $join->on('user_products.user_id','=','users.id');
//                $join->on('user_products.product_id','=','products.id');
//            })
            ->where('branches.id','LIKE',$cabang)
            ->where('users.id','LIKE',$toko);

        //QUERY LAMA
//        return $query->join('users','users.branch_id','=','branches.id')
//            ->join('products','products.branch_id','=','branches.id')
//            ->join('categories','products.category_id','=','categories.id')
//            ->join('user_products', function ($join) {
//                $join->on('user_products.user_id','=','users.id');
//                $join->on('user_products.product_id','=','products.id');
//            })
//            ->where('branches.id','LIKE',$cabang)
//            ->where('users.id','LIKE',$toko);

        //            ->where(function($query) use($cabang){
        //                foreach($cabang as $item){
        //                    $query->orWhere('branches.id', 'LIKE', $item);
        //                }
        //            })
        //            ->where(function($query) use($toko){
        //                foreach($toko as $item){
        //                    $query->orWhere('users.id','LIKE',$item);
        //                }
        //            });
    }

    public function scopeConnectToDetail($query, $cabang, $toko)
    {
        return $query->join('users','users.branch_id','=','branches.id')
            ->join('trx_headers','trx_headers.user_id','=','users.id')
            ->join('user_products','user_products.user_id','=','users.id')
            ->join('trx_details', function ($join) {
                $join->on('trx_details.trx_header_id', '=', 'trx_headers.id');
                $join->on('trx_details.user_product_id', '=', 'user_products.id');
            })
            ->where('branches.id','LIKE',$cabang)
            ->where('users.id','LIKE',$toko);
//            ->where(function($query) use($cabang){
//                foreach($cabang as $item){
//                    $query->orWhere('branches.id', 'LIKE', $item);
//                }
//            })
//            ->where(function($query) use($toko){
//                foreach($toko as $item){
//                    $query->orWhere('users.id','LIKE',$item);
//                }
//            });
    }

    public function scopeConnectToPb($query, $cabang, $toko)
    {
        return $query->join('users','users.branch_id','=','branches.id')
            ->join('pb_headers','pb_headers.user_id','=','users.id')
            ->join('pb_statuses','pb_headers.status_id','=','pb_statuses.id')
            ->where('branches.id','LIKE',$cabang)
            ->where('users.id','LIKE',$toko);
    }

    public function scopeConnectToUserProducts($query, $cabang, $toko)
    {
        return $query->join('users','users.branch_id','=','branches.id')
            ->join('user_products','user_products.user_id','=','users.id')
            ->where('branches.id','LIKE',$cabang)
            ->where('users.id','LIKE',$toko);
    }

    public function scopeConnectToPromotion($query, $cabang, $toko)
    {
        //select b.name as branch_name, b.code, u.name, u.member_code, u.store_name, up.plu, up.description, p.mechanism, p.start_date, p.end_date,
        //sum(d.sub_total) as sales
        //from promotions p
        //join user_products up on p.user_product_id = up.id
        //join users u on up.user_id = u.id
        //join branches b on u.branch_id = b.id
        //left join trx_details d on p.id = d.promotion_id
        //where date(p.start_date) >= '2020-10-01' and date(p.end_date) <= '2020-10-15'
        //group by p.id;
        $queries =  $query->join('users','users.branch_id','=','branches.id')
            ->join('user_products','user_products.user_id', '=', 'users.id')
            ->join('promotions','promotions.user_product_id','=','user_products.id')
//            ->leftJoin('trx_details', 'trx_details.user_product_id','=', 'user_products.id');
            ->leftJoin('trx_details', function($join){
////                $join->on('master_margin.dep', '=', 'department.DEP_KODEDEPARTEMENT');
////                $join->on('divisi.DIV_KODEDIVISI', '=', 'department.DEP_KODEDIVISI');
                $join->on('trx_details.user_product_id', '=', 'user_products.id');
                $join->on('trx_details.promotion_id', '=', 'promotions.id');
            });
        if($cabang != '%') {
            $queries = $queries->where('branches.id', 'LIKE', $cabang);
        }
        if($toko != '%'){
            $queries = $queries->where('users.id','LIKE',$toko);
        };
        $queries = $queries->groupBy('promotions.id');
        return $queries;

    }
}
