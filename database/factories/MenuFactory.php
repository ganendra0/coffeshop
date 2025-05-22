<?php

namespace Database\Factories;

use App\Models\Menu; // Pastikan model di-import
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str; // Jika menggunakan Str::slug atau helper string lainnya

class MenuFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Menu::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true), // Nama unik dengan 2 kata
            'price' => $this->faker->randomFloat(2, 10000, 50000), // Harga antara 10rb - 50rb
            'category' => $this->faker->randomElement(['Coffee', 'Tea', 'Snack', 'Pastry']),
            'stock' => $this->faker->numberBetween(0, 100),
            'is_available' => $this->faker->boolean(80), // 80% kemungkinan true
            'image_url' => null, // Atau $this->faker->imageUrl(640, 480, 'food') jika ingin gambar dari faker provider
                                 // Tapi untuk test file upload, lebih baik image_url null dan diatur di test
        ];
    }
}