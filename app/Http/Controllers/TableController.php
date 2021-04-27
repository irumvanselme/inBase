<?php
namespace App\Http\Controllers;


use App\Data;
use App\Database;
use App\Table;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    public function index(Database $database): JsonResponse
    {
        return response()->json($database->tables);
    }

    public function create(Database $database, Request $request): JsonResponse
    {
        $validator = Validator::make($request->json()->all(), [
            "name" => "required|string|min:3|max:100",
            "description" => "string|min:10|max:255",
            "fields" => "required|array|min:2",
            "fields.*.name" => "required|string|min:3",
            "fields.*.validations" => "required|string|min:3",
        ]);

        if($validator->fails())
            return response()->json($validator->errors(), 400);

        if($database->tables()->where("name","=",$request->json()->get("name"))->first())
            return response()->json(["name"=>["Table name already registered in your tables "]],400);

        $fields = $request->json()->get("fields");

        $fields_s = [];
        foreach ($fields as $field) $fields_s[] = $field["name"];

        foreach ($fields_s as $string)
            if(array_count_values($fields_s)[$string] > 1)
                return response()->json(["fields"=>["Every field name must be unique "]]);

        $table = $database->tables()->create([
            "name" => $request->json()->get("name"),
            "description" => $request->json()->get("description"),
            "__data_counter" => 0
        ]);

        foreach ($fields as $field) {
            (new FieldController())->create($field, $table);
        }

        return response()->json($table, 201);
    }

    public function show(Table $table): JsonResponse
    {
        return response()->json($table);
    }

    public function tabular(Table $table): JsonResponse
    {
        $data = [];
        $fields = $table->fields;
        foreach ($table->entries as $entry){
            $row = [];
            $row["id"] = $entry["id"];
            foreach ($fields as $field){
                $row[$field["slug"]] = $entry->data()->where("field_id", "=", $field["_id"])->first()->data;
            }

            $data[] = $row;
        }

        return response()->json($data);
    }

    public function fields(Table $table): JsonResponse
    {
        return response()->json($table->fields);
    }

    public function edit(Request $request, Table $table): JsonResponse
    {
        $validator = Validator::make($request->json()->all(), [
            "name" => "required|string|min:3",
            "description" => "required|string|min:10"
        ]);

        if($validator->fails())
            return response()->json($validator->errors(), 400);

        $table = $table->update([
            "name" => $request->json()->get("name"),
            "description" => $request->json()->get("description")
        ]);

        return response()->json($table);
    }

    public function delete(Table $table): JsonResponse
    {
        foreach ($table->fields as $field)
            $field->delete();

        foreach ($table->entries as $entry)
            (new EntryController())->delete($entry);

        try {
            return response()->json($table->delete());
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    public function search($query): JsonResponse
    {
        $query = "%".$query."%";
        return response()->json(auth()->user()->tables()->orWhere("name", "like", $query)
            ->orWhere("description", "like", $query)
            ->get());
    }
}