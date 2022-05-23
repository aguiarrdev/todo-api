<?php

namespace Features\app\Http\Controllers;

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UsersControllerTest extends \Tests\TestCase
{
    use DatabaseMigrations;

    public function testUserCanBeCreated()
    {
        // Prepare
        $payload = [
            'name' => 'Test Name',
            'email' => 'email@test.com',
            'password' => '!Test123456'
        ];

        // Act
        $result = $this->post('/users/create', $payload);

        // Assert
        $result->assertResponseStatus(201);
        $result->seeInDatabase('users', ['name' => $payload['name'], 'email' => $payload['email'] ]);
    }
}