<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:32'
        ]);

        $tag = Tag::query()->firstOrCreate($validated);

        return response()->json(['success' => true, 'tag' => $tag]);
    }

    public function index(): JsonResponse
    {
        $tags = Tag::all();

        return response()->json(['success' => true, 'tags' => $tags]);
    }
}