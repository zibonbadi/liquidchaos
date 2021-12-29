<?php

// Sanity checks
if (!extension_loaded('yaml')) {
    echo "CRITICAL: EXT_YAML not found! Please activate it in your PHP environment!\n";
    exit;
}

if (!extension_loaded('mbstring')) {
    echo "CRITICAL: EXT_MBSTRING not found! Please activate it in your PHP environment!\n";
    exit;
}

if (!extension_loaded('curl')) {
    echo "CRITICAL: EXT_CURL not found! Please activate it in your PHP environment!\n";
    exit;
}


// Load settings
$config = [];
if( PHP_SAPI == "cli" ){
   $config = yaml_parse_file(__DIR__."/env.yaml", -1);

   if($config === false){
       echo "CRITICAL: env.yaml not found! Please configure your server using env.yaml.example!\n";
       exit;
   }

   // Merge all yaml configs together
   $tmp = [];
   foreach($config as $docname => $doc) {
      $tmp = array_merge_recursive($tmp, $doc);
   }
   $config = $tmp;
   unset($tmp);
}
?>
