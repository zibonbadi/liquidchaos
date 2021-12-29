<?php
echo <<<END
liquidchaos CLI
===============

Options
-------


END;

foreach( scandir(__DIR__) as $cmd ){
$cmdname = basename($cmd, '.php');
echo <<<END
$cmdname [ARGS]
: Dummy desc


END;
}
?>

