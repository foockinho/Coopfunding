<?php

elgg_load_library('coopfunding:fundraising:bitcoin');
$address = fundraising_bitcoin_get_address($vars['entity']->guid, elgg_get_logged_in_user_guid());

//show btc address and qr image
echo '<div>';
echo elgg_echo("fundraising:bitcoin:contributeToaddress", array($address));
echo '<br>' . elgg_echo("fundraising:bitcoin:or") . '<br>';
echo elgg_echo("fundraising:bitcoin:contributeToQRcode");
echo '<br>';
echo '<img src="https://blockchain.info/es/qr?data=' . $address . '&size=200">';
echo '</div>';

echo '<div>';
echo elgg_echo("fundraising:bankaccount:message");
echo '</div>';

echo elgg_view('output/url', array(
	'href' => "fundraising/contribute/{$vars['entity']->guid}?amount={$vars['amount']}",
	'text' => "&laquo; " . elgg_echo('back'),
	'class' => 'elgg-button elgg-button-action',
));

echo elgg_view('output/url', array(
	'href' => $vars['entity']->getURL(),
	'text' => elgg_echo('Confirm'),
	'class' => 'elgg-button elgg-button-action',
));