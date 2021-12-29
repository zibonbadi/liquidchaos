<?php
echo <<<END
Some configurations may have been altered to a default failsafe.
Please check this YAML excerpt to verify that everything is in order:


END;

// Configure defaults
if( !array_key_exists("basepath",$config) ){ $config["basepath"] = '/'; }

// Display final settings
echo yaml_emit($config)."\n";

?>
