<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Margin extends Model
{

    protected $table = "master_margin";
    protected $fillable = ['kode_tmi', 'kode_mrg', 'flag_cab', 'div', 'dep', 'kat', 'margin_min', 'margin_saran', 'margin_max'];
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_cms');
        parent::__construct($attributes);
    }


}
