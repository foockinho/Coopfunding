<?php

//$gpg = new gnupg();
//$signed_text = $request;
//$plaintext = "":
//$signature = "DD7CD463FE345EB2"; //elgg_get_config('securewallet_fingerprint');

//$info = $gpg -> verify($signed_text, "", $plaintext);

//if ($info[0]['fingerprint'] != $signature) {
//	forward ("", 404);
//}

elgg_load_library('coopfunding:fundraising');
elgg_load_library('coopfunding:fundraising:bitcoin');

$timestamp = get_input('timestamp');
$balance = get_input('balance');
$balance2 = get_input('balance2');
$address = get_input('address');

if (!$address) {
	forward ("", 404);
}

list($entity_guid, $user_guid) = fundraising_bitcoin_get_project_and_user_from_address($address);

if (!$entity_guid || !$user_guid) {
	exit();
}

$entity = get_entity($entity_guid);

$contributions_set = fundraising_get_contributions_set($entity_guid, $user_guid);
$ia = elgg_set_ignore_access(true);
if (!$contributions_set) {
	$contributions_set = new ElggObject();
	$contributions_set->subtype = "contributions_set";
	$contributions_set->owner_guid = $user_guid;
	$contributions_set->container_guid = $entity_guid;
	$contributions_set->save();
}

$difference = $balance - $constributions_set->btc_amount;
if (($difference > 0) && ($timestamp > $contributions_set->timestamp)) {

	$transaction = new ElggObject();
	$transaction->subtype = "transaction";
	$transaction->owner_guid =  $user_guid;
	$transaction->container_guid = $entity_guid;
	$transaction->btc_amount = $difference;
	$transaction->method = 'bitcoin';
	$transaction->contributor = $user_guid;
	$transaction->save();

	$contributions_set->btc_amount = $balance;
	$entity->btc_amount += $difference;

	
}
elgg_set_ignore_access($ia);

error_log("Entity $entity->name received $difference from $address.");
exit();
