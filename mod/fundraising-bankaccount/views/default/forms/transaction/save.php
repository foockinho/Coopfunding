<?php
/**
 * Edit transaction form
 *
 * @package transaction
 */

 
$transaction = get_entity($vars['guid']);
$vars['entity'] = $transaction;

$amount = $vars['eur_amount'];

$action_buttons = '';
$delete_link = '';

if ($vars['guid']) {
	// add a delete button if editing
	$delete_url = "action/fundraising/bankaccount/delete?guid={$vars['guid']}";
	$delete_link = elgg_view('output/confirmlink', array(
		'href' => $delete_url,
		'text' => elgg_echo('delete'),
		'class' => 'elgg-button elgg-button-delete float-alt'
	));
}
$save_url = "action/transaction/delete?guid={$vars['guid']}";
$save_button = elgg_view('input/submit', array(
    'href' => $save_url,
	'value' => elgg_echo('save'),
	'name' => 'save',
));

$action_buttons = $save_button . $delete_link;

$contributor_label = elgg_echo('fundraising:contributor');
$user_guid = $transaction->contributor;
if (!$user_guid) {
    $user_guid = elgg_get_logged_in_user_entity()->guid;
}
$defaultvalues['value'] = array($user_guid);
$defaultvalues['name'] = 'contributor';
$contributor_text = elgg_view('input/userpicker', $defaultvalues);


if (!$vars['commit_date']) { 
    $vars['commit_date'] = new DateTime();
    
    $vars['commit_date'] = $vars['commit_date']->format('Y-m-d H:i:s');
} else {
 
}
$date_label = elgg_echo('fundraising:date');
$date_input = elgg_view('input/date', array(
	'name' => 'commit_date',
	'id' => 'commit_date',
	'value' => $vars['commit_date']
));
 
$amount_label = elgg_echo('fundraising:amount');
$amount_input = elgg_view('input/text', array(
	'name' => 'eur_amount',
	'id' => 'transaction_amount',
	'value' => _elgg_html_decode($vars['eur_amount'])
));

if (elgg_is_active_plugin("campaign_reward")) {		
	
		$params = array('fundcampaign_guid' => $vars['container_guid'], 'transaction_guid' => $vars['guid']);		
		$campaign_reward_text = elgg_trigger_plugin_hook('fundraising:rewards:selector', 'campaign_reward', $params);	
}


if ($vars['guid']) {
	$entity = get_entity($vars['guid']);
	$saved = date('F j, Y @ H:i', $entity->time_created);
} else {
	$saved = elgg_echo('never');
}

// hidden inputs
$container_guid_input = elgg_view('input/hidden', array('name' => 'container_guid', 'value' => $vars['container_guid']));
$guid_input = elgg_view('input/hidden', array('name' => 'guid', 'value' => $vars['guid']));
//$contributor_text
 
echo <<<___HTML

<div>
	<label for="transaction_contributor">$contributor_label</label>
	$contributor_text
</div>

<div>
	<label for="transaction_date">$date_label</label>
	$date_input
</div>

<div>
	<label for="transaction_amount">$amount_label</label>
	$amount_input
</div>

	$campaign_reward_text

<div class="elgg-foot">
	$guid_input
	$container_guid_input

	$action_buttons
</div>

___HTML;


