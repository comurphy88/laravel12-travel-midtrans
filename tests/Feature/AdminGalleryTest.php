<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class AdminGalleryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->createOne(['role' => 'admin', 'email_verified_at' => now()]);
        $this->actingAs($this->user);
        Session::start();
        session(['_token' => csrf_token()]);
    }

    public function test_index_displays_galleries()
    {
        Gallery::factory()->count(3)->create();

        $response = $this->get(route('admin.galleries.index'));

        $response->assertStatus(200);
        $response->assertViewHas('galleries');
    }

    public function test_store_creates_gallery()
    {
        $data = [
            'title' => 'Test Gallery',
            // GANTI DENGAN UNSPLASH:
            'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e',
            '_token' => csrf_token(),
        ];

        $response = $this->post(route('admin.galleries.store'), $data);

        $response->assertRedirect(route('admin.galleries.index'));
        $this->assertDatabaseHas('galleries', [
            'title' => 'Test Gallery',
            'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e',
        ]);
    }

    public function test_update_modifies_gallery()
    {
        $gallery = Gallery::factory()->create();
        $data = [
            'title' => 'Updated Title',
            // GANTI DENGAN UNSPLASH:
            'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e',
            '_token' => csrf_token(),
        ];

        $response = $this->put(route('admin.galleries.update', $gallery), $data);

        $response->assertRedirect(route('admin.galleries.index'));
        $this->assertDatabaseHas('galleries', [
            'title' => 'Updated Title', 
            'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e'
        ]);
    }

    public function test_destroy_deletes_gallery()
    {
        $gallery = Gallery::factory()->create();

        $response = $this->delete(route('admin.galleries.destroy', $gallery), [
            '_token' => csrf_token(),
        ]);

        $response->assertRedirect(route('admin.galleries.index'));
        $this->assertDatabaseMissing('galleries', ['id' => $gallery->id]);
    }
}