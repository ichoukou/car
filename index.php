<?php
$_config_file = __DIR__ . DIRECTORY_SEPARATOR . 'Libs/Configs' . DIRECTORY_SEPARATOR . 'Config.php';
$_init_file = __DIR__ . DIRECTORY_SEPARATOR . 'Libs/Init.php';

if(!is_file($_config_file) or !is_file($_init_file)) return false;

require_once $_config_file;
require_once $_init_file;

$init = new Init($_config);







