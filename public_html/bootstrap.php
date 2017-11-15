<?php

// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';

// Load API class
$class = $_REQUEST['class'];
require_once 'library/Api.php';
require_once 'library/Config.php';
