<?php

$reward_book = elgg_extract('entity', $vars, FALSE);

if (!$reward_book) {
	return TRUE;
}

$owner = get_entity($reward_book->contributor);   
$container = $reward_book->container_guid;
    
$owner_icon = elgg_view_entity_icon($owner, 'tiny');
$owner_link = elgg_view('output/url', array(
	'href' => "profile/$owner->username",
	'text' => $owner->name,
	'is_trusted' => true,
));
$author_text = elgg_echo('byline', array($owner_link));
$date = elgg_view_friendly_time($reward_book->time_created);

elgg_load_library('elgg:campaign_reward');
	
$reward_guid = campaign_reward_get_reward_or_transaction ($reward_book->guid);	
$reward = get_entity ($reward_guid);
if ($reward) {
	$reward_title = $reward->title;	
} else {
	$reward_title = "campaign_reward:noreward";
}

$metadata .= elgg_view('output/confirmlink', array(
	'href' => "action/reward_book/delete?guid=" . $reward_book->guid,	
	'confirm' => elgg_echo("book_reward:deletewarning"),	
	'class' => 'elgg-icon elgg-icon-delete float-alt',
));

if ($reward_book->method == 'bankaccount') {
	$metadata .= elgg_view('output/confirmlink', array(
		'href' => "action/reward_book/commit?guid=" . $reward_book->guid,
		'text' => "commit",
		'confirm' => "make transaction from this book.",
		'is_trusted' => true,
		'class' => 'float-alt',
	));
}



$subtitle = "$author_text $date / $reward_book->method / $reward_title";
$content = "$reward_book->amount" . "€";

$params = array(
		'entity' => $reward_book,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'content' => $content,
);

$params = $params + $vars;

$list_body = elgg_view('object/summary', $params);
echo elgg_view_image_block($owner_icon, $list_body);

