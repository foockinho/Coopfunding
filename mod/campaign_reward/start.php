<?php
/**
 * Individual Returns
 *
 * @package campaign_reward
 *
 */

elgg_register_event_handler('init', 'system', 'campaign_reward_init');

/**
 * Init campaign_reward plugin.
 */
function campaign_reward_init() {

	elgg_register_library('elgg:campaign_reward', elgg_get_plugins_path() . 'campaign_reward/lib/campaign_reward.php');

	// routing of urls
	elgg_register_page_handler('campaign_reward', 'campaign_reward_page_handler');

    	// Register for search.
	elgg_register_entity_type('object', 'campaign_reward');

	elgg_register_plugin_hook_handler('fundcampaigns:profilebuttons', 'fundcampaign', 'campaign_reward_set_add_button');
	elgg_register_plugin_hook_handler('fundcampaigns:sidebarmenus', 'fundcampaign', 'campaign_reward_set_side_bar_menu');

	// add a campaign_reward widget
	elgg_register_widget_type('campaign_reward', elgg_echo('campaign_reward'), elgg_echo('campaign_reward:widget:description'));

	// register actions
	$action_path = elgg_get_plugins_path() . 'campaign_reward/actions/campaign_reward';
	elgg_register_action('campaign_reward/save', "$action_path/save.php");
	elgg_register_action('campaign_reward/delete', "$action_path/delete.php");

	// override the default url to view a campaign object
	elgg_register_plugin_hook_handler('entity:url', 'campaign_reward', 'campaign_reward_set_url');
	
	//Register for donate buttons
	if (elgg_is_active_plugin('fundraising')) {
	    elgg_register_library('coopfunding:fundraising', elgg_get_plugins_path() . 'fundraising/lib/fundraising.php');
	}
	if (elgg_is_active_plugin('fundcampaigns')) {
	    elgg_register_library('coopfunding:fundcampaigns', elgg_get_plugins_path() . 'fundcampaigns/lib/fundcampaigns.php');
	}
}

/**
 * Dispatches campaign_reward pages.
 * URLs take the form of
 *  Campaigns's reward:    campaign_reward/owner/<fundcampaing>
 *
 * @param array $page
 * @return bool
 */
function campaign_reward_page_handler($page) {

	elgg_load_library('elgg:campaign_reward');

	if (!isset($page[0])) {
		forward('', '404');
	}

    elgg_set_page_owner_guid($page[1]);
    
	$page_type = $page[0];
	switch ($page_type) {
		case 'owner':
			$params = campaign_reward_get_page_content_list($page[1]);
			campaign_reward_set_add_button_func($page[1]);
			break;
		case 'add':
			gatekeeper();
		
			$params = campaign_reward_get_page_content_edit($page_type, $page[1]);
			break;
		case 'edit':
			gatekeeper();
			$params = campaign_reward_get_page_content_edit($page_type, $page[1]);
			break;
		default:
			return false;
	}

	if (isset($params['sidebar'])) {
		$params['sidebar'] .= elgg_view('campaign_reward/sidebar', array('page' => $page_type));
	}

	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($params['title'], $body);
	return true;
}




/**
 * Register campaign_reward with ECML.
 */
function campaign_reward_ecml_views_hook($hook, $entity_type, $return_value, $params) {
	$return_value['object/campaign_reward'] = elgg_echo('campaign_reward:campaign_rewards');

	return $return_value;
}

function campaign_reward_set_side_bar_menu ($hook, $entity_type, $return_value, $params) {

    $donatebutton = false;
    if (elgg_is_active_plugin('fundraising')) {
        elgg_load_library('coopfunding:fundraising');
        
        if (elgg_is_active_plugin('fundcampaigns')) {
            $donatebutton = fundcampaigns_is_active_campaign($params);
        }
    }

    return $return_value . elgg_view('campaign_reward/sidebar/rewards', array('entity' => $params, 'donatebutton' => $donatebutton));
    
}

function campaign_reward_set_add_button ($hook, $entity_type, $return_value, $params) {

	campaign_reward_set_add_button_func($params->guid);
}

function campaign_reward_set_add_button_func ($guid) {

        $text = elgg_echo("campaign_reward:addreward");
        $url = elgg_get_site_url() . "campaign_reward/add/{$guid}";

    	elgg_register_menu_item('title', array(
				'name' => $text,
				'href' => $url,
				'text' => elgg_echo($text),
				'link_class' => 'elgg-button elgg-button-action',
			));
        return false;
}

/**
 * Format and return the URL for campaign_reward.
 *
 * @param string $hook
 * @param string $type
 * @param string $url
 * @param array  $params
 * @return string URL of campaign_reward.
 */
function campaign_reward_set_url($hook, $type, $url, $params) {
    
	$entity = $params['entity'];
	if (elgg_instanceof($entity, 'object', 'campaign_reward')) {
		return "campaign_reward/owner/{$entity->container_guid}";
	}
}
