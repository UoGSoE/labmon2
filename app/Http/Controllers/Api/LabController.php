<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lab;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LabController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:labs,name',
            'school' => 'nullable|string|max:255',
        ]);

        $lab = Lab::create([
            'name' => $request->name,
            'school' => $request->school,
        ]);

        return response()->json($lab, 201);
    }

    public function destroy(string $name): JsonResponse
    {
        $lab = Lab::where('name', $name)->firstOrFail();
        $lab->delete();

        return response()->json(['message' => 'Lab deleted successfully']);
    }
}