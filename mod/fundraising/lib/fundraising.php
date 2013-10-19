<?php

function fundraising_get_methods() {
	return elgg_get_config('fundraising_methods');
}

function fundraising_get_contribute_button($fundcampaign_guid, $amount = 0, $reward_guid) {
    
    $output = "<span><ul>";
    $link = "<li><a class='elgg-button elgg-button-action' href='" . elgg_get_site_url() ."fundraising/contribute/{$fundcampaign_guid}?amount={$amount}&reward={$reward_guid}'>" . elgg_echo('fundraising:contribute:with', array($amount)) . "</a>";
    $output .= $link;
    
	$output .= "</ul><span>";

    return $output;
}

function fundraising_get_contributions_set($project_guid, $user_guid = 0) {

	if ($user_guid == 0) {
		$user_guid = elgg_get_logged_in_user_guid();
	}

	$contributor = current(elgg_get_entities(array(
		'type' => 'object',
		'subtype' => 'contributions_set',
		'owner_guid' => $user_guid,
		'container_guid' => $project_guid,
		'limit' => 1,
	)));
	return $contributor;
}

function fundraising_sum_amount(ElggEntity $entity) {
	return elgg_trigger_plugin_hook('fundraising', 'sum_amount', array('entity' => $entity), 0);
}

