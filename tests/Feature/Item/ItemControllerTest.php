<?php

namespace Tests\Feature\Controllers;

use App\Models\Item;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $tenant;
    protected $user;
    protected $token;

    // Set up common data for tests
    public function setUp(): void
    {
        parent::setUp();

        // Create a tenant and associate the user with the tenant
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);
        
        // Generate a token for the user
        $this->token = $this->user->createToken('TestApp')->plainTextToken;
    }

    // Helper method to build the URL for a given path
    protected function buildUrl($path)
    {
        return 'http://' . $this->tenant->subdomain . '.' . env('APP_DOMAIN') . $path;
    }

    // Test to get all items
    public function test_can_get_all_items()
    {
        Item::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $url = $this->buildUrl('/api/items');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson($url);

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    // Test to get a single item
    public function test_can_get_single_item()
    {
        $item = Item::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $url = $this->buildUrl("/api/items/{$item->id}");

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson($url);

        $response->assertStatus(200)
                 ->assertJson(['id' => $item->id]);
    }

    // Test 404 response for non-existing item
    public function test_returns_404_if_item_not_found()
    {
        $url = $this->buildUrl('/api/items/999');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson($url);

        $response->assertStatus(404);
    }

    // Test for creating an item
    public function test_can_create_item()
    {
        $data = [
            'name' => 'New Item',
            'description' => 'Description here',
            'price' => 99.99,
            'stock' => 10,
            'tenant_id' => $this->tenant->id,
        ];

        $url = $this->buildUrl('/api/items');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson($url, $data);

        $response->assertStatus(201)
                 ->assertJson(['name' => 'New Item']);

        $this->assertDatabaseHas('items', ['name' => 'New Item']);
    }

    // Test for invalid item creation data
    public function test_cannot_create_item_with_invalid_data()
    {
        $url = $this->buildUrl('/api/items');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson($url, []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'price', 'stock']);
    }

    // Test for updating an item
    public function test_can_update_item()
    {
        $item = Item::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);
        $data = ['name' => 'Updated Name'];

        $url = $this->buildUrl("/api/items/{$item->id}");

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson($url, $data);

        $response->assertStatus(200)
                 ->assertJson(['name' => 'Updated Name']);

        $this->assertDatabaseHas('items', ['id' => $item->id, 'name' => 'Updated Name']);
    }

    // Test for deleting an item
    public function test_can_delete_item()
    {
        $item = Item::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $url = $this->buildUrl("/api/items/{$item->id}");

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson($url);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Item deleted successfully']);

        // Assert that the item is soft deleted
        $softDeletedItem = Item::withTrashed()->find($item->id);
        $this->assertNotNull($softDeletedItem->deleted_at);
    }
}
