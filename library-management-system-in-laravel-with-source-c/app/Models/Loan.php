<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    const LOAN_DAYS = 14;
    const MAX_BOOKS = 3;
    const FINE_PER_DAY = 5;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'borrowed_on' => 'date',
            'due_on' => 'date',
            'returned_on' => 'date',
        ];
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('returned_on');
    }

    public function scopeOverdue($query)
    {
        return $query->active()->whereDate('due_on', '<', today());
    }

    public function daysLate(): int
    {
        $end = $this->returned_on ?? today();

        return max(0, (int) $this->due_on->diffInDays($end));
    }

    public function currentFine(): int
    {
        return $this->daysLate() * self::FINE_PER_DAY;
    }

    public function giveBack(): self
    {
        $this->update([
            'returned_on' => today(),
            'fine' => $this->currentFine(),
        ]);

        return $this;
    }
}
