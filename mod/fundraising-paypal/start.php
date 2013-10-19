<?php
/**
 * Fundraising plugin
 *
 * @package Coopfunding
 * @subpackage Fundraising.Paypal
 */

elgg_register_event_handler('init', 'system', 'fundraising_paypal_init');

function fundraising_paypal_init() {
	elgg_register_library('coopfunding:fundraising:paypal', elgg_get_plugins_path() . 'fundraising-paypal/lib/fundraising-paypal.php');
	elgg_register_action('fundraising/paypal-callback', dirname(__FILE__) . '/actions/paypal-callback.php', "public");
	elgg_register_plugin_hook_handler('fundraising', 'sum_amount', 'fundraising_paypal_sum_amount');
	
	elgg_register_library('coopfunding:fundraising', elgg_get_plugins_path() . 'fundraising/lib/fundraising.php');
	fundraising_register_method('paypal');
	fundraising_register_currency('eur');
}

function fundraising_paypal_sum_amount($hook, $type, $returnvalue, $params) {
	return (float) $returnvalue + (float) $params['entity']->eur_amount;
}
