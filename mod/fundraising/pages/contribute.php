<?php
/**
 * Fundraising plugin
 *
 * @package Coopfunding
 * @subpackage Fundraising
 *
 */
$guid = get_input('guid');
$reward_guid = get_input('reward');

$entity = get_entity($guid);
$amount = get_input('amount');

if (elgg_instanceof($entity, 'group', 'project')) {
    $entity_text = 'project';
    $entities_text = 'projects';
}else{
    $entity_text = 'fundcampaigns';
    $entities_text = 'fundcampaigns';
}

elgg_load_library("elgg:{$entities_text}");

elgg_set_page_owner_guid($guid);

elgg_push_breadcrumb(elgg_echo("{$entities_text}"), "{$entities_text}/all");
elgg_push_breadcrumb(elgg_echo($entity->name), "{$entity_text}/{$alias}");
elgg_push_breadcrumb(elgg_echo('fundraising:contribute'));

$content = elgg_view_form('fundraising/contribute', array(), array('entity' => $entity, 'amount' => $amount, 'reward_guid' => $reward_guid));

$title = elgg_echo("fundraising:contribute:{$entity_text}", array($entity->name));
$body = elgg_view_layout('content', array(
	'title' => $title,
	'content' => $content,
	'filter' => '',
));

echo elgg_view_page($title, $body);
