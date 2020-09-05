<?php

namespace App\Http\Controllers;

use App\Api;
use App\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function add(Table $table)
    {
        if($table->api)
            return response()->json(["This table already has Api "]);

        return response()->json($table->api()->create([
            "header_key" => "random_key",
            "slug" => $table->code,
            "can_read" => false,
            "can_write" => false,
            "can_delete" => false,
            "active" => true
        ]));
    }

    public function activate(Api $api) {
        return $api->update(["active" => !$api["active"]]);
    }

    public function edit(Api $api, Request $request){
        $validator = Validator::make($request->json()->all(), [
            "slug" => "required|string|min:5|unique:api",
            "can_read" => "required|boolean",
            "can_write" => "required|boolean",
            "can_delete" => "required|boolean",
            "active" => "required|boolean"
        ]);

        if($validator->fails())
            return response()->json($validator->errors(), 400);

        return response()->json($api->update([
            "slug" => $request->json()->get("slug"),
            "can_read" => $request->json()->get("can_read"),
            "can_write" => $request->json()->get("can_write"),
            "can_delete" => $request->json()->get("can_delete"),
            "active" => $request->json()->get("active")
        ]));
    }
}
