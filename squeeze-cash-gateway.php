<?php

/*
Plugin Name: Squeeze Cash Gateway
Plugin URI: https://squeeze.cash
Description: Accept payments with Squeeze Cash.
Version: 1.0
Author: Squeeze Cash
Author URI: https://squeeze.cash
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: squeeze-cash
*/

class Squeeze_Cash_Gateway {
	private static $instance;

	/**
	 * Squeeze_Cash_Gateway constructor.
	 */
	public function __construct() {
		$this->isWooCommerceActive();

		add_action('init', 'Squeeze_Cash_Gateway::wc_squeeze_cash_init');

		add_filter('woocommerce_payment_gateways', array($this, 'add_squeeze_cash_gateway'));
	}

	/**
	 * @return mixed
	 */
	public static function getInstance() {
		if(self::$instance == NULL){
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function wc_squeeze_cash_init(){
		if(class_exists('WC_Payment_Gateway')){
			require_once plugin_dir_path(__FILE__) . './includes/wc_squeeze_cash_gateway.php';
		}

	}

	public function add_squeeze_cash_gateway($gateways){
		$gateways[] = 'WC_Squeeze_Cash_Gateway';
		return $gateways;
	}

	public static function activate(){
		if(version_compare(get_bloginfo('version'), '5.0', '<')){
			wp_die("You must update WordPress to use this plugin.", 'squeeze-cash');
		}
	}

	private function isWooCommerceActive(){
		if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) return;
	}
}
Squeeze_Cash_Gateway::getInstance();

define('SQUEEZE_CASH_PLUGIN_URL', plugin_dir_url(__FILE__));

register_activation_hook(__FILE__, 'Squeeze_Cash_Gateway::activate');

