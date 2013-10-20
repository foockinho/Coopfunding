<?php

$rewardbook_guid = get_input('guid');
$rewardbook = get_entity($rewardbook_guid);
var_dump($rewardbook_guid); 
elgg_load_library('elgg:campaign_reward');
$reward_guid = campaign_reward_get_reward_or_transaction ($rewardbook_guid); //TODO Better name for this function, get_entity_relationship 


if (!$rewardbook) {forward('', '404');}

elgg_load_library('coopfunding:fundraising');
var_dump($rewardbook->container_guid);  
$contributions_set = fundraising_get_contributions_set($rewardbook->container_guid, $rewardbook->contributor);

if (!$contributions_set) {
	$contributions_set = new ElggObject();
	$contributions_set->subtype = "contributions_set";
	$contributions_set->owner_guid = $rewardbook->contributor;
	$contributions_set->container_guid = $rewardbook->container_guid;
}

if ($contributions_set_guid = $contributions_set->save()) {

	$transaction = new ElggObject();
	$transaction->type = 'object';
	$transaction->subtype = "transaction";
	$transaction->method = $rewardbook->method;
   
	$transaction->owner_guid = elgg_get_logged_in_user_guid();
	$transaction->container_guid = $rewardbook->container_guid;
var_dump($rewardbook->container_guid); 
	$transaction->eur_amount = $rewardbook->amount;
	
	$transaction->contributor = $rewardbook->contributor;
	$transaction->commit_date = elgg_view_friendly_time ($rewardbook->time_created);
    $transaction->save();

    $contributions_set->eur_amount += $amount;
    $container->eur_amount += $amount;
	remove_entity_relationships($rewardbook_guid, "reward");		
var_dump("se hace ". $transaction->guid . " / reward / " . $reward_guid); 
	add_entity_relationship ($transaction->guid, 'reward', $reward_guid);

	$params = array('transaction_guid' => $transaction->guid, 'reward_guid' => $reward_guid);		
var_dump("action_commit "); var_dump($params);
	elgg_trigger_plugin_hook('fundraising:rewards:save', 'campaign_reward', $params);	

	$rewardbook->delete();	

    system_message(elgg_echo('campaign_reward:reward_book:success'));	

} else {
	register_error(elgg_echo('campaign_reward:reward_book:error'));	
}
   

  
    
  
  
