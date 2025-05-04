<?php

const VG_ACCESS = true;

header('Content-Type: text/html; charset=utf-8');
session_start();

//error_reporting(0);

require_once 'config.php';
require_once 'core/base/settings/internal_settings.php';
require_once 'libraries/functions.php';

use core\base\exceptions\RouteException;
use core\base\controller\BaseRoute;
use core\base\exceptions\DBException;

try {
    BaseRoute::routeDirection();
} catch (RouteException $e) {
    exit($e->getMessage());
} catch (DBException $e) {
    exit($e->getMessage());
}