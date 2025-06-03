<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBOwnership extends Model
{
    protected $table = "ownerships";
    protected $fillable =
    [
        'id',
        'ownership',
        'created_at',
        'updated_at'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }
    
    public function userProducts()
    {
        return $this->hasMany('App\Models\TDBUserProduct','ownership_id');
    }
}
