#!/usr/bin/env php
<?php

$output = shell_exec( 'npm run build' );
echo $output.PHP_EOL;
if ( $output === 1 ) {
	exit( 1 );
}