<?php

$entity_guid = get_input('entity_guid');
elgg_load_library('coopfunding:fundraising:bitcoin');
$address = fundraising_bitcoin_get_address(277);
echo $address;
exit();
