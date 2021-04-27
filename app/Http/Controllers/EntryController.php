<?php

namespace App\Http\Controllers;

use App\Data;
use App\Entry;
use App\Table;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EntryController extends Controller
{
    public function index(Table $table): JsonResponse
    {
        return response()->json($table->entries);
    }

    public function create(Request $request, Table $table): JsonResponse
    {
        $fields = $table->fields;

        $validator = $this->_validate($fields, $request->json()->all());
        if($validator->fails()) return response()->json($validator->errors(), 400);

        $entry = $table->entries()->create([
            "id" => $table->__id()
        ]);

        foreach ($fields as $field)
            (new DataController())->create($entry, $field, $request->json()->get($field->slug));

        return response()->json($entry, 201);
    }

    public function show(Entry $entry): JsonResponse
    {
        return response()->json($entry);
    }

    public function update(Entry $entry, Request $request): JsonResponse
    {
        $fields = $entry->table->fields;

        $validator = $this->_validate($fields, $request->json()->all());
        if($validator->fails())
            return response()->json($validator->errors(), 400);

        foreach ($fields as $field)
            $entry->data()->where("field_id", "=", $field->getKey())
                ->update(["data" => $request->json()->get($field->slug)]);

        return response()->json(["message" => "Entry edited"]);
    }

    public function delete(Entry $entry): JsonResponse
    {
        $data = $entry->data()->delete();
        foreach ($data as $datum)
            $datum->delete();

        try {
            return response()->json($entry->delete());
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    public function _validate($fields, $data): \Illuminate\Contracts\Validation\Validator
    {
        $validations = [];
        foreach ($fields as $field)
            $validations[$field->slug] = $field->validations;

        return Validator::make($data, $validations);
    }

    public function search(Table $table, $query){
        $entries = $table->entries;
        $query = "%".$query."%";
        $data = [];
        foreach ($entries as $entry)
            if($entry->data()->where("data", "like", $query)->count() > 0) $data[] = $entry;

        return response()->json($data);
    }
}