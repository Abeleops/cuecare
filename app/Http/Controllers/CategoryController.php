<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\AuditLogger;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    public function store(Request $request, AuditLogger $audit): JsonResponse
    {
        $name = $request->input('name');
        if (is_string($name)) {
            $request->merge(['name' => trim($name)]);
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')],
            'category_group' => ['required', Rule::in(['food', 'beverage', 'alcoholic'])],
        ]);

        try {
            $category = DB::transaction(function () use ($data, $audit): Category {
                $category = Category::query()->create($data);
                $audit->record('category_created', $category);

                return $category;
            });
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['name' => 'This category name is already in use']);
        }

        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'category_group' => $category->category_group,
        ], 201);
    }
}
