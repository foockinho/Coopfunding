<?php
/**
 * Individual returns helper functions
 *
 * @package campaign_reward
 */

/**
 * Get page components to list all indvidual rewards
 *
 * @param int $container_guid The GUID of the fundcampaign
 * @return array
 */
function campaign_reward_get_page_content_list($guid = NULL) {

	$return = array();

	$return['filter_context'] = 'mine';

	$options = array(
		'type' => 'object',
		'subtype' => 'campaign_reward',
		'full_view' => false,
		'no_results' => elgg_echo('campaign_reward:none'),
		'order_by_metadata' => array('name' => 'amount', 'direction' => 'ASC', 'as' => 'integer')
	);

	if ($guid) {
		$options['container_guid'] = $guid;
		$container = get_entity($guid);
		elgg_load_library('coopfunding:fundcampaigns');
		$options['donatebutton'] = fundcampaigns_is_active_campaign($container);

		$return['title'] = elgg_echo('campaign_reward:title:campaigns_individual_rewards', array($container->name));

		$crumbs_title = $container->alias;
		elgg_push_breadcrumb(elgg_echo("fundcampaigns"), "fundcampaigns/all");
		elgg_push_breadcrumb($container->alias, "fundcampaigns/{$container->alias}");

        	$return['filter'] = false;

	} else {
		return false;
	}

 	$content = elgg_list_entities_from_metadata($options);

	$return['title'] = $title;
	$return['content'] = $content;

	return $return;

}



/**
 * Get page components to edit/create a campaign_reward post.
 *
 * @param string  $page     'edit' or 'new'
 * @param int     $guid     GUID of campaing_reward
 * @return array
 */
function campaign_reward_get_page_content_edit($page, $guid = NULL) {

	$return = array(
		'filter' => '',
	);

	$vars = array();
	$vars['id'] = 'campaign_reward-post-edit';
	$vars['class'] = 'elgg-form-alt';

	$sidebar = '';
	if ($page == 'edit') {
		$campaign_reward = get_entity((int)$guid);

		$title = elgg_echo('campaign_reward:edit');

		if (elgg_instanceof($campaign_reward, 'object', 'campaign_reward') && $campaign_reward->canEdit()) {
			$vars['entity'] = $campaign_reward;

			$title .= ": " . $campaign_reward->title;

			$body_vars = campaign_reward_prepare_form_vars($campaign_reward, $guid);

			elgg_push_breadcrumb($campaign_reward->title, $campaign_reward->getURL());
			elgg_push_breadcrumb(elgg_echo('edit'));

			$content = elgg_view_form('campaign_reward/save', $vars, $body_vars);
		} else {
			$content = elgg_echo('campaign_reward:error:cannot_edit_item');
		}
	} else {
		elgg_push_breadcrumb(elgg_echo('campaign_reward:add'));

		$body_vars = campaign_reward_prepare_form_vars(null, $guid);

		$title = elgg_echo('campaign_reward:add');
		$content = elgg_view_form('campaign_reward/save', $vars, $body_vars);
	}

	$return['title'] = $title;
	$return['content'] = $content;
	$return['sidebar'] = $sidebar;
	return $return;
}

/**
 * Pull together campaign_reward variables for the save form
 *
 * @param ElggObject       $campaign_reward
 * @return array
 */
function campaign_reward_prepare_form_vars($campaign_reward = NULL, $container_guid = NULL) {

	// input names => defaults
	$values = array(
		'title' => NULL,
		'description' => NULL,
		'stock' => '0',
		'access_id' => ACCESS_DEFAULT,
		'amount' => '0',
		'container_guid' => $container_guid,
		'guid' => NULL,
	);

	if ($campaign_reward) {
		foreach (array_keys($values) as $field) {
			if (isset($campaign_reward->$field)) {
				$values[$field] = $campaign_reward->$field;
			}
		}
	}

	if (elgg_is_sticky_form('campaign_reward')) {
		$sticky_values = elgg_get_sticky_values('campaign_reward');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}

	elgg_clear_sticky_form('campaign_reward');

	if (!$campaign_reward) {
		return $values;
	}
	return $values;
}

