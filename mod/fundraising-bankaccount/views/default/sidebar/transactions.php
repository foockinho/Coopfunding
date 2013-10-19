<?php
/**
 * Manage fundraising-bankaccount transactions sidebar
 *
 * @package Coopfunding
 * @subpackage Fundraising
 *
 * @uses $vars['entity'] Campaign entity
 */

elgg_load_library('coopfunding:fundraising');

$guid = $vars['entity']->guid;

$body = elgg_view('output/url', array(
	'text' => elgg_echo('fundraising:bankaccount:manage'),
	'href' => "fundraising/bankaccount/managedeposits/{$guid}",
	'class' => "elgg-button elgg-button-action" 
));  

echo elgg_view_module('aside', elgg_echo('fundraising:bankaccount'), $body);
