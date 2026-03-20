<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_redirected_away_from_storefront_pages(): void
    {
        /** @var User $admin */
        $admin = User::factory()->createOne(['role' => User::ROLE_ADMIN]);
        $event = $this->createEvent();

        $this->actingAs($admin)
            ->get(route('home'))
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($admin)
            ->get(route('events.index'))
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($admin)
            ->get(route('events.show', $event))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_staff_is_redirected_away_from_storefront_pages(): void
    {
        /** @var User $staff */
        $staff = User::factory()->createOne(['role' => User::ROLE_STAFF]);
        $event = $this->createEvent();

        $this->actingAs($staff)
            ->get(route('home'))
            ->assertRedirect(route('staff.dashboard'));

        $this->actingAs($staff)
            ->get(route('events.index'))
            ->assertRedirect(route('staff.dashboard'));

        $this->actingAs($staff)
            ->get(route('events.show', $event))
            ->assertRedirect(route('staff.dashboard'));
    }

    public function test_guest_and_customer_can_access_storefront_pages(): void
    {
        /** @var User $customer */
        $customer = User::factory()->createOne(['role' => User::ROLE_USER]);
        $event = $this->createEvent();

        $this->get(route('home'))->assertOk();
        $this->get(route('events.index'))->assertOk();
        $this->get(route('events.show', $event))->assertOk();

        $this->actingAs($customer)
            ->get(route('home'))
            ->assertOk();

        $this->actingAs($customer)
            ->get(route('events.index'))
            ->assertOk();

        $this->actingAs($customer)
            ->get(route('events.show', $event))
            ->assertOk();
    }

    private function createEvent(): Event
    {
        $category = Category::create(['name' => 'Storefront']);

        return Event::create([
            'title' => 'Storefront Event',
            'category_id' => $category->id,
            'price' => 99,
            'location' => 'City',
            'description' => 'Storefront access validation event',
            'total_slots' => 20,
            'is_active' => true,
        ]);
    }
}
