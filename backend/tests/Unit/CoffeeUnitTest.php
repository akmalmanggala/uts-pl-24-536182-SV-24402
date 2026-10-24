<?php

namespace Tests\Unit;

use App\Models\Coffee;
use PHPUnit\Framework\TestCase;

class CoffeeUnitTest extends TestCase
{
    /**
     * Test basic unit assertions for coffee entity calculations.
     */
    public function test_coffee_model_casts_and_attributes(): void
    {
        $coffee = new Coffee([
            'name'         => 'Toraja Sapan Washed',
            'sku'          => 'KOP-TRJ-002',
            'origin'       => 'Tana Toraja, Sulawesi Selatan',
            'process'      => 'Fully Washed',
            'roast_level'  => 'Medium',
            'price'        => 115000,
            'stock'        => 42,
            'flavor_notes' => 'Dark Cocoa, Citrus Lemon',
        ]);

        $this->assertEquals('Toraja Sapan Washed', $coffee->name);
        $this->assertEquals('KOP-TRJ-002', $coffee->sku);
        $this->assertEquals(115000, $coffee->price);
        $this->assertEquals(42, $coffee->stock);
        $this->assertTrue($coffee->stock > 0);
    }
}
