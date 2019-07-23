<?php
/*
 * Plugin Name: Маршруты
 * Version: 1.0
 * Description: Карта маршрутов с диаграммой перепадов высот
 * Author: Roman Zhakhov
 * Author URI: https://fpbeat.name
 * Requires at least: 4.6
 * Tested up to: 5.0
 *
 * Text Domain: map-route
 *
 * @package WordPress
 * @author Roman Zhakhov
 * @since 1.0.0
 */

require_once 'vendor/autoload.php';

define('MAP_ROUTE_DEBUG', TRUE);
define('MAP_ROUTE_VERSION', microtime(true));

$mapRouteInstance = MapRoute\Service::instance(__FILE__, MAP_ROUTE_VERSION);
$mapRouteInstance->setSettingsInstance(MapRoute\Admin\Settings::instance());

