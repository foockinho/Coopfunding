<?php
/**
 * Contributors render
 *
 * @package Coopfunding
 * @subpackage Fundraising
 * @uses $vars['entity'] Project entity
 */

//picture + name + amount + date

$contributions_set = $vars['entity'];

$user = get_entity($contributions_set->owner_guid);

if (!$user) {
	return;
}

$icon = elgg_view_entity_icon($user, 'tiny');
$user_name = elgg_view('output/url', array(
	'text' => $user->name,
	'href' => $user->getURL(),
));
$user_amount = "<ul class=\"fundraising-contributions\">";
foreach (elgg_get_config('fundraising_currencies') as $currency) {
	$currency_attr = "${currency}_amount";
	if ($currency_amount = $contributions_set->$currency_attr) {
		if ($currency == "btc") {
			$currency_amount /= pow(10, 8);
		}
		$user_amount .= "<li class=\"fundraising-contribution fundraising-contribution-$currency\">";
		$user_amount .= elgg_echo("fundraising:contributions:$currency", array($currency_amount)) . "</li>";
	}
}
$user_amount .= "</ul>";

$timestamp = elgg_view_friendly_time($contributions_set->time_updated);

$body = <<<HTML
<div class="contributors-name">$user_name</div>
<div class="contributors-amount">$user_amount</div>
<div class="contributors-date">$timestamp</div>
HTML;

echo elgg_view_image_block($icon, $body, array('class' => 'contributor'));
