<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBTag extends Model
{
    protected $table = "tags";
    protected $fillable =
    [
        'id',
        'tags',
        'can_sell',
        'can_order',
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
        return $this->hasMany('App\Models\TDBProduct','tag_id');
    }
}
