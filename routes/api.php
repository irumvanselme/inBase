<?php

Route::post("/register", "UserController@register");
Route::get("/check-email", "UserController@check_email");
Route::post("/login", "UserController@login");

Route::group([ "middleware" => "jwt.verify" ] ,function (){
    Route::group(["prefix" => "auth"], function () {
        Route::get("", "UserController@profile");
        Route::put("/edit", "UserController@edit");
        Route::put("/edit-password", "UserController@edit_password");
        Route::delete("/delete", "UserController@delete");
    });

    Route::group([ "prefix" => "tables" ], function(){
        Route::get("", "TableController@index");
        Route::post("", "TableController@create");
        Route::get("/code/{code}", "TableController@code");
        Route::get("search/{query}", "TableController@search");
        Route::group(["prefix" => "{table}"], function () {
            Route::get("", "TableController@show");
            Route::get("forms", "FormController@index");
            Route::get("fields", "TableController@fields");
            Route::post("forms", "FormController@create");
            Route::put("", "TableController@edit");
            Route::put("fields", "FieldController@update_fields");
            Route::put("star", "TableController@star");
            Route::delete("", "TableController@delete");
        });
    });

    Route::group([ "prefix" => "entries" ], function () {
        Route::get("", "EntryController@index");
        Route::post("{table}", "EntryController@create");
        Route::get("{table}/search/{querry}", "EntryController@search");
        Route::get("{entry}", "EntryController@show");
        Route::put("{entry}", "EntryController@update");
        Route::delete("{entry}", "EntryController@delete");
    });

    Route::group(["prefix" => "apis"], function (){
        Route::post("{table}", "ApiController@add");
        Route::put("{api}/activate", "ApiController@activate");
        Route::put("{api}", "ApiController@edit");
    });
});