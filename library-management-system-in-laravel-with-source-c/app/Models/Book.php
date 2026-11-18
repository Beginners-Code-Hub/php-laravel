<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $guarded = [];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function activeLoan()
    {
        return $this->hasOne(Loan::class)->whereNull('returned_on');
    }

    public function onLoan(): bool
    {
        return $this->activeLoan()->exists();
    }
}
