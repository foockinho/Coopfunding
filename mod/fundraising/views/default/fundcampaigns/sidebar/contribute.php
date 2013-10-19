<?php
/**
 * Contribute sidebar
 *
 * @package Coopfunding
 * @subpackage Fundraising
 *
 * @uses $vars['entity'] Campaign entity
 */

elgg_load_library('coopfunding:fundraising');

$guid = $vars['entity']->guid;

$contributors_count = elgg_get_entities(array(
	'type' => 'object',
	'subtype' => 'contributions_set',
	'container_guid' => $guid,
	'count' => true,
));

$contributions_amount = fundraising_sum_amount($vars['entity']);

if (!$contributors_count) {
	$contributors_count = "0";
}

$body = elgg_view('output/url', array(
	'text' => elgg_echo('fundraising:contribute:button'),
	'href' => "fundraising/contribute/{$vars['entity']->guid}",
	'class' => 'elgg-button elgg-button-action',
));


$body .= "<br>" . elgg_view('output/url', array(
	'text' => elgg_echo('fundraising:contributors:count', array($contributors_count)),
	'href' => "fundraising/contributors/{$guid}",
));

$amount = elgg_echo('fundraising:contributions:amount', array($contributions_amount));
$amount .= elgg_echo('fundraising:contributions:of');
$amount .= elgg_echo('fundraising:contributions:eur', array($vars['entity']->total_amount));
$amount .= "\n" . elgg_echo($contributions_amount / $vars['entity']->total_amount * 100) . '%';

$body .= "<br>" . elgg_view('output/text', array(
	'value' => $amount,
));

echo elgg_view_module('aside', elgg_echo('fundraising:contribute'), $body);
