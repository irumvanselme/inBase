<?php

namespace App\Http\Controllers;

use App\Field;
use App\Table;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FieldController extends Controller
{
    public function add(Table $table, Request $request){
        $this->create($request->json()->all(), $table);
    }

    public function store(Request $request, Table $table): JsonResponse
    {
        $validator = Validator::make($request->json()->all(), [
            "name" => "required|string|min:3|max:100",
            "validations" => "string|min:3"
        ]);

        if ($validator->fails())
            return response()->json($validator->errors(), 400);

        $field = $table->fields()->create([
            "name" => $request->json()->get("name"),
            "validations" => $request->json()->get("validations"),
        ]);

        return response()->json($field);
    }

    public function create($field_data, $table): JsonResponse
    {
        $field = $table->fields()->create($field_data);

        return response()->json($field, 201);
    }

    public function edit(Request $request,Field $field): JsonResponse
    {
        $validator = Validator::make($request->json()->all(), [
            "name" => "required|string|min:3|max:100",
            "validations" => "string|min:3"
        ]);

        if ($validator->fails())
            return response()->json($validator->errors(), 400);

        $field = $field->update($request->json()->all());

        return response()->json($field);
    }

    public function delete(Field $field): JsonResponse
    {
        try {
            return response()->json($field->delete(), 201);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }
}