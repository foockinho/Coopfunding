<?php
/**
 * View for campaign_reward objects
 *
 * @package campaign_reward
 */

$full = elgg_extract('full_view', $vars, FALSE);
$campaign_reward = elgg_extract('entity', $vars, FALSE);

if (!$campaign_reward) {
	return TRUE;
}

$owner = $campaign_reward->getOwnerEntity();
$container = $campaign_reward->getContainerEntity();

$owner_icon = elgg_view_entity_icon($owner, 'tiny');
$owner_link = elgg_view('output/url', array(
	'href' => "blog/owner/$owner->username",
	'text' => $owner->name,
	'is_trusted' => true,
));
$author_text = elgg_echo('byline', array($owner_link));
$date = elgg_view_friendly_time($blog->time_created);


$metadata = elgg_view_menu('entity', array(
	'entity' => $vars['entity'],
	'handler' => 'campaign_reward,
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

$subtitle = "campaign_reward->title campaign_reward->amount campaign_reward->description";

// do not show the metadata and controls in widget view
if (elgg_in_context('widgets')) {
	$metadata = '';
}

if ($full) {

	$body = elgg_view('output/longtext', array(
		'value' => $campaign_reward->description,
		'class' => 'campaign_reward-post',
	));

	$params = array(
		'entity' => $campaign_reward,
		'title' => false,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
	);
	$params = $params + $vars;
	$summary = elgg_view('object/elements/summary', $params);

	echo elgg_view('object/elements/full', array(
		'summary' => $summary,
		'icon' => $owner_icon,
		'body' => $body,
	));

} else {
	// brief view

	$params = array(
		'entity' => $campaign_reward,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'content' => $excerpt,
	);
	$params = $params + $vars;
	$list_body = elgg_view('object/elements/summary', $params);

	echo elgg_view_image_block($owner_icon, $list_body);
}
