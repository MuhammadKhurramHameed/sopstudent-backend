<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class programregistration extends Model
{
    use HasFactory;
    
    protected $table='programregistrations';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable=[
        'provinceId', 'districtId', 'gradeId', 'programId', 'batchId', 'email'
    ];
}
