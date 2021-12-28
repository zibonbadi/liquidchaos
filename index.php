<?php
require_once __DIR__ . '/vendor/autoload.php';
use Klein\Klein;

// Router object
$router = new Klein();

$testdata = [
"host" => "localhost:5029",
"game" => "Sonic Robo Blast 2",
"version" => "2.2.9",
"name" => "SRB2 Server",
"meta" => "files=VCL_LegacyHinote-v3.5.wad,CL_Skip-v1.pk3,VL_BattleMod-v1.1.pk3;mod=Battlemod V1.1",
];

$router->with('/servers', function() use ($router, $testdata) {
    $router->respond('GET', '/?', function($request, $response, $service)
    use ($testdata) {
      $response->header('Content-Type','text/csv;header=absent;charset=UTF-8');

      // Get response data
      $data = fopen("php://temp", 'w');
      fputcsv($data, $testdata);
      rewind($data);

      // Generate response
      return "GET recognized: "
	  .var_export($request->params())
	  ."\n".stream_get_contents($data);
    });
    /*
    $router->respond('GET', '/[:id]', function($request) {
      $response->header('Content-Type','text/csv;header=absent;charset=UTF-8');
      return "GET recognized {$request->id}";
    });
    */

    $router->respond('POST', '/?', function($request, $response) {
      $response->header('Content-Type','text/csv;header=absent;charset=UTF-8');
      return "POST recognized";
    });
 
    $router->respond('PUT', '/[:id]', function($request) {
      // No PUT response bodies per HTTP standard
    });

    $router->respond('DELETE', '/[:id]', function($request) {
      // HTTP standard is ambiguous on response bodies
      #$response->header('Content-Type','text/plain');
      #return "DELETE recognized {$request->id}";
    });

    // API index
    $router->respond('OPTIONS', '/?[**:path]?', function($request, $response) {
      $response->header('Content-Type','text/plain');
      $response->header('Allow','OPTIONS, GET, PUT, POST, DELETE');
      return "OPTIONS recognized {$request->path}";
    });
});

$router->with('/auth', function() use ($router) {
    // Auth dummies
    $router->respond('OPTIONS', '/?[**:path]?', function($request, $response) {
      $response->header('Content-Type','text/plain');
      switch($router->path){
	  case '':{
	      $response->header('Allow','OPTIONS, GET, POST');
	      break;
	  }
	  default:{
	      $response->header('Allow','OPTIONS');
	      break;
	  }
      }
      return "OPTIONS recognized '{$request->path}'";
    });
});

// Failsafe
$router->respond(['GET', 'OPTIONS'], '/?[**:path]?', function($request, $response) {
   $response->header('Content-Type','text/plain');
   $response->code(404);
   switch($request->action){
       case 'OPTIONS':{
	   $response->header('Allow','OPTIONS, GET');
	   break;
       }
       case 'GET':{
	   break;
       }
   };
   return "Not a chaosnet route. Try /servers";
});


$router->dispatch();
?>

