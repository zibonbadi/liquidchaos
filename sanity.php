<?php

// Sanity checks
if (!extension_loaded('yaml')) {
    echo "\033[31mCRITICAL:\033[0m EXT_YAML not found! Please activate it in your PHP environment!\n";
    exit;
}

if (!extension_loaded('mbstring')) {
    echo "\033[31mCRITICAL:\033[0m EXT_MBSTRING not found! Please activate it in your PHP environment!\n";
    exit;
}

if (!extension_loaded('pdo')) {
    echo "\033[31mCRITICAL:\033[0m EXT_PDO not found! Please activate it in your PHP environment!\n";
    exit;
}

if (!extension_loaded('curl')) {
    echo "\033[31mCRITICAL:\033[0m EXT_CURL not found! Please activate it in your PHP environment!\n";
    exit;
}


// Load settings
$LIQUIDCHAOS_CONFIG = [];
if( PHP_SAPI == "cli" ){
   $LIQUIDCHAOS_CONFIG = yaml_parse_file(__DIR__."/env.yaml", -1);

   if($LIQUIDCHAOS_CONFIG === false){
       echo <<<END
       \033[31mCRITICAL:\033[0m env.yaml not found! Please run 'liquidchaos init'
       or configure your server manually using env.yaml.example!
       END;
       exit;
   }

   // Merge all yaml configs together
   $tmp = [];
   foreach($LIQUIDCHAOS_CONFIG as $docname => $doc) {
      $tmp = array_merge_recursive($tmp, $doc);
   }
   $LIQUIDCHAOS_CONFIG = $tmp;
   unset($tmp);
}

// Configure defaults
if( !array_key_exists("basepath",$LIQUIDCHAOS_CONFIG) ){ $LIQUIDCHAOS_CONFIG["basepath"] = '/'; }
if( array_key_exists("peers",$LIQUIDCHAOS_CONFIG) ){
    foreach( $LIQUIDCHAOS_CONFIG["peers"] as $peer_url => $peer_data){
	echo "- <".$peer_url.">\n";
	if( !array_key_exists("type",$peer_data) ){
	    $peer_data["type"] = 'string';
	    $peer_data["value"] = 'changeme';
	}
    }
    echo "\n";
}else{ $LIQUIDCHAOS_CONFIG["peers"] = null; }

?>
