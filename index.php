<?php
require_once __DIR__ . '/vendor/autoload.php';
use Klein\Klein;

// Router object
$router = new Klein();

$testdata = [
    [
	"host" => "localhost:5029",
	"game" => "Sonic Robo Blast 2 Kart",
	"version" => "1.3",
	"name" => "Kart Server",
	"meta" => "cheats",
    ],
    [
	"host" => "localhost:5030",
	"game" => "Sonic Robo Blast 2",
	"version" => "2.2.9",
	"name" => "SRB2 Server",
	"meta" => "files=VCL_LegacyHinote-v3.5.wad,CL_Skip-v1.pk3,VL_BattleMod-v1.1.pk3;mod=Battlemod V1.1",
    ],
    [
	"host" => "localhost:65535",
	"game" => "Minecraft",
	"version" => "1.18.1",
	"name" => "MC server",
	"meta" => "server=bukkit 1.16.1;gamemode=adventure;cheats",
    ],
];

$router->with('/servers', function() use ($router, $testdata) {
    $router->respond('GET', '/?', function($request, $response, $service)
    use ($testdata) {
      $response->header('Content-Type','text/csv;header=absent;charset=UTF-8');

    $filter = array_intersect_key($request->params(), [
	"host" => null,
	"game" => null,
	"version" => null,
	"name" => null,
	"meta" => null,
	"count" => null,
	"page" => null,
    ]);

      $use_pager = array_key_exists("count",$filter);
      $page = (array_key_exists("page",$filter))?intval($filter["page"]):1;

      // Get response data
      $data = fopen("php://temp", 'w');
      $viewcounter = 0;
      foreach($testdata as $row){
	  $match = true;
	  foreach($filter as $f_key => $f_value){
	      switch($f_key){
		  case "name": // Contain-Match names for comfortable searches
		  case "meta":
		      {
			  // toUpper to make searches more comfortable
			  if( !str_contains( strtoupper($row[$f_key]), strtoupper($f_value) ) ){ $match = false; }
			  break;
		      }
		  case "count":
		  case "page":
		      { break; } // Ignore pager
		  default:{
		      if($row[$f_key] != $f_value){ $match = false; }
		      break;
		  }
	      }
	  }
	  if(!$match){ continue; } // Skip unmatching

	  if($use_pager){
	    if( $viewcounter >= intval($filter["count"])*($page-1) && 
	        $viewcounter < intval($filter["count"])*$page 
	    ){ 
	    fputcsv($data, $row); }
	  }else{ fputcsv($data, $row); }
	  $viewcounter++;
      }
      rewind($data);

      // Return table
      return stream_get_contents($data);
    });

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

/*
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
*/


$router->dispatch();
?>

