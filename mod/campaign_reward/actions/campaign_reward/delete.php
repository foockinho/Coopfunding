<?php
/**
 * Delete campaign_reward entity
 *
 * @package campaign_reward
 */

$campaign_reward_guid = get_input('guid');
$campaign_reward = get_entity($campaign_reward_guid);

if (elgg_instanceof($campaign_reward, 'object', 'campaign_reward') && $campaign_reward->canEdit()) {
	$container = get_entity($campaign_reward->container_guid);
	if ($campaign_reward->delete()) {
		system_message(elgg_echo('campaign_reward:deleted'));
		forward("campaign_reward/owner/$container->guid");
	} else {
		register_error(elgg_echo('campaign_reward:error:cannot_delete_item'));
	}
} else {
	register_error(elgg_echo('campaign_reward:error:item_not_found'));
}

forward(REFERER);
