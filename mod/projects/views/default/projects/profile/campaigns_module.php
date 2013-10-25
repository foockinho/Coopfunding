<?php

$project = $vars['entity'];
if (!$project) {
	return true;
}

$all_link = elgg_view('output/url', array(
	'href' => "fundcampaigns/owner/$project->guid",
	'text' => elgg_echo('link:view:all'),
	'is_trusted' => true,
));

elgg_load_library('elgg:fundcampaigns');

$fundcampaign = fundcampaigns_get_active_campaign ($project->guid);

if (!$fundcampaign) {
	$content = '<p>' . elgg_echo('projects:campaigns:none') . '</p>';
} else {

	$content = elgg_view('fundcampaigns/profile/gallery', array (
				'entity' => $fundcampaign
			));
	$content .=  elgg_trigger_plugin_hook('fundcampaigns:sidebarmenus', 'fundcampaign', $fundcampaign);
	
}



echo elgg_view('projects/profile/module', array(
	'title' => elgg_echo('fundcampaigns:campaigns'),
	'content' => $content,
	'all_link' => $all_link,
));
