<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Store a newly created asset.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'unique:assets,serial_number'],
            'status' => ['required', 'string', 'in:active,inactive,maintenance'],
        ]);

        $asset = Asset::create($validated);

        return response()->json($asset, 201);
    }

    /**
     * Display the specified asset with its latest 3 inspections.
     */
    public function show(string $id): JsonResponse
    {
        $asset = Asset::with(['inspections' => function ($query) {
            $query->orderByDesc('created_at')
                  ->orderByDesc('id')
                  ->limit(3);
        }])->findOrFail($id);

        return response()->json($asset);
    }
}
