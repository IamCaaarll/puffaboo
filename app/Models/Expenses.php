<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    use HasFactory;

    protected $table = 't_expenses';
    protected $primaryKey = 'expense_id';
    protected $guarded = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id','branch_id');
    }
}
