<?php

namespace Tests\Unit;

use App\Models\User;
use App\services\UserService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class UserServiceTest
 *
 * @package Tests\Unit
 */
class UserServiceTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var UserService
     */
    private $userService;

    //Analogue of __construct
    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = app()->make(UserService::class);
    }

    /**
     * @dataProvider  userProvider
     */
    public function testCreateUser(array $data)
    {
        $user = $this->userService->createUser($data);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($data['email'], $user->email);
        $this->assertNotEmpty($user->password);
        $this->assertCount(10, str_split($user->remember_token));
        $this->assertDatabaseHas('users', [
            'id' => $user->id
        ]);
    }

    /**
     * @return string[][][]
     */
    public function userProvider(): array
    {
        return [
            [
                [
                    'email' => 'test@gmail.com',
                    'password' => 'test'
                ],
            ]
        ];
    }
}
