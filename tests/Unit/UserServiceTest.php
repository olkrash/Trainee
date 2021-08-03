<?php

namespace Tests\Unit;

use App\Models\ResetPassword;
use App\Models\User;
use App\services\UserService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
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
     * @dataProvider  createProvider
     */
    public function testCreate(array $data)
    {
        $user = $this->userService->create($data);
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
    public function createProvider(): array
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

    /**
     * @dataProvider loginProvider
     */
    public function testLogin($email, $password = '')
    {
        $data['email'] = 'email@email';
        $data['password'] = '$2y$10$6tHbQJQzyaVh96hRzVl.feBdNBAFdcIYmjy2Um0f07yhyb0eZk4hy';
        User::factory()->create($data);

        $data['password'] = $password;
        $data['email'] = $email;
        $user = $this->userService->login($data);

        if ($password === '') {
            $this->assertNull($user);
            return;
        }

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @return string[][]
     */
    public function loginProvider(): array
    {
        return [
            //first run of testLogin
            [
                'email@email',
                '34567sg',
            ],
            //second run of testLogin
            [
                'test@test'
            ],
            //third run of testLogin
            [
                'email@email',
            ],
        ];
    }

    /**
     * @dataProvider  resetProvider
     */
    public function testResetPassword(string $email, bool $expected)
    {
        $data['email'] = 'email@email';
        $data['password'] = Hash::make('12345');
        User::factory()->create($data);

        $result = $this->userService->resetPassword($email);
        $this->assertEquals($expected, $result);
    }

    public function resetProvider(): array
    {
        return [
            ['email', false],
            ['email@email', true],
        ];
    }

    /**
     * @dataProvider changeProvider
     */
    public function testChangePassword(string $token, int $id, Carbon $tokenDate, bool $expected = false)
    {
        User::factory()->create([
            'id' => 1,
            'email' => 'email@email',
            'password' => '$2y$10$6tHbQJQzyaVh96hRzVl.feBdNBAFdcIYmjy2Um0f07yhyb0eZk4hy',
        ]);

        ResetPassword::factory()->create([
            'user_id' => $id,
            'token' => '123456',
            'created_at' => $tokenDate,
        ]);

        $actual = $this->userService->changePassword($token, '123456');
        $this->assertEquals($expected, $actual);
    }

    public function changeProvider(): array
    {
        return [
            ['token', 1, Carbon::now()],
            ['123456', 5, Carbon::now()],
            ['123456', 1, Carbon::now()->subHours(4)],
            ['123456', 1, Carbon::now(), true],
        ];
    }
}
