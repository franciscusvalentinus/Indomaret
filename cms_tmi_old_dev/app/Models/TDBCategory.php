<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBCategory extends Model
{
    protected $table = "categories";
    protected $fillable =
    [
        'id',
        'category',
        'min_price',
        'max_price',
        'rec_price',
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
        return $this->hasMany('App\Models\TDBProduct','category_id');
    }
}
