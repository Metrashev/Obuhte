<?php    
/*
  Plugin Name: HQ Shortcodes
  Plugin URI: 
  Version: 1.0.0
  Author: 
  Author URI: 
  Description: 
  Text Domain: hq
  Domain Path: /languages
  License: GPL
 */

// Define plugin constants
define('HQ_PLUGIN_FILE', __FILE__);
define('HQ_PLUGIN_VERSION', '1.0.0');
define('HQ_ENABLE_CACHE', false);

// Includes
require_once 'inc/vendor/sunrise.php';
require_once 'inc/core/admin-views.php';
//require_once 'inc/core/requirements.php';
require_once 'inc/core/load.php';
require_once 'inc/core/assets.php';
require_once 'inc/core/shortcodes.php';
require_once 'inc/core/tools.php';
require_once 'inc/core/data.php';
require_once 'inc/core/generator-views.php';
require_once 'inc/core/generator.php';
require_once 'inc/core/widget.php';
//require_once 'inc/core/vote.php';
require_once 'inc/core/counters.php';