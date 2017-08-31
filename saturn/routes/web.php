<?php

Route::get('/', function () {
    return "SATURN";
});


Route::get('/passengers', function () {
    $passengers = \App\Passenger::where("name", "like", request("query") . "%")
        ->get();

    return $passengers;

//    return $passengers->map(function($passenger) {
//        return collect($passenger->toArray() + ["num" => $passenger->id])->except("id");
//    })->all();
});
