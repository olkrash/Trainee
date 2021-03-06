<?php

namespace Tests\Feature;

use App\Mail\DeleteUser;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\ResetPassword;
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

        Mail::fake();

        $response = $this->getJson("api/users/reset_password?email=email@email");

        Mail::assertSent(\App\Mail\ResetPassword::class);
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

    public function testUpdate(): void
    {
        $user = User::factory()->create([
            'id' => 1,
            'email' => 'email@email',
            'password' => '$2y$10$6tHbQJQzyaVh96hRzVl.feBdNBAFdcIYmjy2Um0f07yhyb0eZk4hy'
        ]);

        $data = [
            'email' => 'test1@test1',
            'password' => '34567sg',
            'password_confirmation' => '34567sg',
        ];

        $response = $this->actingAs($user, 'api')->putJson('api/users/1', $data);

        $response->assertExactJson(['success' => true]);
    }

    public function testList(): void
    {
        User::factory()->create([
            'id' => 1,
            'email' => 'email@email',
            'password' => '$2y$10$6tHbQJQzyaVh96hRzVl.feBdNBAFdcIYmjy2Um0f07yhyb0eZk4hy'
        ]);
        User::factory()->create([
            'id' => 2,
            'email' => 'email2@email',
            'password' => '$2y$10'
        ]);

        $response = $this->getJson('api/users/');

        $response->assertExactJson(["users" => ["email@email", "email2@email"]]);
    }

    public function testView(): void
    {
        $user = User::factory()->create([
            'created_at'=> "2021-08-12T10:44:56.000000Z",
            'id' => 2,
            'email' => 'email2@email',
            'password' => '$2y$10',
            "updated_at" => "2021-08-12T10:44:56.000000Z",
        ]);

        $response = $this->actingAs($user, 'api')->getJson('api/users/2');

        $response->assertExactJson(['data' => [
            'created_at'=> "2021-08-12T10:44:56.000000Z",
            'id' => 2,
            'email' => 'email2@email',
            "updated_at" => "2021-08-12T10:44:56.000000Z"]]);

    }

    public function testDelete(): void
    {
        $user = User::factory()->create([
            'created_at' => "2021-08-12T10:44:56.000000Z",
            'id' => 2,
            'email' => 'email2@email',
            'password' => '$2y$10',
            "status" => User::ACTIVE,
        ]);

        Mail::fake();
        $response = $this->actingAs($user, 'api')->deleteJson('api/users/2');

        Mail::assertSent(DeleteUser::class);
        $response->assertExactJson(['success' => true]);
    }
}
