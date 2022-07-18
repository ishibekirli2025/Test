<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Route::get("default_controller","test/route");

Route::prefix("test", function(){
  Route::get("route","test/route",['as' => 'test']); // => Route::named('test')
  Route::get("route/{id}","test/routeByID/$1")->where("id", '[0-9]+');
  Route::get("route/{id}/{name}","test/routeByMultiParams/$1/$2")->where(["id" => "[0-9]+", "name" => "(:any)"]);

  Route::get("route-sub","sub/sub2/sub3/home/index",['before' => function(){
    if ("authed" === "not authed") {
      echo "not authed";
      die;
    }
  }]);
});
