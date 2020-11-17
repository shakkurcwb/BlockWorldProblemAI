<?php

require_once('./App/Processor.php');

#
# Input File Here (on Windows, use double backslash)
#
$inputFile = "C:\\Users\\Admin\\Desktop\\NewWorld\\inputFileExample";

$processor = new App\Processor($inputFile);
$processor->execute();

#
# Utils
#

function dump(...$val) {
    print '<pre>'; foreach ($val as $n) { var_dump($n); }
}

function dd(...$val) {
    dump(...$val); exit;
}
