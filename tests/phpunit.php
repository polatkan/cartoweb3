<?php
/**
 * Unit tests launcher
 *
 * This was originally a PHPUnit2 file called PHPUnit2/pear-phpunit
 * @package Tests
 * @version $Id$
 */

/**
 * Root directory for client scripts
 */
define('CARTOCLIENT_HOME', realpath(dirname(__FILE__) . '/..') . '/');
define('CARTOCOMMON_HOME', CARTOCLIENT_HOME);
define('CARTOSERVER_HOME', CARTOCLIENT_HOME);

// clears include_path, to prevent side effects
ini_set('include_path', '');

require_once(CARTOCOMMON_HOME . 'common/Common.php');
Common::preInitializeCartoweb(array());

set_include_path(get_include_path() . PATH_SEPARATOR . 
                 CARTOCLIENT_HOME . 'tests/');

require 'PHPUnit2/TextUI/TestRunner.php';
?>
