<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Demo Pharmacist',
            'email' => 'demo@example.com',
            'password' => Hash::make('Demo@1234'),
        ]);

        $categories = collect([
            'Pain & Fever', 'Antibiotics', 'Allergy', 'Vitamins',
            'Stomach', 'Cough & Cold', 'First Aid',
        ])->mapWithKeys(fn ($name) => [$name => Category::create(['name' => $name])->id]);

        // name, generic, category, price, stock, days until expiry
        $rows = [
            ['Biogesic 500mg', 'Paracetamol', 'Pain & Fever', 4.50, 240, 410],
            ['Advil 200mg', 'Ibuprofen', 'Pain & Fever', 9.00, 120, 300],
            ['Amoxil 500mg', 'Amoxicillin', 'Antibiotics', 12.00, 80, 45],
            ['Zithromax 500mg', 'Azithromycin', 'Antibiotics', 85.00, 30, 520],
            ['Cetirizine 10mg', 'Cetirizine', 'Allergy', 6.00, 15, 380],
            ['Claritin 10mg', 'Loratadine', 'Allergy', 18.00, 60, 25],
            ['Vitamin C 500mg', 'Ascorbic acid', 'Vitamins', 3.50, 400, 600],
            ['Centrum Adult', 'Multivitamin', 'Vitamins', 14.00, 8, 700],
            ['Kremil-S', 'Antacid', 'Stomach', 7.50, 90, 12],
            ['Imodium 2mg', 'Loperamide', 'Stomach', 16.00, 50, -9],
            ['Solmux 500mg', 'Carbocisteine', 'Cough & Cold', 10.00, 110, 200],
            ['Neozep Forte', 'Phenylephrine', 'Cough & Cold', 8.00, 12, 330],
            ['Betadine 60ml', 'Povidone-iodine', 'First Aid', 95.00, 35, 800],
            ['Band-Aid Strips', 'Adhesive bandage', 'First Aid', 2.00, 300, 900],
            ['Mefenamic 500mg', 'Mefenamic acid', 'Pain & Fever', 5.50, 180, -30],
        ];

        foreach ($rows as $i => [$name, $generic, $category, $price, $stock, $days]) {
            Medicine::create([
                'category_id' => $categories[$category],
                'name' => $name,
                'generic_name' => $generic,
                'barcode' => '480' . str_pad((string) (1000 + $i * 37), 9, '0', STR_PAD_LEFT),
                'price' => $price,
                'stock' => $stock,
                'reorder_level' => 20,
                'expiry_date' => now()->addDays($days)->toDateString(),
            ]);
        }
    }
}
