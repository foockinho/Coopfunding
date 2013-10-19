<?php
/**
 * Campaigns search
 *
 * @package Coopfunding
 * @subpackage fundcampaigns
 */
$url = elgg_get_site_url() . 'fundcampaigns/search';
$body = elgg_view_form('fundcampaigns/find', array(
	'action' => $url,
	'method' => 'get',
	'disable_security' => true,
));

echo elgg_view_module('aside', elgg_echo('fundcampaigns:searchtag'), $body);
