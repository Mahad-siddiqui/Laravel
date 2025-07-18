<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MahadTodo extends Model
{
    protected $table = 'mahadTodos'; // 👈 Tell Laravel your actual table name

    protected $fillable = ['task', 'completed'];
}
// Laravel automatically:

// Converts PascalCase or CamelCase model name → to snake_case + adds an s (plural).

// So: StudentProfile → student_profiles