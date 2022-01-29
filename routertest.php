<?php
// Autoload files using composer
require_once __DIR__ . '/vendor/autoload.php';

// Use this namespace
use Steampixel\Route;

// Add your first route
Route::add('/all', function() {
   $routes = Route::getAll();
   foreach($routes as $route) {
     echo $route['expression'].' ('.$route['method'].')'."\n";
   }
}, 'get');

Route::add('/servers', function($id) {
  return "GET recognized {$id}";
}, 'get');

Route::add('/servers/([0-9]+)?', function($id) {
  return "GET recognized {$id}";
}, 'get');

Route::add('/servers', function($id) {
  return "POST recognized {$id}";
}, 'post');

Route::add('/servers/([0-9]+)', function($id) {
  return "PUT recognized {$id}";
}, 'put');

Route::add('/servers/([0-9]+)', function($id) {
  return "DELETE recognized {$id}";
}, 'delete');

// Run the router
Route::run('/');

?>

