<?php

$rewardbook_guid = get_input('guid');
$rewardbook = get_entity($rewardbook_guid);

elgg_load_library('elgg:campaign_reward');
$reward = campaign_reward_get_reward_or_transaction ($rewardbook_guid); //TODO Better name for this function, get_entity_relationship 
$reward_guid = $reward->guid;

if (!$rewardbook) {forward('', '404');}

if ($rewardbook->delete()) {
		remove_entity_relationships($rewardbook_guid, "reward");
		system_message(elgg_echo('campaign_reward:reward_book:notdeleted'));	
} else {
	    register_error(elgg_echo('campaign_reward:reward_book:notdeleted'));	
}
forward(REFERER);
