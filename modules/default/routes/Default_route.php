<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Route::get("default_controller","test/route");

Route::prefix("test", function(){
  Route::get("route","test/route",['as' => 'test']); // => Route::named('test')
  Route::get("route/{id}","test/routeByID/$1");
  Route::get("route/{id}/{name}","test/routeByMultiParams/$1/$2");

  Route::get("route-sub","sub/sub2/sub3/home/index",['before' => function(){
    if ("authed" === "not authed") {
      echo "not authed";
      die;
    }
  }]);
});

Route::get("products","products");
Route::prefix("products", function(){
  Route::get("route/{id}","test/routeByID/$1");
  Route::get("route/{id}/{name}","test/routeByMultiParams/$1/$2");
  Route::get("create","products/create");
  Route::post("create","products/create");

  Route::get("route-sub","sub/sub2/sub3/home/index",['before' => function(){
    if ("authed" === "not authed") {
      echo "not authed";
      die;
    }
  }]);
});
Route::get("categories","categories");
Route::prefix("categories", function(){
  Route::get("route/{id}","test/routeByID/$1");
  Route::get("route/{id}/{name}","test/routeByMultiParams/$1/$2");
  Route::get("all","categories/all");
  Route::post("add","categories/add");
  Route::put("update/(:num)", "categories/update/$1");
  Route::delete("delete/(:num)","categories/delete/$1");
  Route::get("route-sub","sub/sub2/sub3/home/index",['before' => function(){
    if ("authed" === "not authed") {
      echo "not authed";
      die;
    }
  }]);
});
Route::get("products", "products");
Route::prefix("products", function(){
  Route::get("route/{id}", "test/routeByID/$1");
  Route::get("route/{id}/{name}", "test/routeByMultiParams/$1/$2");
  Route::get("all", "products/all");
  Route::post("add", "products/add");
  Route::put("update/(:num)", "products/update/$1");
  Route::delete("delete/(:num)", "products/delete/$1");
  Route::get("route-sub", "sub/sub2/sub3/home/index", ['before' => function(){
    if ("authed" === "not authed") {
      echo "not authed";
      die;
    }
  }]);
});
Route::get("users", "users");
Route::prefix("users", function(){
  Route::get("route/{id}", "test/routeByID/$1");
  Route::get("route/{id}/{name}", "test/routeByMultiParams/$1/$2");
  Route::get("all", "users/all");
  Route::post("add", "users/add");
  Route::put("update/(:num)", "users/update/$1");
  Route::delete("delete/(:num)", "users/delete/$1");
  Route::get("route-sub", "sub/sub2/sub3/home/index", ['before' => function(){
    if ("authed" === "not authed") {
      echo "not authed";
      die;
    }
  }]);
});
Route::get("basket", "basket");
Route::prefix("basket", function(){
  Route::get("route/{id}", "test/routeByID/$1");
  Route::get("route/{id}/{name}", "test/routeByMultiParams/$1/$2");
  Route::get("all/(:num)", "basket/all/$1");
  Route::post("add", "basket/add");
  Route::put("update/(:num)", "basket/update/$1");
  Route::put("remove", "basket/remove/");
  Route::get("route-sub", "sub/sub2/sub3/home/index", ['before' => function(){
    if ("authed" === "not authed") {
      echo "not authed";
      die;
    }
  }]);
});
Route::get("orders", "orders");
Route::prefix("orders", function(){
  Route::get("route/{id}", "test/orderByID/$1");
  Route::get("route/{id}/{name}", "test/orderByMultiParams/$1/$2");
  Route::get("all/(:num)", "orders/all/$1");
  Route::post("add", "orders/add");
  Route::get("route-sub", "sub/sub2/sub3/home/index", ['before' => function(){
    if ("authed" === "not authed") {
      echo "not authed";
      die;
    }
  }]);
});
