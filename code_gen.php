<?php

/*
 * code_gen.php
 * Code Auto-Generation Utilities
 * 
 * Useage-
 *    php code_gen.php
 * 
 */

use GetOpt\GetOpt;
use GetOpt\Option;
use GetOpt\ArgumentException;
use GetOpt\ArgumentException\Missing;

require __DIR__ . '/../vendor/autoload.php';

define('NAME', '365admin code gen');
define('VERSION', '1.0-alpha');


$getOpt = new \GetOpt\GetOpt([
    
    \GetOpt\Option::create('c', 'command', \GetOpt\GetOpt::REQUIRED_ARGUMENT)
    ->setDescription('Command to execute:'),
    
    \GetOpt\Option::create('t', 'table', \GetOpt\GetOpt::REQUIRED_ARGUMENT)
    ->setDescription('DB table'),

    \GetOpt\Option::create(null, 'help', GetOpt::NO_ARGUMENT)
    ->setDescription('Show help text'),

    \GetOpt\Option::create(null, 'version', GetOpt::NO_ARGUMENT)
    ->setDescription('Show version information and quit'),
]);


// process arguments and catch user errors
try {
    try {
        $getOpt->process();
    } catch (Missing $exception) {
        // catch missing exceptions if help is requested
        if (!$getOpt->getOption('help')) {
            throw $exception;
        }
    }
} catch (ArgumentException $exception) {
    file_put_contents('php://stderr', $exception->getMessage() . PHP_EOL);
    echo $exception->getMessage();
    echo PHP_EOL . $getOpt->getHelpText();
    exit;
}

// show version and quit
if ($getOpt->getOption('version')) {
    echo sprintf('%s: %s' . PHP_EOL, NAME, VERSION);
    exit;
}

// show help and quit
if ($getOpt->getOption('help')) {
    echo $getOpt->getHelpText();
    exit;
}

die();

try {

    $getopt->process();
    
    $cmd = $getopt->getOption('command');
    $table = $getopt->getOption('table');
    
    print "cmd: ".$cmd."\n";
    print "table: ".$table."\n";

    switch ($cmd) {
        
        case "generate-class" :
            break;
        
    }
    
} catch (Exception $e) {
   die($e->getMessage());
}

?>
