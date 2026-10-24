<?php

namespace Database\Seeders;

use App\Models\Coffee;
use Illuminate\Database\Seeder;

class CoffeeSeeder extends Seeder
{
    /**
     * Run the coffee database seeds.
     */
    public function run(): void
    {
        $coffees = [
            [
                'name'         => 'Gayo Bourbon Anaerobic Natural',
                'sku'          => 'KOP-GYO-001',
                'origin'       => 'Takengon, Aceh Tengah',
                'process'      => 'Anaerobic Natural',
                'roast_level'  => 'Light',
                'price'        => 135000,
                'stock'        => 38,
                'flavor_notes' => 'Red Wine, Blackcurrant, Jasmine, Peach',
            ],
            [
                'name'         => 'Toraja Sapan Washed Reserve',
                'sku'          => 'KOP-TRJ-002',
                'origin'       => 'Tana Toraja, Sulawesi Selatan',
                'process'      => 'Fully Washed',
                'roast_level'  => 'Medium',
                'price'        => 115000,
                'stock'        => 42,
                'flavor_notes' => 'Dark Cocoa, Citrus Lemon, Cedar, Brown Sugar',
            ],
            [
                'name'         => 'Bali Kintamani Carbonic Maceration',
                'sku'          => 'KOP-BALI-003',
                'origin'       => 'Kintamani, Bangli, Bali',
                'process'      => 'Carbonic Maceration',
                'roast_level'  => 'Light',
                'price'        => 150000,
                'stock'        => 20,
                'flavor_notes' => 'Wild Strawberry, Bergamot, Honeycomb, Mandarin',
            ],
            [
                'name'         => 'Flores Bajawa Yellow Caturra Honey',
                'sku'          => 'KOP-FLR-004',
                'origin'       => 'Bajawa, Ngada, NTT',
                'process'      => 'Yellow Honey',
                'roast_level'  => 'Medium',
                'price'        => 120000,
                'stock'        => 55,
                'flavor_notes' => 'Hazelnut, Caramel Butter, Red Apple, Milk Chocolate',
            ],
            [
                'name'         => 'Java Ijen Raung Washed Typica',
                'sku'          => 'KOP-IJN-005',
                'origin'       => 'Gunung Ijen, Bondowoso, Jawa Timur',
                'process'      => 'Fully Washed',
                'roast_level'  => 'Medium-Dark',
                'price'        => 98000,
                'stock'        => 65,
                'flavor_notes' => 'Warm Cinnamon, Toasted Walnut, Molasses, Orange Zest',
            ],
            [
                'name'         => 'Mandheling Lintong Ni Huta Wet-Hulled',
                'sku'          => 'KOP-MDH-006',
                'origin'       => 'Lintong Nihuta, Humbang Hasundutan, Sumut',
                'process'      => 'Wet-Hulled (Giling Basah)',
                'roast_level'  => 'Dark',
                'price'        => 110000,
                'stock'        => 30,
                'flavor_notes' => 'Earthy Forest, Dark Chocolate 85%, Pipe Tobacco, Spicy Clove',
            ],
        ];

        foreach ($coffees as $coffee) {
            Coffee::updateOrCreate(
                ['sku' => $coffee['sku']],
                $coffee
            );
        }
    }
}
