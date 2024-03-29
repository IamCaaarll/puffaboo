<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $table = 't_sales';
    protected $primaryKey = 'sale_id';
    protected $guarded = [];

    public function member()
    {
        return $this->hasOne(Member::class, 'member_id', 'member_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    
    public function salesDetails()
    {
        return $this->hasMany(SalesDetail::class, 'sale_id', 'sale_id');
    }
}
