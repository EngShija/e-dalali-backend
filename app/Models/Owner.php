<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'bank_account_number',
        'mobile_money_number',
        'debt_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
    
    public function debts()
    {
        return $this->hasMany(Debt::class);
    }
}