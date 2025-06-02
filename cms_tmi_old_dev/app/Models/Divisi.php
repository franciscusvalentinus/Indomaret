<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $table = "divisi";
    protected $fillable = ['DIV_KODEIGR', 'DIV_KODEDIVISI', 'DIV_NAMADIVISI', 'DIV_DIVISIMANAGER', 'DIV_SINGKATANNAMADIVISI', 'DIV_CREATE_BY', 'DIV_CREATE_DT', 'DIV_MODIFY_BY', 'DIV_MODIFY_DT'];
    public $timestamps = false;
}
