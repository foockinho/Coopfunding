<?php
/**
* Fundraising-bitcoin language file
*/

$language = array(

	/**
	* Content
	*/

	'fundraising:bitcoin:title' => "Contribute with bitcoins to %s",
	'fundraising:bitcoin:contributeToaddress' => "Send bitcoins to this address: ",
	'fundraising:bitcoin:contributeToQRcode' => "Scan this QRcode:",
	'fundraising:bitcoin:contributeNoAddress' => "This entity is not configured to recieve bitcoins.",
	'fundraising:bitcoin:or' => 'or',
	'fundraising:contributions:btc' => '%.4f BTC',
	'fundraising:bitcoin:message' => 'By confirming this operation a notification will be send to admins that will wait for 10 days you to make effective the transaction. And also your suitable reward will be reserved during this period of time.',

);
add_translation(basename(__FILE__, '.php'), $language);
