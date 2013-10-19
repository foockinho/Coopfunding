<?php

$credentials = elgg_get_config('paypalcredentials');

if (!isset($credentials['USER']) || !isset($credentials['PWD']) || !isset($credentials['SIGNATURE'])) {
	$url = elgg_get_site_url() . "admin_plugin_text_file/fundraising-paypal/README.md";
	register_error(elgg_echo("fundraising:paypal:nocredentials", array($url)));
	return false;
}
