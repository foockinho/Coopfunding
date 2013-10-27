<?php

$rewardbook_guid = get_input('guid');
$rewardbook = get_entity($rewardbook_guid);

if (!$rewardbook) {forward('', '404');}

elgg_load_library('elgg:campaign_reward');
$reward_guid = campaign_reward_get_reward_or_transaction ($rewardbook_guid);  

$entity = get_entity($rewardbook->container_guid);

elgg_load_library('coopfunding:fundraising');
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

	$transaction->eur_amount = $rewardbook->amount;
	
	$transaction->contributor = $rewardbook->contributor;
	$transaction->commit_date = $rewardbook->time_created;
    	$transaction->save();

    	$contributions_set->eur_amount += $rewardbook->amount;
    	$entity->eur_amount += $rewardbook->amount;
	
	remove_entity_relationships($rewardbook_guid, "reward");		 
	add_entity_relationship ($transaction->guid, 'reward', $reward_guid);

	$params = array('transaction_guid' => $transaction->guid, 'reward_guid' => $reward_guid);	
	elgg_trigger_plugin_hook('fundraising:rewards:save', 'campaign_reward', $params);	

	$rewardbook->delete();	

    	system_message(elgg_echo('campaign_reward:reward_book:success'));	

} else {
	register_error(elgg_echo('campaign_reward:reward_book:error'));	
}
   

  
    
  
  
