<?php
/**
 * Delete a campaign
 *
 * @package Coopfunding
 * @subpackage fundcampaigns
 */

$guid = (int) get_input('guid');
$entity = get_entity($guid);
$project_guid = $entity->container_guid;

if (!$entity->canEdit()) {
	register_error(elgg_echo('fundcampaign:notdeleted'));
	forward(REFERER);
}

if (($entity) && ($entity instanceof ElggObject)) {
	// delete fundcampaign icons
	$owner_guid = $entity->owner_guid;
	$prefix = "fundcampaigns/" . $entity->guid;
	$imagenames = array('.jpg', 'tiny.jpg', 'small.jpg', 'medium.jpg', 'large.jpg');
	$img = new ElggFile();
	$img->owner_guid = $owner_guid;
	foreach ($imagenames as $name) {
		$img->setFilename($prefix . $name);
		$img->delete();
	}

	// delete fundcampaign
	if ($entity->delete()) {
		system_message(elgg_echo('fundcampaign:deleted'));
	} else {
		register_error(elgg_echo('fundcampaign:notdeleted'));
	}
} else {
	register_error(elgg_echo('fundcampaign:notdeleted'));
}

forward(elgg_get_site_url() . "project/{$project_guid}");
