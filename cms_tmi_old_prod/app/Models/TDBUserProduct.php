<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBUserProduct extends Model
{
    protected $table = "user_products";
    protected $fillable =
    [
        'id',
        'product_id',
        'parent_id',
        'user_id',
        'ownership_id',
        'plu',
        'description',
        'unit',
        'fraction',
        'stock',
        'min_stock',
        'max_stock',
        'price',
        'cost',
        'flag_freeplu',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }

    public function products()
    {
        return $this->belongsTo('App\Models\TDBProduct','product_id');
    }

    public function parentUserproducts()
    {
        return $this->belongsTo('App\Models\TDBUserProduct','parent_id');
    }

    public function users()
    {
        return $this->belongsTo('App\Models\TDBUser','user_id');
    }

    public function ownerships()
    {
        return $this->belongsTo('App\Models\TDBOwnership','ownership_id');
    }

    public function childUserProducts()
    {
        return $this->hasMany('App\Models\TDBUserProduct','parent_id');
    }

    public function userBarcodes()
    {
        return $this->hasMany('App\Models\TDBBarcode','user_product_id');
    }

    public function stockOpnames()
    {
        return $this->hasMany('App\Models\TDBStockOpname','user_product_id');
    }

    public function trxDetails()
    {
        return $this->hasMany('App\Models\TDBTrxDetail','user_product_id');
    }

    public function promotions()
    {
        return $this->hasMany('App\Models\TDBPromotion','user_product_id');
    }

    public function mutations()
    {
        return $this->hasMany('App\Models\TDBMutation','user_product_id');
    }

    public function scopeConnectForFreeplu($query, $toko, $cabang)
    {
        return $query->leftjoin('products','user_products.product_id','=','products.id')
            ->leftjoin('users','user_products.user_id','=','users.id')
            ->leftjoin('branches','users.branch_id','=','branches.id')
            ->leftjoin('categories','products.category_id','=','categories.id')
            ->join('trx_headers','trx_headers.user_id','=','users.id')
            ->join('trx_details', function ($join) {
                $join->on('trx_details.trx_header_id', '=', 'trx_headers.id');
                $join->on('trx_details.user_product_id', '=', 'user_products.id');
            })
            ->where('users.id','LIKE',$toko)
            ->where('branches.id','LIKE',$cabang);
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
}
