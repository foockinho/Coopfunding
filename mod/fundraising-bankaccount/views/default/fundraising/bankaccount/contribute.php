<?php


elgg_load_library('coopfunding:fundraising:bankaccount');

$entity = $vars['entity'];
$user = elgg_get_logged_in_user_entity();
$code = $entity->guid . '0' . $user->guid;
$ban = elgg_get_config('ban');
$amount = $vars['amount'];

if (!$ban) 
{
   system_message(elgg_echo('fundraising:bankaccount:contributeNoBAN', array($vars['entity']->name)));
   forward(REFERER);
}

//show BAN number
echo '<div>';
echo elgg_echo("fundraising:bankaccount:contributeToBAN", array($amount, $ban, $code));
echo '</div>';

echo '<div>';
echo elgg_echo("fundraising:bankaccount:message");
echo '</div>';

echo elgg_view('output/url', array(
	'href' => "fundraising/contribute/{$vars['entity']->guid}?amount={$vars['amount']}",
	'text' => "&laquo; " . elgg_echo('back'),
	'class' => 'elgg-button elgg-button-action',
));

if (get_input('reward_guid')) {
    $bookreward = new ElggObject ();
    $bookreward->owner_guid = $user->guid;
    $bookreward->container_guid = $entity->guid;
    $bookreward->reward = get_input('reward');
    $bookreward->save();
}



