<?php
    
require('autoload.php');

global $lumise;

if ($lumise->connector->platform == 'php') {
    $lumise->do_action('api_request');
}
