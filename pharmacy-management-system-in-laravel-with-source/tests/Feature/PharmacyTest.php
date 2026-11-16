<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PharmacyTest extends TestCase
{
    use RefreshDatabase;

    private function medicine(array $attrs = []): Medicine
    {
        return Medicine::create(array_merge([
            'category_id' => Category::firstOrCreate(['name' => 'Pain'])->id,
            'name' => 'Biogesic 500mg',
            'barcode' => '4801000',
            'price' => 5,
            'stock' => 10,
            'reorder_level' => 20,
            'expiry_date' => now()->addYear()->toDateString(),
        ], $attrs));
    }

    public function test_every_page_loads_for_a_logged_in_user(): void
    {
        $this->seed();
        $this->actingAs(User::first());

        foreach (["/", "/medicines", "/medicines/create", "/pos", "/sales"] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_user_can_log_in(): void
    {
        $user = User::factory()->create(['password' => 'secret-pass']);

        $this->post('/login', ['email' => $user->email, 'password' => 'secret-pass'])
            ->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_dashboard_lists_expired_expiring_and_low_stock(): void
    {
        $this->medicine(['name' => 'Old', 'barcode' => '1', 'expiry_date' => now()->subDay()->toDateString()]);
        $this->medicine(['name' => 'Soon', 'barcode' => '2', 'expiry_date' => now()->addDays(20)->toDateString(), 'stock' => 99]);
        $this->medicine(['name' => 'Few', 'barcode' => '3', 'stock' => 3]);

        $this->actingAs(User::factory()->create())->get('/')
            ->assertOk()->assertSee('Old')->assertSee('Soon')->assertSee('Few');
    }

    public function test_expired_medicine_cannot_be_scanned(): void
    {
        $this->medicine([
            'barcode' => '999',
            'expiry_date' => now()->subDay()->toDateString(),
        ]);
        $message = 'Biogesic 500mg is EXPIRED. Do not sell.';

        $this->actingAs(User::factory()->create())
            ->getJson('/pos/lookup/999')
            ->assertStatus(422)
            ->assertJsonFragment(['error' => $message]);
    }

    public function test_checkout_reduces_stock_and_saves_the_sale(): void
    {
        $medicine = $this->medicine();

        $this->actingAs(User::factory()->create())
            ->postJson('/pos/checkout', [
                'items' => [['id' => $medicine->id, 'qty' => 3]],
                'paid' => 20,
            ])->assertOk()->assertJsonStructure(['receipt']);

        $this->assertSame(7, $medicine->fresh()->stock);
        $this->assertDatabaseHas('sales', ['total' => 15, 'paid' => 20]);
    }

    public function test_checkout_rejects_more_than_the_stock(): void
    {
        $medicine = $this->medicine(['stock' => 2]);

        $this->actingAs(User::factory()->create())
            ->postJson('/pos/checkout', [
                'items' => [['id' => $medicine->id, 'qty' => 5]],
                'paid' => 100,
            ])->assertStatus(422);

        $this->assertSame(2, $medicine->fresh()->stock);
    }
}
