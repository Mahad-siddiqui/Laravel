<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TodoController extends Controller
{
    // In-memory todos (simulate DB)
    private static array $todos = [
        ['id' => 1, 'title' => 'Learn Laravel', 'completed' => false],
        ['id' => 2, 'title' => 'Build a Todo App', 'completed' => false],
    ];

    // GET /api/todos
    public function index(): JsonResponse
    {
        return response()->json(self::$todos);
    }

    // POST /api/todos
    public function store(Request $request): JsonResponse
    {
        $newTodo = [
            'id' => count(self::$todos) + 1,
            'title' => $request->input('title'),
            'completed' => false,
        ];

        self::$todos[] = $newTodo;

        return response()->json($newTodo, 201);
    }

    // PUT /api/todos/{id}
    public function update(Request $request, $id): JsonResponse
    {
        foreach (self::$todos as &$todo) {
            if ($todo['id'] == $id) {
                $todo['title'] = $request->input('title', $todo['title']);
                $todo['completed'] = $request->input('completed', $todo['completed']);
                return response()->json($todo);
            }
        }

        return response()->json(['message' => 'Todo not found'], 404);
    }

    // DELETE /api/todos/{id}
    public function destroy($id): JsonResponse
    {
        foreach (self::$todos as $index => $todo) {
            if ($todo['id'] == $id) {
                array_splice(self::$todos, $index, 1);
                return response()->json(['message' => 'Todo deleted']);
            }
        }

        return response()->json(['message' => 'Todo not found'], 404);
    }
}

