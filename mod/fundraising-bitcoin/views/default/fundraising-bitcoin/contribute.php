<?php

$address = $vars["address"]; 

echo elgg_view('input/hidden', array(
			'name' => "entity_guid",
			'id' => "entity_guid",
	                'value' => $vars['entity']->guid
			));

//show btc address and qr image
echo "<div class='fundraising-bitcoin-contribute-form fundraising-hidden'>";
echo "<hr>";
	echo '<div>';
	echo elgg_echo("fundraising:bitcoin:contributeToaddress");
	echo "<label id='bitcoin_address'></label>";
	echo '<br>' . elgg_echo("fundraising:bitcoin:or") . '<br>';
	echo elgg_echo("fundraising:bitcoin:contributeToQRcode");
	echo '<br>';
	echo '<img id="bitcoin_qrcode">';
	echo '</div>';

	echo '<div>';
	echo elgg_echo("fundraising:bankaccount:message");
	echo '</div>';

	echo elgg_view('input/submit', array(
		'name' => 'method',
                'value' => elgg_echo('fundraising:contribute:button:method', array('bitcoin')),
	));
echo "</div>";




