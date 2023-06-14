<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
    use HasFactory;

    protected $fillable = [

        'phone',
        'otp',
        'validUntil'
    ];

public function User(){
    return $this->belongsTo(User::class);
}
}
