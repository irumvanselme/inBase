<?php

namespace App\Http\Controllers;

use App\Field;
use App\Table;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FieldController extends Controller
{
    public function add(Table $table, Request $request){
        $this->create($request->json()->all(), $table);
    }

    public function create($field_data, Table $table){
        $validator = $this->_validate($field_data);

        if ($validator->fails())
            return response()->json($validator->errors(), 400);

        $field = $table->fields()->create($field_data);

        return response()->json($field, 201);
    }

    public function edit(Request $request,Field $field){
        $validator = $this->_validate($request->json()->all());

        if ($validator->fails())
            return response()->json($validator->errors(), 400);

        $field = $field->update($request->json()->all());

        return response()->json($field);
    }

    public function delete(Field $field){
        try {
            return response()->json($field->delete(), 201);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    public function update_fields(Request $request, Table $table){
        $validator = Validator::make($request->json()->all(), [
            "fields" => "required|array|min:2"
        ]);

        if($validator->fails())
            return response()->json($validator->errors(), 400);

        $fields = $request->json()->get("fields");

        $fields_s = [];
        foreach ($fields as $field){
            if(is_array($field)) {
                $has_id = array_key_exists("_id", $field) ? false : true;
                $validator = $this->_validate($field, $has_id);

                if ($validator->fails())
                    return response()->json($validator->errors(), 400);

                $fields_s[] = $field["name"];
            }else
                return response()->json(["message"=>"Unsupported field type format"], 400);
        }

        foreach ($fields_s as $string)
            if(array_count_values($fields_s)[$string] > 1)
                return response()->json(["fields"=>["Every field name must be unique "]]);

        $field_id = 0;
        foreach ($fields as $field)
            array_key_exists("_id", $field) ? $field_id++ : true;

        if(sizeof($tf = $table->fields) !== $field_id){
            foreach ($tf as $item){
                $cts = 0;
                foreach ($fields as $field)
                    if(in_array($item["_id"], $field, true))
                        $cts++;
                if($cts === 0)
                    $item->delete();
            }
        }

        foreach ($fields as $field)
            array_key_exists("_id", $field) ? Field::query()->find($field["_id"])->update($field) : $table->fields()->create($field);

        return response()->json(['status'=>true]);
    }

    public function _validate($data, $default = false){
        return Validator::make($data, [
            "name" => "required|string|min:3|max:100",
            "type" => "required|string|min:3|max:100|in:string,int,file,date",
            "unique" => "required|boolean",
            "default" => $default ? "required|string|min:1" : "",
            "required" => "required|boolean",
            "minimum" => "required|int|min:0",
            "maximum" => "required|int|min:1",
            "data_in" => "array",
            "data_not_in" => "array",
        ]);
    }
}