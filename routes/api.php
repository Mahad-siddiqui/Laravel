    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\TodoController;
use Illuminate\Http\Request;

Route::post('/test', function (Request $request) {
    return response()->json([
        'message' => 'POST API is working!',
        'your_data' => $request->all(),
    ]); 
});
    Route::get('/test', function () {
        return response()->json(['message' => 'API is working!']);
    });

    Route::get('/todos', [TodoController::class, 'index']);
    Route::post('/todos', [TodoController::class, 'store']);
    Route::put('/todos/{id}', [TodoController::class, 'update']);
    Route::delete('/todos/{id}', [TodoController::class, 'destroy']);

    // Route::fallback(function () {
    //     return response()->json(['message' => 'Not -------Found'], 404);
    // });

    
// Route is a class
// get is a static method
// :: says “Call this method without creating an object”