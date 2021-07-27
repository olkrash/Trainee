<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateUserTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
    }


    public function testRegister()
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
}
