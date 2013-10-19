<?php
/**
 * Campaign_reward widget display view
 */

$num = $vars['entity']->num_display;

$options = array(
	'type' => 'object',
	'subtype' => 'campaign_reward',
	'container_guid' => $vars['entity']->container_guid,
	'limit' => $num,
	'full_view' => false,
	'pagination' => false,
);
$content = elgg_list_entities($options);

echo $content;

if ($content) {
	$campaign_reward_url = "campaign_reward/owner/" . $vars['entity']->container_guid;
	$more_link = elgg_view('output/url', array(
		'href' => $campaign_reward_url,
		'text' => elgg_echo('campaign_reward:morecampaign_reward'),
		'is_trusted' => true,
	));
	echo "<span class=\"elgg-widget-more\">$more_link</span>";
} else {
	echo elgg_echo('campaign_reward:nocampaign_reward');
}
