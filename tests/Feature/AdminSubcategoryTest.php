<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Subcategory;
use Spatie\Permission\Models\Role;

class AdminSubcategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        
        $this->category = Category::factory()->create();
    }

    public function test_admin_can_view_subcategories_index()
    {
        Subcategory::factory()->count(3)->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.subcategories.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_specific_subcategory()
    {
        $subcategory = Subcategory::factory()->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.subcategories.show', $subcategory->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_subcategory()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.subcategories.store'), [
            'category_id' => $this->category->id,
            'name' => 'New Subcat Test',
            'description' => 'Test Desc Subcat',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('subkriteria', [
            'nama_subkriteria' => 'New Subcat Test',
            'category_id' => $this->category->id,
        ]);
    }

    public function test_admin_can_update_subcategory()
    {
        $subcategory = Subcategory::factory()->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->admin)->put(route('admin.subcategories.update', $subcategory->id), [
            'category_id' => $this->category->id,
            'name' => 'Updated Subcat',
            'description' => 'Updated Context',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('subkriteria', [
            'nama_subkriteria' => 'Updated Subcat',
        ]);
    }

    public function test_admin_can_delete_subcategory()
    {
        $subcategory = Subcategory::factory()->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->admin)->delete(route('admin.subcategories.destroy', $subcategory->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('subkriteria', [
            'id_subkriteria' => $subcategory->id,
        ]);
    }
}
