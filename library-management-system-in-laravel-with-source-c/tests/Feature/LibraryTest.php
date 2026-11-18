<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LibraryTest extends TestCase
{
    use RefreshDatabase;

    private function book(string $code = 'BK-1'): Book
    {
        return Book::create(['code' => $code, 'title' => "Book {$code}", 'author' => 'A. Writer', 'category' => 'Test']);
    }

    private function member(string $code = 'MB-1'): Member
    {
        return Member::create(['code' => $code, 'name' => "Member {$code}", 'email' => "{$code}@example.com"]);
    }

    private function loanedDaysAgo(Member $member, Book $book, int $days): Loan
    {
        return Loan::create([
            'book_id' => $book->id,
            'member_id' => $member->id,
            'borrowed_on' => today()->subDays($days),
            'due_on' => today()->subDays($days)->addDays(Loan::LOAN_DAYS),
        ]);
    }

    public function test_login_page_and_guests_are_redirected(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/')->assertRedirect('/login');
    }

    public function test_borrow_sets_a_due_date_14_days_ahead(): void
    {
        $loan = $this->member()->borrow($this->book());

        $this->assertTrue($loan->due_on->equalTo(today()->addDays(14)));
    }

    public function test_a_book_on_loan_cannot_be_borrowed_twice(): void
    {
        $book = $this->book();
        $this->member('MB-1')->borrow($book);

        $this->expectExceptionMessage('already on loan');
        $this->member('MB-2')->borrow($book);
    }

    public function test_member_limit_is_three_books(): void
    {
        $member = $this->member();
        foreach (['A', 'B', 'C'] as $c) {
            $member->borrow($this->book($c));
        }

        $this->expectExceptionMessage('limit');
        $member->borrow($this->book('D'));
    }

    public function test_overdue_members_cannot_borrow(): void
    {
        $member = $this->member();
        $this->loanedDaysAgo($member, $this->book('OLD'), 20);

        $this->expectExceptionMessage('overdue');
        $member->borrow($this->book('NEW'));
    }

    public function test_return_late_charges_5_per_day(): void
    {
        $loan = $this->loanedDaysAgo($this->member(), $this->book(), 17);

        $loan->giveBack();

        $this->assertSame(3, $loan->daysLate());
        $this->assertEquals(15, $loan->fresh()->fine);
    }

    public function test_return_on_time_has_no_fine(): void
    {
        $loan = $this->loanedDaysAgo($this->member(), $this->book(), 5);

        $loan->giveBack();

        $this->assertEquals(0, $loan->fresh()->fine);
    }

    public function test_counter_borrows_and_returns_through_the_web(): void
    {
        $user = User::factory()->create();
        $this->book('BK-9');
        $this->member('MB-9');

        $this->actingAs($user)
            ->post('/counter/borrow', ['member' => 'MB-9', 'book' => 'BK-9'])
            ->assertSessionHas('ok');

        $this->actingAs($user)
            ->post('/counter/borrow', ['member' => 'MB-9', 'book' => 'BK-9'])
            ->assertSessionHas('bad');

        $this->actingAs($user)
            ->post('/counter/return', ['book' => 'BK-9'])
            ->assertSessionHas('ok');
    }

    public function test_overdue_command_emails_late_members(): void
    {
        Mail::fake();
        $this->loanedDaysAgo($this->member(), $this->book(), 20);

        $this->artisan('library:overdue-notices')->expectsOutput('Overdue notices sent: 1');
    }

    public function test_book_list_paginates_after_ten_books(): void
    {
        foreach (range(1, 12) as $n) {
            $this->book("BK-{$n}");
        }

        $this->actingAs(User::factory()->create())
            ->get('/books')
            ->assertOk()
            ->assertSee('Next');
    }

    public function test_pages_render_with_a_qr_code(): void
    {
        $user = User::factory()->create();
        $book = $this->book();
        $member = $this->member();

        $this->actingAs($user)->get('/books/labels')->assertOk()->assertSee('<svg', false);
        $this->actingAs($user)->get("/members/{$member->id}/card")->assertOk()->assertSee('<svg', false);
        $this->actingAs($user)->get('/')->assertOk();
        $this->actingAs($user)->get('/books')->assertOk()->assertSee($book->title);
        $this->actingAs($user)->get('/members')->assertOk();
        $this->actingAs($user)->get('/loans?show=overdue')->assertOk();
        $this->actingAs($user)->get('/counter')->assertOk();
    }
}
