<?php

$title = elgg_echo('moderation:toverifylist');

elgg_push_breadcrumb($title);

$list = elgg_list_entities_from_metadata(array(
    'type' => 'group',
    'subtype' => 'project',
    'metadata_name' => 'verify',
    'metadata_value' => 1	
));	

if (!$list) {
	$list = elgg_echo('projects:none');
}

$sidebar = elgg_view('moderation/sidebar/tagcloud');
$params = array(
	'content' => $list,
	'title' => $title,
	'filter' => '',
	'sidebar' => $sidebar,
);
$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);




























$body = elgg_view_layout(array('content' => $list));
 
echo elgg_view_page("All Site Blogs", $body);





