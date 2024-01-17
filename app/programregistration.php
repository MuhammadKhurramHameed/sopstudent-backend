<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class programregistration extends Model
{
    protected $table='programregistrations';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable=[
        'provinceId', 'districtId', 'gradeId', 'programId', 'batchId', 'email'
    ];

}
