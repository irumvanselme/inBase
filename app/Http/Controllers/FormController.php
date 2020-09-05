<?php

namespace App\Http\Controllers;

use App\Form;
use App\Table;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormController extends Controller
{
    public function index(Table $table){
        return response()->json($table->forms);
    }

    public function show(Form $form){
        return response()->json($form);
    }

    public function create(Request $request, Table $table){
        $validator = Validator::make($request->json()->all(), [
            "host" => "required|url|min:3",
            "title" => "required|string"
        ]);

        if($validator->fails())
            return response()->json($validator->errors());

        $forms = $table->forms()->where("host", "=" , $request->json()
            ->get("host"))->where("title", "=" , $request->json()->get("title"))
            ->get();

        if(sizeof($forms) > 0)
            return response()->json(["api" => ["Form already registered"]]);


        return response()->json($table->forms()->create([
            "auth_key" => md5(rand(1, 1000)),
            "host" => $request->json()->get("host"),
            "title" => $request->json()->get("title")
        ]));
    }

    public function edit(Form $form, Request $request){
        $validator = Validator::make($request->json()->all(), [
            "host" => "required|string|min:3",
            "title" => "required|string"
        ]);

        if($validator->fails())
            return response()->json($validator->errors());

        return response()->json($form->update([
            "auth_key" => "random_key",
            "host" => $request->json()->get("host"),
            "title" => $request->json()->get("title")
        ]));
    }

    public function delete(Form $form){
        try {
            return response()->json($form->delete());
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
