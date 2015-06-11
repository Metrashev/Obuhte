<?php

/**
 * Plugin Name: WooCommerce Custom Product Tabs Lite
 * Plugin URI: 
 * Description: Extends WooCommerce to add a custom product view page tab
 * Author: 
 * Author URI: 
 * Version: 1.2.5
 * Tested up to: 3.5
 * Text Domain: woocommerce-custom-product-tabs-lite
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package     WC-Custom-Product-Tabs-Lite
 * @author      
 * @category    Plugin
 * @copyright   Copyright (c), , Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
	exit;

// Check if WooCommerce is active and bail if it's not
if (!WooCommerceCustomProductTabsLite::is_woocommerce_active()) {
	return;
}

/**
 * The WooCommerceCustomProductTabsLite global object
 * @name $woocommerce_product_tabs_lite
 * @global WooCommerceCustomProductTabsLite $GLOBALS['woocommerce_product_tabs_lite']
 */
$GLOBALS['woocommerce_product_tabs_lite'] = new WooCommerceCustomProductTabsLite();

class WooCommerceCustomProductTabsLite {

	private $tab_data = false;
	private $tab_data2 = false;

	/** plugin version number */

	const VERSION = "1.2.5";

	/** plugin text domain */
	const TEXT_DOMAIN = 'woocommerce-custom-product-tabs-lite';

	/** plugin version name */
	const VERSION_OPTION_NAME = 'woocommerce_custom_product_tabs_lite_db_version';

	/**
	 * Gets things started by adding an action to initialize this plugin once
	 * WooCommerce is known to be active and initialized
	 */
	public function __construct() {
		// Installation
		if (is_admin() && !defined('DOING_AJAX'))
			$this->install();

		add_action('init', array($this, 'load_translation'));
		add_action('woocommerce_init', array($this, 'init'));
	}

	/**
	 * Init WooCommerce PDF Product Vouchers when WordPress initializes
	 *
	 * @since 1.2.5
	 */
	public function load_translation() {

		// localization
		load_plugin_textdomain('woocommerce-custom-product-tabs-lite', false, dirname(plugin_basename(__FILE__)) . '/i18n/languages');
	}

	/**
	 * Init WooCommerce Product Tabs Lite extension once we know WooCommerce is active
	 */
	public function init() {

		// frontend stuff
		add_filter('woocommerce_product_tabs', array($this, 'add_custom_product_tabs'));

		// allow the use of shortcodes within the tab content
		add_filter('woocommerce_custom_product_tabs_lite_content', 'do_shortcode');
	}

	/** Frontend methods ***************************************************** */

	/**
	 * Add the custom product tab
	 *
	 * $tabs structure:
	 * Array(
	 *   id => Array(
	 *     'title'    => (string) Tab title,
	 *     'priority' => (string) Tab priority,
	 *     'callback' => (mixed) callback function,
	 *   )
	 * )
	 *
	 * @since 1.2.0
	 * @param array $tabs array representing the product tabs
	 * @return array representing the product tabs
	 */
	public function add_custom_product_tabs($tabs) {
		global $product;
                $tabs['sizesss'] = array(
					'title' => __('Размери', self::TEXT_DOMAIN),
					'priority' => 25,
					'callback' => array($this, 'custom_product_tabs_panel_content'),
					'content' => '[table url="http://obuhte.com/wp-content/uploads/2015/02/sizee.csv"][/table]', // custom field
				);
                $tabs['how-to-sizesss'] = array(
					'title' => __('Как да измерим', self::TEXT_DOMAIN),
					'priority' => 26,
					'callback' => array($this, 'custom_product_tabs_panel_content'),
					'content' => '<img class=" size-full wp-image-2004 alignleft" src="http://obuhte.com/wp-content/uploads/2015/01/sizes-elements.png" alt="sizes-elements" width="760" height="479" />
<h3>С какво да измерите?</h3>
Дължината на стъпалото ще измерите най-точно като използвате ролетка или мек метър.

* При различните марки обувки са възможни разминавания с посочените стандартни мерки.', // custom field
				);

		return $tabs;
	}

	/**
	 * Render the custom product tab panel content for the given $tab
	 *
	 * $tab structure:
	 * Array(
	 *   'title'    => (string) Tab title,
	 *   'priority' => (string) Tab priority,
	 *   'callback' => (mixed) callback function,
	 *   'id'       => (int) tab post identifier,
	 *   'content'  => (sring) tab content,
	 * )
	 *
	 * @param string $key tab key
	 * @param array $tab tab data
	 *
	 * @param array $tab the tab
	 */
	public function custom_product_tabs_panel_content($key, $tab) {

		// allow shortcodes to function
		$content = apply_filters('the_content', $tab['content']);
		$content = str_replace(']]>', ']]&gt;', $content);

		echo apply_filters('woocommerce_custom_product_tabs_lite_heading', '<h2>' . $tab['title'] . '</h2>', $tab);
		echo apply_filters('woocommerce_custom_product_tabs_lite_content', $content, $tab);
	}

	/** Helper methods ***************************************************** */

	/**
	 * Lazy-load the product_tabs meta data, and return true if it exists,
	 * false otherwise
	 *
	 * @return true if there is custom tab data, false otherwise
	 */
	private function product_has_custom_tabs($product) {
		if (false === $this->tab_data) {
			$this->tab_data = maybe_unserialize(get_post_meta($product->id, 'frs_woo_product_tabs', true));
		}
		if (false === $this->tab_data2) {
			$this->tab_data2 = maybe_unserialize(get_post_meta($product->id, 'frs_woo_product_tabs2', true));
		}
		// tab must at least have a title to exist
		return (!empty($this->tab_data) && !empty($this->tab_data[0]) && !empty($this->tab_data[0]['title'])) ||
				!empty($this->tab_data2) && !empty($this->tab_data2[0]) && !empty($this->tab_data2[0]['title']);
	}

	/**
	 * Checks if WooCommerce is active
	 *
	 * @since  1.0
	 * @return bool true if WooCommerce is active, false otherwise
	 */
	public static function is_woocommerce_active() {

		$active_plugins = (array) get_option('active_plugins', array());

		if (is_multisite()) {
			$active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
		}

		return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
	}

	/** Lifecycle methods ***************************************************** */

	/**
	 * Run every time.  Used since the activation hook is not executed when updating a plugin
	 */
	private function install() {

		global $wpdb;

		$installed_version = get_option(self::VERSION_OPTION_NAME);

		// installed version lower than plugin version?
		if (-1 === version_compare($installed_version, self::VERSION)) {
			// new version number
			update_option(self::VERSION_OPTION_NAME, self::VERSION);
		}
	}

}
