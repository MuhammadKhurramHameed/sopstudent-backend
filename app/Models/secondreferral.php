<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class secondreferral extends Model
{
    use HasFactory;

    protected $table = 'secondreferrals';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = ['referralCode', 'parentReferralCode', 'userId'];
}
