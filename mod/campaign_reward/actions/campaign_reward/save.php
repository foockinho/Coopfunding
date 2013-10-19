<?php
/**
 * Save campaign_reward entity
 *
 *
 * @package campaign_reward
 */

// start a new sticky form session in case of failure
elgg_make_sticky_form('campaign_reward');

// store errors to pass along
$error = FALSE;
$error_forward_url = REFERER;
$user = elgg_get_logged_in_user_entity();

// edit or create a new entity
$guid = get_input('guid');

if ($guid) {
	$entity = get_entity($guid);
	if (elgg_instanceof($entity, 'object', 'campaign_reward') && $entity->canEdit()) {
		$campaign_reward = $entity;
	} else {
		register_error(elgg_echo('campaign_reward:error:item_not_found'));
		forward(get_input('forward', REFERER));
	}
} else {
	$campaign_reward = new ElggObject();
	$campaign_reward->subtype = 'campaign_reward';
	$campaign_reward->owner_guid = elgg_get_logged_in_user_entity()->guid;

	$campaign_reward->container_guid = get_input('container_guid');
	$new_campaign_reward = TRUE;
}

// set defaults and required values.
$values = array(
	'title' => '',
	'description' => '',
	'access_id' => ACCESS_DEFAULT,
	'stock' => '0',
	'amount'=> '0'
);

// fail if a required entity isn't set
$required = array('title', 'description', 'amount');

// load from POST and do sanity and access checking
foreach ($values as $name => $default) {
	if ($name === 'title') {
		$value = htmlspecialchars(get_input('title', $default, false), ENT_QUOTES, 'UTF-8');
	} else {
		$value = get_input($name, $default);
	}

	if (in_array($name, $required) && empty($value)) {
		$error = elgg_echo("campaign_reward:error:missing:$name");
	}

	if ($error) {
		break;
	}

	switch ($name) {
		default:
			$values[$name] = $value;
			break;
	}
}

// assign values to the entity, stopping on error.
if (!$error) {
	foreach ($values as $name => $value) {
	  $campaign_reward->$name = $value;
	}
}


// only try to save base entity if no errors
if (!$error) {
	if ($campaign_reward->save()) {
		// remove sticky form entries
		elgg_clear_sticky_form('campaign_reward');

		system_message(elgg_echo('campaign_reward:message:saved'));
        $url =  elgg_get_site_url() . "campaign_reward/owner/{$campaign_reward->container_guid}";     
		forward($url);
		
	} else {
		register_error(elgg_echo('campaign_reward:error:cannot_save'));
		forward($error_forward_url);
	}
} else {
	register_error($error);
	forward($error_forward_url);
}
