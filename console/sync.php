<?php
echo <<<"END"
liquidchaos sync
================

Sync this node's database with it's peers through GET requests.


END;

// Configure defaults
if( array_key_exists("peers",$LIQUIDCHAOS_CONFIG) && $LIQUIDCHAOS_CONFIG["peers"] != null ){
    foreach( $LIQUIDCHAOS_CONFIG["peers"] as $peer_url => $peer_data){
	echo "<".$peer_url.">\n"
	     .str_pad("",strlen($peer_url)+2,"-")."\n\n";
    }
}else{
    echo "Missing or invalid peers in env.yaml!\n";
}

?>
