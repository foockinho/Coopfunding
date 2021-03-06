<?php 
/**
 * Project entity view
 * 
 * @package Coopfunding
 * @subpackage Projects
 */

$project = $vars['entity'];

$icon = elgg_view_entity_icon($project, 'tiny');

$metadata = elgg_view_menu('entity', array(
	'entity' => $project,
	'handler' => 'projects',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

if (elgg_in_context('owner_block') || elgg_in_context('widgets')) {
	$metadata = '';
}


if ($vars['full_view'] && !elgg_in_context('gallery')) {
	echo elgg_view('projects/profile/summary', $vars);
} elseif (elgg_in_context('gallery')) {
	echo elgg_view('projects/profile/gallery', $vars);
} elseif (elgg_in_context('owner_block')) {
	echo elgg_view('projects/owner_block', $vars);
} else {
	// brief view
	$params = array(
		'entity' => $project,
		'metadata' => $metadata,
		'subtitle' => $project->briefdescription,
	);
	$params = $params + $vars;
	$list_body = elgg_view('group/elements/summary', $params);

	echo elgg_view_image_block($icon, $list_body, $vars);
}
