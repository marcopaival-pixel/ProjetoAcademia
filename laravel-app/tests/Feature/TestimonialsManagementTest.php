<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class TestimonialsManagementTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed default roles and create admin
        $this->seedRbac();
        $this->adminUser = User::factory()->create([
            'is_admin' => true,
        ]);
    }

    public function test_testimonials_section_is_hidden_on_homepage_when_none_are_approved(): void
    {
        // Testimonial pending approval
        Testimonial::create([
            'name' => 'Roberto Carlos',
            'testimonial' => 'NexShape mudou meu negócio.',
            'rating' => 5,
            'approved_at' => null,
            'is_public' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertDontSee('A Escolha da Elite');
        $response->assertDontSee('Roberto Carlos');
    }

    public function test_testimonials_section_is_visible_on_homepage_when_approved_and_public(): void
    {
        // Approved and public testimonial
        Testimonial::create([
            'name' => 'Roberto Carlos',
            'testimonial' => 'NexShape mudou meu negócio.',
            'rating' => 5,
            'approved_at' => now(),
            'is_public' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('A Escolha da');
        $response->assertSee('Elite');
        $response->assertSee('Roberto Carlos');
        $response->assertSee('NexShape mudou meu negócio.');
    }

    public function test_admin_can_manage_testimonials(): void
    {
        $this->actingAs($this->adminUser);

        // 1. Create a testimonial
        $response = $this->post(route('admin.testimonials.store'), [
            'name' => 'Mariana Mendes',
            'profession' => 'Personal Trainer',
            'city' => 'São Paulo',
            'state' => 'SP',
            'rating' => 5,
            'testimonial' => 'Os laudos e avaliações por IA mudaram o valor da consultoria.',
            'is_public' => 1,
            'featured' => 1,
        ]);

        $response->assertRedirect(route('admin.testimonials.index'));
        $this->assertDatabaseHas('testimonials', [
            'name' => 'Mariana Mendes',
            'profession' => 'Personal Trainer',
            'city' => 'São Paulo',
            'state' => 'SP',
            'rating' => 5,
        ]);

        $testimonial = Testimonial::where('name', 'Mariana Mendes')->first();

        // 2. Toggle approval
        $response = $this->patch(route('admin.testimonials.toggle-approve', $testimonial));
        $response->assertRedirect();
        $this->assertNotNull($testimonial->fresh()->approved_at);

        // 3. Toggle featured
        $response = $this->patch(route('admin.testimonials.toggle-feature', $testimonial));
        $response->assertRedirect();
        $this->assertFalse($testimonial->fresh()->featured);

        // 4. Delete testimonial
        $response = $this->delete(route('admin.testimonials.destroy', $testimonial));
        $response->assertRedirect(route('admin.testimonials.index'));
        $this->assertDatabaseMissing('testimonials', ['id' => $testimonial->id]);
    }
}
