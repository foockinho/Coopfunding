<?php

$project = $vars['entity'];

if (!$project) {
	return true;
}

elgg_load_library('elgg:fundcampaigns');

$fundcampaign = fundcampaigns_get_active_campaign ($project->guid);

if (!$fundcampaign) {
	$content = '<p>' . elgg_echo('contribution:none') . '</p>';
} else {	

	$contributors_count = elgg_get_entities(array(
		'type' => 'object',
		'subtype' => 'contributions_set',
		'container_guid' => $fundcampaign->guid,
		'count' => true,
	));

	$all_link = elgg_view('output/url', array(
		'href' => "fundraising/contributors/{$fundcampaign->guid}",
		'text' =>  elgg_echo('fundraising:contributors:count', array($contributors_count)),
		'is_trusted' => true,
	));

	elgg_load_library('coopfunding:fundraising');

	$contributions_amount = fundraising_sum_amount($fundcampaign);

	if (!$contributors_count) {
		$contributors_count = "0";
	}

	$body = elgg_view('output/url', array(
		'text' => elgg_echo('fundraising:contribute:button'),
		'href' => "fundraising/contribute/{$fundcampaign->guid}",
		'class' => 'elgg-button elgg-button-action',
	));

	$amount = elgg_echo('fundraising:contributions:amount', array($contributions_amount));
	$amount .= elgg_echo('fundraising:contributions:of');
	$amount .= elgg_echo('fundraising:contributions:eur', array($fundcampaign->total_amount));

	if ($vars['entity']->total_amount) {
		$amount .= "\n" . elgg_echo($contributions_amount / $fundcampaign->total_amount * 100) . '%';
	}

	$body .= "<br>" . elgg_view('output/text', array(
		'value' => $amount,
	));

}

echo elgg_view('projects/profile/module', array(
	'title' => elgg_echo('fundraising:contributions', array($fundcampaign->name)),
	'content' => $body,
	'all_link' => $all_link,
));


