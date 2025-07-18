<!-- <?php

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
            // const title = req.body.title;
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
 -->


 <?php

namespace App\Http\Controllers;

use App\Models\MahadTodo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    // List all todos
    public function index()
    {
        $todos = MahadTodo::all();
        return response()->json($todos);
    }

    // Create a new todo
    public function store(Request $request)
    {
        $request->validate([
            'task' => 'required|string|max:255',
            'completed' => 'boolean',
        ]);

        $todo = MahadTodo::create([
            'task' => $request->task,
            'completed' => $request->completed ?? false,
        ]);

        return response()->json($todo, 201);
    }

    // Show a single todo
    public function show($id)
    {
        $todo = MahadTodo::find($id);

        if (!$todo) {
            return response()->json(['message' => 'Todo not found'], 404);
        }

        return response()->json($todo);
    }

    // Update a todo
    public function update(Request $request, $id)
    {
        $todo = MahadTodo::find($id);

        if (!$todo) {
            return response()->json(['message' => 'Todo not found'], 404);
        }

        $request->validate([
            'task' => 'sometimes|string|max:255',
            'completed' => 'sometimes|boolean',
        ]);

        $todo->update($request->only(['task', 'completed']));

        return response()->json($todo);
    }

    // Delete a todo
    public function destroy($id)
    {
        $todo = MahadTodo::find($id);

        if (!$todo) {
            return response()->json(['message' => 'Todo not found'], 404);
        }

        $todo->delete();

        return response()->json(['message' => 'Todo deleted']);
    }
}