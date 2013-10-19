<?php
/**
 * Fundraising plugin
 *
 * @package Coopfunding
 * @subpackage Fundraising
 *
 */

$guid = get_input('guid');

$entity = get_entity($guid);

if (elgg_instanceof($entity, 'group', 'project')) {
    $entity_text = 'project';
    $entities_text = 'projects';
}else{
    $entity_text = 'fundcampaigns';
    $entities_text = 'fundcampaigns';
}

if (!$entity) {
	forward('', 404);
}

elgg_set_page_owner_guid($guid);

elgg_push_breadcrumb(elgg_echo("{$entities_text}"), "{$entities_text}/all");
elgg_push_breadcrumb(elgg_echo($entity->name), "{$entity_text}/{$entity->alias}");
elgg_push_breadcrumb(elgg_echo('fundraising:contributors'));

$content =  elgg_list_entities(array(
        'type' => 'object',
        'subtype' => 'contributions_set',
        'container_guid' => $guid
));


$title = elgg_echo("fundraising:contributors:$entity_text", array($entity->name));
$body = elgg_view_layout('content', array(
	'title' => $title,
	'content' => $content,
	'filter' => '',
));

echo elgg_view_page($title, $body);
