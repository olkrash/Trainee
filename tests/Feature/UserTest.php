<?php

namespace Tests\Feature;

use App\Models\ResetPassword;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
    }

    public function testRegister(): void
    {
        $data = [
            'email' => 'email@email',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
        $response = $this->postJson("api/users", $data);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'token',
            ]);
    }

    /**
     *
     */
    public function testLogin(): void
    {
        User::factory()->create([
            'email' => 'email@email',
            'password' => Hash::make('34567sg')
        ]);

        $data = [
            'email' => 'email@email',
            'password' => '34567sg',
        ];
        $response = $this->postJson("api/users/login", $data);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
            ]);
    }

    public function testResetPassword(): void
    {
        User::factory()->create([
            'email' => 'email@email',
            'password' => '$2y$10$6tHbQJQzyaVh96hRzVl.feBdNBAFdcIYmjy2Um0f07yhyb0eZk4hy'
        ]);

        $response = $this->getJson("api/users/reset_password?email=email@email");
        $response->assertExactJson(['success' => true]);
    }

    public function testChangePassword(): void
    {
        User::factory()->create([
            'id' => 1,
            'email' => 'email@email',
            'password' => '$2y$10$6tHbQJQzyaVh96hRzVl.feBdNBAFdcIYmjy2Um0f07yhyb0eZk4hy'
        ]);

        ResetPassword::factory()->create([
            'user_id' => 1,
            'token' => '123456',
        ]);

        $data = [
            'token' => '123456',
            'password' => '34567sg',
            'password_confirmation' => '34567sg',
        ];
        $response = $this->putJson('api/users/change_password', $data);
        $response->assertExactJson(['success' => true]);
    }
}
