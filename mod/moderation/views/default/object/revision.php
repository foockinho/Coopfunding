<?php 
/**
 * revision entity view
 * 
 * @package Coopfunding
 * @subpackage Projects
 */

$revision = $vars['entity']; 
$entity = get_entity($revision->container_guid);
$vars['entity'] = $entity;
$icon = elgg_view_entity_icon($entity, 'tiny');

$delete_url = "action/{$entity->getSubtype()}s/delete?guid={$entity->getGUID()}";
$metadata =  elgg_view('output/confirmlink', array(
		'href' => $delete_url,
		'confirm' => elgg_echo("moderation:deletewarning"),
		'class' => 'elgg-icon elgg-icon-delete float-alt',
	));
if (elgg_in_context('owner_block') || elgg_in_context('widgets')) {
	$metadata = '';
}

if ($vars['full_view'] && !elgg_in_context('gallery')) {
	echo elgg_view('moderation/summary', $vars);
} elseif (elgg_in_context('gallery')) {
	echo elgg_view('moderation/gallery', $vars);
} elseif (elgg_in_context('owner_block')) {
	echo elgg_view('moderation/owner_block', $vars);
} else {
	// brief view
	$params = array(
		'entity' => $entity,
		'metadata' => $metadata,
		'subtitle' => $entity->briefdescription,
	);
	
	$vars['title'] = elgg_view('output/url', array('text' => $entity->name, 'href' => "{$entity->getSubtype()}s/edit/{$entity->alias}"));
	$params = $params + $vars;
	$list_body = elgg_view('group/elements/summary', $params);

	echo elgg_view_image_block($icon, $list_body, $vars);
}
