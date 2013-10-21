<?php

/**
 * Gatekeeper for moderation plugin
 */
function moderator_gate_keeper() {	

	if (elgg_is_admin_logged_in()){
		return true;
	}else {
		FORWARD('','404');
	}
}

/**
 * Adds a toggle to extra menu for switching between list and gallery views
 */
function moderation_register_toggle() {

	set_input('list_type', get_input('list_type', 'gallery'));

	$url = elgg_http_remove_url_query_element(current_page_url(), 'list_type');

	if (get_input('list_type', 'list') == 'list') {
		$list_type = "gallery";
		$icon = elgg_view_icon('grid');
	} else {
		$list_type = "list";
		$icon = elgg_view_icon('list');
	}

	if (substr_count($url, '?')) {
		$url .= "&list_type=" . $list_type;
	} else {
		$url .= "?list_type=" . $list_type;
	}


	elgg_register_menu_item('extras', array(
		'name' => 'file_list',
		'text' => $icon,
		'href' => $url,
		'title' => elgg_echo("file:list:$list_type"),
		'priority' => 1000,
	));
}

function get_moderation_last_revision($entity) {

	if ($entity) {
		$revisions = elgg_get_entities_from_metadata( array(
			'type' => 'object',
			'subtype' => 'revision',		
			'container_guid' => $entity->guid,
			'metadata_name' => 'state',
			'metadata_value' => 'in_progress',
			));
	
		if($revisions) {				
			return $revisions[0];
		}
	}
	return null;
}

function get_moderation_revisions($entity) {

	$revisions = elgg_get_entities_from_metadata( array(
		'type' => 'object',
		'subtype' => 'revision',		
		'container_guid' => $entity->guid,		
		));
	
	return $revisions;
}


function do_moderation_user_save($entity, $input) {	

	$entity_type = $entity->getSubtype();

	//fetch entity current revision
	$revision = get_moderation_last_revision($entity);
	
	if (!$revision) {
		
		//create revision
		$revision = new ElggObject();
		$revision->type = 'object';
		$revision->subtype = 'revision';

		$user = elgg_get_logged_in_user_entity();
		$revision->owner_guid = $user->guid;

		$revision->container_guid = $entity->guid;
		
	}
	
	//save changed fields		
	foreach($input as $shortname => $value) {
		echo (" field: [" . $shortname . "]");
		echo (" project [" . $entity ->$shortname . "]");
		echo (" value: [" . $value . "]");

		if ($entity->$shortname != $value ) {				
			$revision->$shortname = $value;
			
			echo (" <br> caso: [" . $revision->$shortname . "] <br>");
		}			
	}

	$revision->state = 'in_progress';
	$revision->save();

	system_message(elgg_echo("moderation:revision_saved_user"));

	
	return $entity->getUrl();


}

function do_moderation_admin_save($entity, $input) {	

	$entity_type = $entity->getSubtype();

	//fetch entity current revision
	$revision = get_moderation_last_revision($entity);
	
	if ($revision) {
		//save changed fields		
		foreach($input as $shortname => $value) {
			echo (" field: [" . $shortname . "]");
			echo (" project [" . $entity ->$shortname . "]");
			echo (" value: [" . $value . "]");

			if ($entity->$shortname != $value ) {				
				$entity->$shortname = $value;
			
				echo (" <br> caso: [" . $revision->$shortname . "] <br>");
			}			
		}		
		
		$revision->state = 'commited';		
		$revision->save();
	} 
	$entity->state = 'commited';
	$entity->access_id = $input['access_id'];
	$entity->save();
	
	system_message(elgg_echo("moderation:revision_saved"));
	return 'moderation/main';

}

function moderation_get_field ($revision, $entity_type, $fieldname, $fieldtype, $fieldvalue) {

	$line_break = '<br />';

	if ($revision->$fieldname) {
		$class = "moderation-edited";
	}

	$output = "<div><label class='{$class}'>";
	$output .= elgg_echo("{$entity_type}:{$fieldname}");
	$output .= "</label>$line_break";

	if ($class) {
		$output .= elgg_view("output/{$fieldtype}", array(
			'name' => $fieldname,
			'value' => $fieldvalue
		));
	}
	
	$valNew = $revision->$fieldname;
	if (!$valNew) {
		$valNew = $fieldvalue;
	}
	$output .=  elgg_view("input/{$fieldtype}", array(
			'name' => $fieldname,
			'value' => $valNew
		));
	$output .= '</div>';

	return $output;
}
	
function moderation_get_request_user_button ($guid) {

	$entity = get_entity($guid);

	if ($entity->state == "in_progress") {
		$request_url = 'action/moderation/request?guid=' . $guid;
		return elgg_view('output/confirmlink', array(
			'text' => elgg_echo('moderation:request'),
			'href' => $request_url,
			'confirm' => elgg_echo('moderation:requestwarning'),
			'class' => 'elgg-button elgg-button-save float-alt',
		));
	}
}

	
function moderation_get_request_admin_button ($guid) {

	return "";

}

