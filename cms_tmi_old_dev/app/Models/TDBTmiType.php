<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBTmiType extends Model
{
    protected $table = "tmi_types";
    protected $fillable =
    [
        'id',
        'code',
        'description',
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
        return $this->hasMany('App\Models\TDBProduct','tmi_type_id');
    }

    public function users()
    {
        return $this->hasMany('App\Models\TDBUser','tmi_type_id');
    }
}
