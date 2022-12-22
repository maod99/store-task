<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    protected $fillable = ['user_id' ,'store_id' , 'amount'];

    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class , 'store_id');
    }
}
