<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Member extends Model
{
    protected $guarded = [];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function borrow(Book $book): Loan
    {
        return DB::transaction(function () use ($book) {
            $fresh = Book::lockForUpdate()->find($book->id);

            if ($fresh->onLoan()) {
                throw new DomainException("{$book->title} is already on loan.");
            }
            if ($this->loans()->overdue()->exists()) {
                throw new DomainException("{$this->name} has an overdue book.");
            }
            if ($this->loans()->active()->count() >= Loan::MAX_BOOKS) {
                throw new DomainException("{$this->name} reached the limit of " . Loan::MAX_BOOKS . " books.");
            }

            return $this->loans()->create([
                'book_id' => $book->id,
                'borrowed_on' => today(),
                'due_on' => today()->addDays(Loan::LOAN_DAYS),
            ]);
        });
    }
}
