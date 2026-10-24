<?php

namespace Tests\Feature;

use App\Models\Coffee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CoffeeApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name'     => 'Akmal Roaster',
            'email'    => 'admin@kopinusantara.id',
            'password' => Hash::make('password123'),
        ]);
    }

    /**
     * Test 1: User authentication via Sanctum login endpoint returns token.
     */
    public function test_user_can_login_and_receive_sanctum_token(): void
    {
        $response = $this->postJson('/api/login', [
            'email'    => 'admin@kopinusantara.id',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
                'user' => ['id', 'name', 'email'],
            ]);

        $this->assertNotEmpty($response->json('token'));
    }

    /**
     * Test 2: Unauthenticated user is rejected with 401 Unauthorized.
     */
    public function test_unauthenticated_request_is_rejected_with_401(): void
    {
        $response = $this->getJson('/api/coffees');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    /**
     * Test 3: Authenticated user can create coffee and receives 201 Created.
     */
    public function test_authenticated_user_can_create_coffee(): void
    {
        $token = $this->user->createToken('test_token')->plainTextToken;

        $coffeeData = [
            'name'         => 'Toraja Kalosi Washed',
            'sku'          => 'KOP-TRJ-999',
            'origin'       => 'Tana Toraja, Sulawesi Selatan',
            'process'      => 'Washed',
            'roast_level'  => 'Medium',
            'price'        => 125000,
            'stock'        => 50,
            'flavor_notes' => 'Herbal, Dark Chocolate, Brown Sugar',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/coffees', $coffeeData);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Toraja Kalosi Washed')
            ->assertJsonPath('data.sku', 'KOP-TRJ-999');

        $this->assertDatabaseHas('coffees', [
            'sku' => 'KOP-TRJ-999',
        ]);
    }

    /**
     * Test 4: Creation with invalid/missing data triggers 422 Unprocessable Content.
     */
    public function test_create_coffee_validation_fails_with_422(): void
    {
        $token = $this->user->createToken('test_token')->plainTextToken;

        // Missing required fields: name, sku, origin, process, roast_level, price, stock
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/coffees', [
                'name' => '',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'sku', 'origin', 'process', 'roast_level', 'price', 'stock']);
    }

    /**
     * Test 5: Authenticated user can read, update, and delete coffee.
     */
    public function test_authenticated_user_can_read_update_and_delete_coffee(): void
    {
        $token = $this->user->createToken('test_token')->plainTextToken;

        $coffee = Coffee::create([
            'name'         => 'Gayo Honey',
            'sku'          => 'KOP-GYO-777',
            'origin'       => 'Takengon, Aceh Tengah',
            'process'      => 'Honey',
            'roast_level'  => 'Light',
            'price'        => 130000,
            'stock'        => 25,
            'flavor_notes' => 'Floral, Apricot, Honey',
        ]);

        // READ / SHOW (200 OK)
        $showResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/coffees/{$coffee->id}");
        $showResponse->assertStatus(200)
            ->assertJsonPath('data.sku', 'KOP-GYO-777');

        // UPDATE (200 OK)
        $updateResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/coffees/{$coffee->id}", [
                'name'         => 'Gayo Honey Special Reserve',
                'sku'          => 'KOP-GYO-777',
                'origin'       => 'Takengon, Aceh Tengah',
                'process'      => 'Honey',
                'roast_level'  => 'Light',
                'price'        => 145000,
                'stock'        => 30,
                'flavor_notes' => 'Floral, Apricot, Raw Honey, Jasmine',
            ]);
        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.name', 'Gayo Honey Special Reserve')
            ->assertJsonPath('data.price', 145000);

        // DELETE (200 OK)
        $deleteResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/coffees/{$coffee->id}");
        $deleteResponse->assertStatus(200);

        $this->assertDatabaseMissing('coffees', [
            'id' => $coffee->id,
        ]);
    }
}
