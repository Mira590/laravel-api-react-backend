<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_users_and_filters_search_results(): void
    {
        $user = User::factory()->create([
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
        ]);

        User::factory()->create([
            'name' => 'Bob Smith',
            'email' => 'bob@example.com',
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/users?search=alice')
            ->assertOk()
            ->assertJsonPath('message', 'Users retrieved successfully')
            ->assertJsonPath('users.data.0.name', 'Alice Johnson');
    }
}
