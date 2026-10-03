<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Spatie\Permission\Models\Role;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_view_categories_index()
    {
        Category::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_specific_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.categories.show', $category->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_category()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'New Category Test',
            'description' => 'Test Description',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('kriteria', [
            'nama_kriteria' => 'New Category Test',
        ]);
    }

    public function test_admin_can_update_category()
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->admin)->put(route('admin.categories.update', $category->id), [
            'name' => 'Updated Category',
            'description' => 'Updated Description',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('kriteria', [
            'nama_kriteria' => 'Updated Category',
        ]);
    }

    public function test_admin_can_delete_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $category->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('kriteria', [
            'id_kriteria' => $category->id,
        ]);
    }
}
