<?php
$cli_in = fopen('php://stdin', 'r');

echo <<<END
liquidchaos init
================


END;

// Configure defaults
if( !array_key_exists("basepath",$LIQUIDCHAOS_CONFIG) ){ $LIQUIDCHAOS_CONFIG["basepath"] = '/'; }

// Confirm final settings
$yaml_out = yaml_emit($LIQUIDCHAOS_CONFIG);

echo <<<END
Final settings
--------------

{$yaml_out}

Are these settings correct [type 'yes' to confirm]: 
END;

$input = fgets($cli_in);
if(strtolower(trim($input)) == 'yes'){
    #yaml_emit_file(__DIR__.'/../env.yaml',$LIQUIDCHAOS_CONFIG);
    echo "\n\033[32mConfiguration successfully created!\033[0m \n";
}

?>
