<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarginDetail extends Model
{

    protected $table = "margin_details";
    protected $fillable = ['margin_id', 'kode_igr'];
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_cms');
        parent::__construct($attributes);
    }
}
