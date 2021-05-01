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

    Route::group(["prefix" => "databases"], function () {
        Route::get("/", "DatabaseController@index");
        Route::post("/", "DatabaseController@create");
        Route::group(["prefix" => "{database}"], function () {
            Route::get("", "DatabaseController@show");
            Route::put("", "DatabaseController@update");
            Route::delete("", "DatabaseController@delete");
            Route::group(["prefix" => "tables"], function () {
                Route::get("", "TableController@index");
                Route::post("", "TableController@create");
            });
        });
    });

    Route::group(["prefix" => "fields"], function (){
        Route::post("{table}", "FieldController@store");
    });

    Route::group([ "prefix" => "tables" ], function(){
        Route::get("search/{query}", "TableController@search");
        Route::group(["prefix" => "{table}"], function () {
            Route::get("", "TableController@show");
            Route::get("fields", "TableController@fields");
            Route::get("tabular", "TableController@tabular");
            Route::get("entries", "EntryController@index");
            Route::post("entries", "EntryController@create");
            Route::put("", "TableController@edit");
            Route::delete("", "TableController@delete");
        });
    });

    Route::group([ "prefix" => "entries" ], function () {
        Route::post("{table}", "EntryController@create");
        Route::get("{table}/search/{querry}", "EntryController@search");
        Route::get("{entry}", "EntryController@show");
        Route::put("{entry}", "EntryController@update");
        Route::delete("{entry}", "EntryController@delete");
    });

    Route::group(["prefix" => "o"], function (){
        Route::group(["prefix" => "t/{table}"], function (){
            Route::get("", "TableController@tabular");
            Route::post("", "EntryController@create");
        });
        Route::group(["prefix" => "e/{entry}"], function (){
            Route::get("", "EntryController@details");
            Route::put("", "EntryController@update");
            Route::delete("", "EntryController@delete");
        });
    });
});