<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Demo Librarian',
            'email' => 'demo@example.com',
            'password' => Hash::make('Demo@1234'),
        ]);

        $books = [
            ['Clean Code Basics', 'Robert Hale', 'Programming'],
            ['Learning PHP the Easy Way', 'Maria Santos', 'Programming'],
            ['Laravel from Scratch', 'Ken Alvarez', 'Programming'],
            ['The Little SQL Book', 'Ana Reyes', 'Programming'],
            ['A Brief History of Time Zones', 'Luis Cruz', 'Science'],
            ['The Stars Above Manila', 'Joy Lim', 'Science'],
            ['Cooking for Busy Students', 'Rita Gomez', 'Lifestyle'],
            ['Budget Travel Notes', 'Paolo Diaz', 'Lifestyle'],
            ['The Lighthouse Keeper', 'Elena Ford', 'Fiction'],
            ['Rivers of Glass', 'Noel Bautista', 'Fiction'],
            ['Last Train to Baguio', 'Mia Torres', 'Fiction'],
            ['Essays on Everyday Math', 'Dr. Ben Ong', 'Education'],
        ];

        foreach ($books as $i => [$title, $author, $category]) {
            Book::create([
                'code' => 'BK-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'title' => $title,
                'author' => $author,
                'category' => $category,
            ]);
        }

        $members = [
            ['Ana Cruz', 'ana@example.com', '0917 000 0001'],
            ['Ben Reyes', 'ben@example.com', '0917 000 0002'],
            ['Carla Santos', 'carla@example.com', '0917 000 0003'],
            ['Dan Lopez', 'dan@example.com', '0917 000 0004'],
            ['Ella Garcia', 'ella@example.com', '0917 000 0005'],
        ];

        foreach ($members as $i => [$name, $email, $phone]) {
            Member::create([
                'code' => 'MB-' . (1001 + $i),
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
            ]);
        }

        // book number, member number, borrowed days ago, returned days ago (null = still out)
        $loans = [
            [1, 2, 20, null],  // Ben: 6 days overdue
            [5, 3, 17, null],  // Carla: 3 days overdue
            [3, 1, 5, null],   // Ana: on time
            [9, 1, 12, null],  // Ana: due in 2 days
            [7, 4, 30, 18],    // Dan: returned late (history)
            [2, 5, 40, 30],    // Ella: returned on time (history)
        ];

        foreach ($loans as [$book, $member, $ago, $returned]) {
            $due = now()->subDays($ago)->addDays(14);

            Book::find($book)->loans()->create([
                'member_id' => $member,
                'borrowed_on' => now()->subDays($ago)->toDateString(),
                'due_on' => $due->toDateString(),
                'returned_on' => $returned === null ? null : now()->subDays($returned)->toDateString(),
                'fine' => $returned !== null && now()->subDays($returned)->gt($due)
                    ? (int) $due->diffInDays(now()->subDays($returned)) * 5
                    : 0,
            ]);
        }
    }
}
