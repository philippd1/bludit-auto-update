<?php
$statusfile = dirname(__FILE__) . '/status.json';
$response = "no update in progress";
if (file_exists($statusfile)) {
    $response = file_get_contents($statusfile);
}
$response = str_replace("\n", "<br>", $response);
echo $response;
