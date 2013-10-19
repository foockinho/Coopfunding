<?php
/**
 * Fundraising-bankaccount plugin
 *
 * @package Coopfunding
 * @subpackage Fundraising.bankaccount
 */

elgg_register_event_handler('init', 'system', 'fundraising_bankaccount_init');

function fundraising_bankaccount_init() {

	elgg_register_library('coopfunding:fundraising:bankaccount', elgg_get_plugins_path() . 'fundraising-bankaccount/lib/fundraising-bankaccount.php');
	
    // register actions
	elgg_register_action('transaction/save', dirname(__FILE__) . '/actions/save.php');
	elgg_register_action('fundraising/bankaccount/delete', dirname(__FILE__) . '/actions/delete.php');
	elgg_register_action('fundraising/bankaccount/bookreward', dirname(__FILE__) . '/actions/bookreward.php');
    elgg_register_plugin_hook_handler('fundcampaigns:sidebarmenus', 'fundcampaign', 'fundraising_bankaccount_set_side_bar_menu');

    fundraising_register_method('bankaccount');
    fundraising_register_currency('eur');
}

function fundraising_bankaccount_page_handler($page) {
 
    if (isset($page[1])) {
        
        elgg_load_library('coopfunding:fundraising:bankaccount');
        
        if (isset($page[2])) {
	        
	        $entity = get_entity($page[2]);    
	    
	        if($entity) {
    	        if (elgg_instanceof($entity, 'group', 'project')) {
                    $entity_text = 'project';
                    $entities_text = 'projects';
                }else{
                    $entity_text = 'fundcampaigns';
                    $entities_text = 'fundcampaigns';
                }
               
    		    elgg_load_library("elgg:{$entities_text}");
    		    
    			elgg_push_breadcrumb(elgg_echo("{$entities_text}"), "{$entities_text}/all");
    			elgg_set_page_owner_guid($entity->guid);
    			
    	       	switch ($page[1]) {
            		case 'contribute':
            			fundraising_bankaccount_contribute_page($entity);
            			break;
            		case 'managedeposits':
            			fundraising_bankaccount_managedeposits_page($entity);
            			break;	
            		case 'add':
            		    $params = fundraising_bankaccount_get_page_content_edit($page[1], $entity->guid);
            			$body = elgg_view_layout('content', $params);
	                    echo elgg_view_page($params['title'], $body);
			            break;	
            		case 'edit':
            			$params = fundraising_bankaccount_get_page_content_edit($page[1], $entity->guid);
            			$body = elgg_view_layout('content', $params);
	                    echo elgg_view_page($params['title'], $body);
			            break;	
            		case 'bankaccount-callback':
                        fundraising_bankaccount_confirm_page($entity);           
                        break;
            		default:
            		    return false;
            	}
            	return true;
            }
        }     
    	return true;
    } else {
       
    }
}

function fundraising_bankaccount_contribute_page ($entity) {
    
    elgg_push_breadcrumb(elgg_echo('fundraising:contribute'));
    
    $title = elgg_echo('fundraising:bankaccount:title', array($entity->name));
	$content = elgg_view('fundraising/bankaccount/contribute', array(
		'entity' => $entity,
		'amount' => get_input('amount'),
	));
	
	$body = elgg_view_layout('content', array(
		'title' => $title,
		'content' => $content,
		'filter' => '',
	));
	echo elgg_view_page($title, $body);
	return true;
}

function fundraising_bankaccount_confirm_page () {
    //include(elgg_get_plugins_path() . 'fundraising-bitcoin/actions/bitcoin-callback.php');   
    //return true
}    

function fundraising_bankaccount_managedeposits_page ($entity) {
    
    $params = fundraising_bankaccount_managedeposits_get_page_content_list($entity->guid);
	fundraising_bankaccount_managedeposits_set_add_button_func($entity->guid);
	
	if (isset($params['sidebar'])) {
		$params['sidebar'] .= elgg_view('undraising-bankaccount/sidebar', array('page' => $page_type));
	}

	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($params['title'], $body);
	return true;
	
}

function fundraising_bankaccount_managedeposits_set_add_button_func ($guid) {

        $text = elgg_echo("fundraising:bankaccount:newdeposit");
        $url = elgg_get_site_url() . "fundraising/bankaccount/add/{$guid}";

    	elgg_register_menu_item('title', array(
				'name' => $text,
				'href' => $url,
				'text' => elgg_echo($text),
				'link_class' => 'elgg-button elgg-button-action',
			));
        return false;
}

function fundraising_bankaccount_set_side_bar_menu ($hook, $entity_type, $return_value, $params) {

	if (elgg_instanceof($params, 'object', 'fundcampaign')) {
	    	$entity = get_entity($params->container_guid);
	} else {
	    	$entity = $params;
	}	

	if ($entity) {
		if ($entity->isMember() || elgg_is_admin_logged_in()) {
			 $return_value .= elgg_view('sidebar/transactions', array('entity' => $params));
		}
	} 

	return $return_value;    
    
}



