<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class parentReferral extends Model
{
    use HasFactory;
    protected $table = 'parentReferrals';
    const CREATED_AT = 'createAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = ['referralCode', 'userId'];
}
