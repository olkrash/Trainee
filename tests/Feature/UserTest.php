<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
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
    public function testLogin():void
    {
        User::factory()->create([
            'email' => 'email@email',
            'password' => '$2y$10$6tHbQJQzyaVh96hRzVl.feBdNBAFdcIYmjy2Um0f07yhyb0eZk4hy'
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


}
