<?php

namespace App\Http\Controllers;

use App\Field;

class DataController extends Controller
{
    public function create($entry, Field $field, $data = " "){
        return response()->json($entry->data()->create([
            "field_id" => $field->getKey(),
            "data" => $data
        ]));
    }
}