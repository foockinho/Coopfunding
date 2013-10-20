<?php

elgg_register_event_handler('init', 'system', 'moderation_init');

function moderation_init() {

	//Register library
	elgg_register_library('elgg:moderation', elgg_get_plugins_path() . 'moderation/lib/moderation.php');


	//Register actions
	$action_base = elgg_get_plugins_path() . 'moderation/actions';
	//elgg_register_action("moderation/save", "$action_base/save.php", 'public');
	elgg_register_action("moderation/request", "$action_base/request.php", 'public');
	elgg_register_action("moderation/commit", "$action_base/commit.php", 'public');
	
	// Set up the menu
	if (elgg_is_admin_logged_in()) {
		$item = new ElggMenuItem('moderation', elgg_echo('moderation'), 'moderation/main');
		elgg_register_menu_item('site', $item);
	}

	//page handler
	elgg_register_page_handler('moderation', 'moderation_page_handler');
	
	// Register plugin hooks
	elgg_register_plugin_hook_handler('projects:moderation:save', 'entity', 'moderation_do_save');

        /*elgg_register_plugin_hook_handler('permissions_check', 'group', 'projects_permissions_hook');
        elgg_register_plugin_hook_handler('container_permissions_check', 'group', 'projects_container_permissions_hook');*/

	//Css
	elgg_extend_view('css/elgg', 'moderation/css');

}


function moderation_page_handler($page) {

	elgg_load_library('elgg:moderation');	

	moderator_gate_keeper();
	elgg_push_breadcrumb(elgg_echo('moderation'), "moderation/main");

	switch ($page[0]) {

		case "main":
			moderation_register_toggle();
			moderation_handle_main_page();
			break;
		default:			
			break;		
	}
	
	return true;
}


function moderation_handle_main_page() {

	$title = elgg_echo('moderation:manage');

	elgg_push_breadcrumb($title);

	$content = elgg_echo('moderation:manage:new petitions'). "<br>";
	
	$list = elgg_list_entities_from_metadata(array(
	    'type' => 'group',
	    'subtype' => 'project', 
	    'metadata_name' => 'state',
	    'metadata_value' => 'request', 
	    'full_view' => false
	));
	
	if (!$list) {
		$list = elgg_echo('moderation:nonewpetitions') . "<br>";
	}

	$content .= $list;
	
	$content .= elgg_echo('moderation:manage:revision') . "<br>";

	$list= elgg_list_entities_from_metadata(array(
	    'type' => 'object',
	    'subtype' => 'revision',
	    'metadata_name' => 'state',
	    'metadata_value' => 'in_progress', 
	    'full_view' => false
	));

	if (!$list) {
		$list = elgg_echo('moderation:manage:none');
	}
	$content .= $list;

	$params = array(
		'content' => $content,
		'title' => $title,
		'filter' => '',
		'sidebar' => $sidebar
	);
	
	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($title, $body);
}

function moderation_do_save ($hook, $type, $returnvalue, $params) {

	$entity = $params['entity'];
	$input = $params['input'];
	if ($entity) {
		if (elgg_is_admin_logged_in()){
			return do_moderation_admin_save($entity, $input);
		} else {
			return do_moderation_user_save($entity, $input);
		}
	} else {
		return REFERER;
	}	
}

