<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase; // Reset database untuk setiap test
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile; // Untuk simulasi file upload
use Illuminate\Support\Facades\Storage; // Untuk interaksi dengan storage
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Menu;
use App\Models\User; // Jika perlu user untuk otentikasi

class MenuApiTest extends TestCase
{
    use RefreshDatabase; // Sangat penting!
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // Setup tambahan jika diperlukan, misal membuat user admin untuk testing
        // $this->admin = User::factory()->create(['is_admin' => true]);
        Storage::fake('public'); // Fake storage agar tidak benar-benar menulis file
    }

    #[Test]
    public function can_get_all_menus()
    {
        Menu::factory()->count(3)->create(); // Buat 3 menu dummy

            $response = $this->getJson('/api/v1/menus');

       $response->assertStatus(200)
             ->assertJsonStructure([
                 'success', // Kunci di root JSON
                 'message', // Kunci di root JSON
                 'data' => [ // Kunci 'data' di root JSON, yang berisi objek paginator
                     'current_page',
                     'data' => [ // Kunci 'data' di dalam objek paginator, ini adalah array item menu
                         '*' => [ // Setiap item di dalam array 'data' milik paginator
                             'menu_id',
                             'name',
                             'price',
                             'category',
                             'stock',
                             'is_available',
                             'image_url',
                             'full_image_url',
                             'created_at',
                             'updated_at',
                         ]
                     ],
                     'first_page_url',
                     'from',
                     'last_page',
                     'last_page_url',
                     'links' => [ // Kunci 'links' di dalam objek paginator
                        '*' => [ // Setiap item di dalam array 'links'
                            'url',
                            'label',
                            'active',
                        ]
                     ],
                     'next_page_url',
                     'path',
                     'per_page',
                     'prev_page_url',
                     'to',
                     'total',
                 ]
             ])
             ->assertJsonCount(3, 'data.data'); // Hitung item di dalam 'data'.'data'
    }

    #[Test]
    public function can_create_a_menu_with_image()
    {
        // $this->actingAs($this->admin, 'sanctum'); // Jika perlu otentikasi

        $menuData = [
            'name' => $this->faker->sentence(2),
            'price' => $this->faker->randomFloat(2, 10000, 50000),
            'category' => 'Coffee',
            'stock' => $this->faker->numberBetween(10, 100),
            'is_available' => true,
            'image_file' => UploadedFile::fake()->image('latte.jpg', 200, 200) // Simulasi file
        ];

        $response = $this->postJson('/api/v1/menus', $menuData);

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.name', $menuData['name']);

        $this->assertDatabaseHas('menus', ['name' => $menuData['name']]); // Cek database

        // Ambil menu yang baru dibuat untuk cek path gambar
        $createdMenu = Menu::where('name', $menuData['name'])->first();
        Storage::disk('public')->assertExists($createdMenu->image_url); // Cek file ada di fake storage
    }

    #[Test]
    public function create_menu_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/v1/menus', ['name' => '']); // Nama kosong

        $response->assertStatus(422) // Unprocessable Entity
                 ->assertJsonValidationErrors(['name']); // Pastikan ada error validasi untuk 'name'
    }

    #[Test]
    public function can_get_a_single_menu()
    {
        $menu = Menu::factory()->create();

        $response = $this->getJson("/api/v1/menus/{$menu->menu_id}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.menu_id', $menu->menu_id);
    }

    #[Test]
    public function get_single_menu_returns_404_if_not_found()
    {
        $response = $this->getJson('/api/v1/menus/999'); // ID yang tidak ada
        $response->assertStatus(404);
    }


    #[Test]
    public function can_update_a_menu()
    {
        $menu = Menu::factory()->create();
        $updateData = [
            'name' => 'Updated Menu Name',
            'price' => 25000.00,
        ];

        $response = $this->putJson("/api/v1/menus/{$menu->menu_id}", $updateData);

        $response->assertStatus(200)
                 ->assertJsonPath('data.name', 'Updated Menu Name');

        $this->assertDatabaseHas('menus', [
            'menu_id' => $menu->menu_id,
            'name' => 'Updated Menu Name'
        ]);
    }

    #[Test]
    public function can_delete_a_menu_with_its_image()
    {
        // Buat menu dengan gambar
        $image = UploadedFile::fake()->image('menu_to_delete.jpg');
        $path = $image->store('menus', 'public'); // Simpan dulu ke fake storage
        $menu = Menu::factory()->create(['image_url' => $path]);

        Storage::disk('public')->assertExists($path); // Pastikan gambar ada sebelum delete

        $response = $this->deleteJson("/api/v1/menus/{$menu->menu_id}");

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('menus', ['menu_id' => $menu->menu_id]);
        Storage::disk('public')->assertMissing($path); // Pastikan gambar juga terhapus
    }

    // Tambahkan test untuk kasus gagal update, validasi unik saat update, dll.
}