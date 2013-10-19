<?php
/**
 * Search for content in this campaign
 *
 * @uses vars['entity'] ElggObject
 */

$url = elgg_get_site_url() . 'search';
$body = elgg_view_form('fundcampaigns/search', array(
	'action' => $url,
	'method' => 'get',
	'disable_security' => true,
), $vars);

echo elgg_view_module('aside', elgg_echo('fundcampaigns:search_in_fundcampaign'), $body);
