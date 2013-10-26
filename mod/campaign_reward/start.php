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
	elgg_register_entity_type('object', 'reward_book');

	elgg_register_plugin_hook_handler('fundcampaigns:profilebuttons', 'fundcampaign', 'campaign_reward_set_add_button');
	elgg_register_plugin_hook_handler('fundcampaigns:sidebarmenus', 'fundcampaign', 'campaign_reward_set_side_bar_menu');

	elgg_register_plugin_hook_handler('fundraising:rewards:selector', 'campaign_reward', 'campaign_reward_selector');
	
	elgg_register_plugin_hook_handler('fundraising:rewards:save', 'campaign_reward', 'campaign_reward_save');

	elgg_register_plugin_hook_handler('fundraising:transaction:do_books', 'do_books', 'campaign_reward_do_books');		

	// add a campaign_reward widget
	elgg_register_widget_type('campaign_reward', elgg_echo('campaign_reward'), elgg_echo('campaign_reward:widget:description'));

	// register actions
	$action_path = elgg_get_plugins_path() . 'campaign_reward/actions';
	elgg_register_action('campaign_reward/save', "$action_path/campaign_reward/save.php");
	elgg_register_action('campaign_reward/delete', "$action_path/campaign_reward/delete.php");
	elgg_register_action('reward_book/commit', "$action_path/reward_book/commit.php");
	elgg_register_action('reward_book/delete', "$action_path/reward_book/delete.php");

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
		case 'books':
			gatekeeper();
			$params = campaign_reward_get_page_content_books($page[1]);
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
			$return_value .= elgg_view('campaign_reward/sidebar/rewards', array('entity' => $params, 'donatebutton' => $donatebutton));
		}		
	}

	return $return_value;
    
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


function campaign_reward_set_url($hook, $type, $url, $params) {
    
	$entity = $params['entity'];
	if (elgg_instanceof($entity, 'object', 'campaign_reward')) {
		return "campaign_reward/owner/{$entity->container_guid}";
	}
}

/**
gets a output block {label:select} with all campaign_reward of $params['fundcampaign_guid'], and selected $params['default_value']

$params['fundcampaign_guid']
$params['default_value']
**/
function campaign_reward_selector ($hook, $type, $returnvalue, $params){

	elgg_load_library('elgg:campaign_reward');	

	$select = campaign_reward_get_selector ($params); 
	return $select;

}

/*
creates a relationship between $params['reward_guid'] and $params['transaction_guid'] type = "reward" if reward is stocked. All previous transaction's relations removed. If there were any book before, removes it.

$params['transaction_guid']
$params['book_search_code'] some code stored by pay method in booking to find book entity.
$params['referer_if out stock']    if yes, goes back to referer if stocked. else, inform but pass.

*/
function campaign_reward_save ($hook, $type, $returnvalue, $params){
	
	elgg_load_library('elgg:campaign_reward');	
	
	$reward_guid = $params['reward_guid'];

	if ($params['book_search_code']) {
		$reward_book = campaign_reward_get_reward_book ($params['book_search_code']);
		if (!$reward_guid) {
			$reward_guid = campaign_reward_get_reward_or_transaction ($reward_book_guid);
		}
	}

	$related_guid = campaign_reward_get_reward_or_transaction ($params['transaction_guid']);

	$is_edited = $related_guid != $reward_guid;	
	if ($is_edited) {	

		remove_entity_relationships($params['transaction_guid'], "reward");	
		if ($reward_book) {
			$reward_book->delete();
		}

		$non_default_reward = $reward_guid > 0;
		if ($non_default_reward){
			
			list($is_stocked) = campaign_reward_is_stocked ($reward_guid);
			
			if ($is_stocked) {		
				$relation = add_entity_relationship ($params['transaction_guid'], 'reward', $reward_guid);			
			} else {
				if ($params['referer_if out stock']) {
					register_error(elgg_echo('campaign_reward:reward:nostocked'));	
				}
				forward(REFERER);
			}		
		}
	}
}

/*
Creates a bookobject on user_guid and fundcampaign_guid and a relation with reward_guid.

$params['reward_guid']	
$params['user_guid']
$params['fundcampaign_guid']
$params['amount']
$params['method']
*/
function campaign_reward_do_books ($hook, $type, $returnvalue, $params){
	
	elgg_load_library('elgg:campaign_reward');	

	$is_bookable = campaign_reward_is_stocked ($params['reward_guid']);

	if ($is_bookable) {
		
		$book = new ElggObject();	
		$book->type = 'object';
		$book->subtype = 'reward_book';
		$book->owner_guid = $params['user_guid'];
		$book->container_guid = $params['fundcampaign_guid'];

		$book->amount = $params['amount'];
		$book->method = $params['method'];				
		$book->contributor =$params['user_guid'];				
		$book->save();
		add_entity_relationship ($book->guid, 'reward', $params['reward_guid']);
		
	}else {
		register_error(elgg_echo('reward:stock_insuficient_nothing_booked'));	
	}
	
}

