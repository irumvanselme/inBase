<?php
namespace App\Http\Controllers;

use App\Table;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    public function index(){
        return response()->json(auth()->user()->tables()->get(["name", "code", "star", "description"]));
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->json()->all(), [
            "name" => "required|string|min:3|max:100",
            "description" => "string|min:10|max:255",
            "fields" => "required|array|min:2"
        ]);

        if($validator->fails())
            return response()->json($validator->errors(), 400);

        if(auth()->user()->tables()->where("name","=",$request->json()->get("name"))->first())
            return response()->json(["name"=>["Table name already registered in your tables "]],400);

        $fields = $request->json()->get("fields");

        $fields_s = [];
        foreach ($fields as $field){
            if(is_array($field)) {
                $validator = (new FieldController())->_validate($field);

                if ($validator->fails())
                    return response()->json($validator->errors(), 400);

                $fields_s[] = $field["name"];
            }else
                return response()->json(["message"=>"Unsupported field type format"], 400);
        }

        foreach ($fields_s as $string)
            if(array_count_values($fields_s)[$string] > 1)
                return response()->json(["fields"=>["Every field name must be unique "]]);

        $table = auth()->user()->tables()->create([
            "code" => uniqid(),
            "name" => $request->json()->get("name"),
            "published" => false,
            "star" => false,
            "description" => $request->json()->get("description"),
            "has_api" => false,
            "__data_counter" => 0
        ]);

        foreach ($fields as $field) {
            (new FieldController())->create($field, $table);
        }

        return response()->json($table, 201);
    }

    public function show(Table $table){
        return response()->json($table);
    }

    public function fields(Table $table){
        return response()->json($table->fields);
    }

    public function edit(Request $request, Table $table){
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

    public function star(Table $table){
        return response()->json($table->update([ "star" => !$table->star ]));
    }

    public function delete(Table $table)
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

    public function code($code)
    {
        $table = Table::query()->where("code","=",$code)->get()->first();
        if(!$table) return response()->json(["message" => "Table not found "], 404);
        $entries = [];
        $table->fields;
        foreach ($table->entries as $entry) {
            $entry_r = [];
            $entry_r["id"] = $entry["id"];
            foreach ($table->fields as $field) {
                $entry_r[$field["slug"]] = $entry->data()->where("field_id", $field["_id"])->get()->first()["data"];
            }
            $entries[] = $entry_r;
        }

        return response()->json(compact("table", "entries"));
    }

    public function publish(Table $table)
    {
         return response()->json($table->update(["published" => !$table->published ]));
    }

    public function add_form(Table $table){
        return $table->forms()->create(["auth-key"=>"random_key"]);
    }

    public function search($query){
        $query = "%".$query."%";
        return response()->json(auth()->user()->tables()->orWhere("name", "like", $query)
            ->orWhere("description", "like", $query)
            ->get());
    }
}