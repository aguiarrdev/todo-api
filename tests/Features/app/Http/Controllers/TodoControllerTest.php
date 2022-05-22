<?php

namespace Features\app\Http\Controllers;

use App\Models\Todo;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class TodoControllerTest extends \Tests\TestCase
{
    use DatabaseMigrations;
    
    public function testUserCanListTodos()
    {
        // Prepare
        Todo::factory(6)->create();

        // Act
        $response = $this->get('/todos');

        // Assert
        $response->assertResponseOk();
        $response->seeJsonStructure(['current_page']);
    }

    public function testUserCanCreateATodo()
    {
        // Prepare
        $payload = [
            'title' => 'Teste title',
            'description' => 'Description for a todo item'
        ];

        // Act
        $result = $this->post('/todos', $payload);

        // Assert
        $result->assertResponseStatus(201);
        $result->seeInDatabase('todos', $payload);
    }

    public function testUserShouldSendTitleAndDescription()
    {
        // Prepare
        $payload = [
            'field' => 'test'
        ];

        // Act
        $response = $this->post('/todos', $payload);

        // Assert
        $response->assertResponseStatus(422);
    }

    public function testUserCanRetrieveASpecificTodo()
    {
        // Prepare
        $todo = Todo::factory()->create();

        // Act
        $uri = '/todos/' . $todo->id;
        $response = $this->get($uri);

        // Assert
        $response->assertResponseOk();
        $response->seeJsonContains(['title' => $todo->title]);
    }

    public function testUserShouldReceive404WhenSearchSomethingThatDoesntExist()
    {
        // Prepare
        // Act
        $response = $this->get('/todos/1');

        // Assert
        $response->assertResponseStatus(404);
        $response->seeJsonContains(['error' => 'not found']);
    }

    public function testUserCanDeleteATodo()
    {
        // Prepare
        $todo = Todo::factory()->create();

        // Act
        $uri = '/todos/' . $todo->id;
        $response = $this->delete($uri);

        // Assert
        $response->assertResponseStatus(204);
        $response->notSeeInDatabase('todos', [
            'id' => $todo->id
        ]);
    }

    public function testUserCanSetTodoDone()
    {
        // Prepare
        $todo = Todo::factory()->create();

        // Act
        $uri = '/todos/' . $todo->id . '/status/done';
        $response = $this->post($uri);

        // Assert
        $response->assertResponseStatus(200);
        $this->seeInDatabase('todos', [
            'id' => $todo->id,
            'done' => true
        ]);
    }

    public function testUserCanSetTodoUndone()
    {
        // Prepare
        $todo = Todo::factory()->create(['done' => true]);

        // Act
        $uri = '/todos/' . $todo->id . '/status/undone';
        $response = $this->post($uri);

        // Assert
        $response->assertResponseStatus(200);
        $this->seeInDatabase('todos', [
            'id' => $todo->id,
            'done' => false
        ]);
    }
}