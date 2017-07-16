<?php
require_once 'config.php';

use Kymyzek\WebPhpDemon\WebPhpDemon;
use Kymyzek\WebPhpDemon\WebPhpScript;

$interface = 'test_interface_1';
$wps = new WebPhpScript($interface);

$params = array(
    'dir'=>D_ROOT,
    'name'=>'test_1.php',
    'wait'=>10,
);
$wps->params($params);
$wpd = new WebPhpDemon();
$demon = array(
    'dir'   =>  D_ROOT,
);
$wpd->setParam($demon);
$wpd->run();

$interface = 'test_interface_2';
$wps = new WebPhpScript($interface);
$params['name'] = 'test_2.php';
$params['wait'] = 15;
$params['log'] = 'info';
$wps->params($params);

