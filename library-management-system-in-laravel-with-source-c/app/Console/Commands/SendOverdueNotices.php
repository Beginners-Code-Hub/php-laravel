<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendOverdueNotices extends Command
{
    protected $signature = 'library:overdue-notices';

    protected $description = 'Email every member who has an overdue book';

    public function handle(): int
    {
        $loans = Loan::overdue()->with(['book', 'member'])->get();
        $sent = 0;

        foreach ($loans as $loan) {
            if (! $loan->member->email) {
                continue;
            }

            $text = "Hello {$loan->member->name}, \"{$loan->book->title}\" was due on "
                . $loan->due_on->format('M j') . ". Fine so far: {$loan->currentFine()}.";

            Mail::raw($text, fn ($m) => $m->to($loan->member->email)->subject('Overdue library book'));
            $sent++;
        }

        $this->info("Overdue notices sent: {$sent}");

        return self::SUCCESS;
    }
}
