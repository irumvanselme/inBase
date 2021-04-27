<?php

namespace App\Http\Controllers;

use App\Database;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DatabaseController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(auth()->user()->databases);
    }

    public function show(Database $database): JsonResponse
    {
        return response()->json($database);
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->json()->all(), [
            "name" => "required|string|min:3|max:100",
            "description" => "string|min:10|max:255",
            "key" => "string|min:2"
        ]);

        if($validator->fails())
            return response()->json($validator->errors(), 400);

        $database = auth()->user()->databases()->create([
            "name" => $request->json()->get("name"),
            "description" => $request->json()->get("description"),
            "key" => $request->json()->get("key")
        ]);

        return response()->json($database);
    }

    public function update(Database $database, Request $request): JsonResponse
    {
        $validator = Validator::make($request->json()->all(), [
            "name" => "required|string|min:3|max:100",
            "description" => "string|min:10|max:255",
            "key" => "string|min:2"
        ]);

        if($validator->fails())
            return response()->json($validator->errors(), 400);

        $database = $database->update([
            "name" => $request->json()->get("name"),
            "description" => $request->json()->get("description"),
            "key" => $request->json()->get("key")
        ]);

        return response()->json($database);
    }

    public function delete(Database $database): JsonResponse
    {
        try {
            return response()->json($database->delete());
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }
}
