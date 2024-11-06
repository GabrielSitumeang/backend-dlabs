<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class UserApiTest extends TestCase
{
    public function test_get_users_with_pagination()
    {
        User::factory()->count(15)->create();
        $response = $this->getJson('/api/users?page=1&per_page=10');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'current_page',
                    'data' => [
                        '*' => ['id', 'name', 'email', 'age', 'membership_status'],
                    ],
                    'total',
                    'per_page'
                ]);
    }

    public function test_create_user()
{
    $userData = [
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
        'password' => 'password123',
        'age' => 30,
    ];

    $response = $this->postJson('/api/users', $userData);

    $response->assertStatus(201)
             ->assertJsonFragment([
                 'name' => 'John Doe',
                 'email' => 'johndoe@example.com'
             ]);

    $invalidData = ['name' => '', 'email' => 'not-an-email', 'age' => -5];
    $response = $this->postJson('/api/users', $invalidData);

    $response->assertStatus(422) 
             ->assertJsonValidationErrors(['name', 'email', 'age']);
}

public function test_update_user()
{
    $user = User::factory()->create();

    $updateData = ['name' => 'Jane Doe', 'age' => 35];
    $response = $this->putJson("/api/users/{$user->id}", $updateData);

    $response->assertStatus(200)
             ->assertJsonFragment([
                 'id' => $user->id,
                 'name' => 'Jane Doe',
                 'age' => 35,
             ]);

    $response = $this->putJson('/api/users/9999', $updateData);
    $response->assertStatus(404)->assertJson(['error' => 'User not found']);
}

public function test_delete_user()
{
    $user = User::factory()->create();

    $response = $this->deleteJson("/api/users/{$user->id}");
    $response->assertStatus(204); 

    $response = $this->deleteJson("/api/users/{$user->id}");
    $response->assertStatus(404)->assertJson(['error' => 'User not found']);
}

public function test_access_protected_route_without_token()
{
    $response = $this->postJson('/api/users', ['name' => 'Jane Doe']);
    $response->assertStatus(401)->assertJson(['error' => 'Unauthorized']);
}

public function test_login_with_invalid_credentials()
{
    $response = $this->postJson('/api/login', [
        'email' => 'invalid@example.com',
        'password' => 'wrongpassword'
    ]);

    $response->assertStatus(401)
             ->assertJson(['error' => 'Invalid credentials']);
}

}
