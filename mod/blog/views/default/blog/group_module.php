<?php
/**
 * Group blog module
 */

$project = $vars['entity'];

$all_link = elgg_view('output/url', array(
	'href' => "blog/group/$project->guid/all",
	'text' => elgg_echo('blog:title:projects_blogs', array($project->name)),
	'is_trusted' => true,
));

elgg_push_context('widgets');
$options = array(
	'type' => 'object',
	'subtype' => 'blog',
	'container_guid' => $project->guid,
	
	'limit' => 6,
	'full_view' => false,
	'pagination' => false,
	'no_results' => elgg_echo('blog:none'),
);
$content = elgg_list_entities_from_metadata($options);
elgg_pop_context();

$new_link = elgg_view('output/url', array(
	'href' => "blog/add/$project->guid",
	'text' => elgg_echo('blog:write'),
	'is_trusted' => true,
));

echo elgg_view('projects/profile/module', array(
	'title' => elgg_echo('blog'),
	'content' => $content,
	'all_link' => $all_link,
	'add_link' => $new_link,
));

