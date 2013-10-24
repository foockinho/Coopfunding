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

	return true;
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

	$entity_type = $entity->getSubtype() ."s";
	moderation_save_icon($entity, $entity_type, "revision", $revision);	

	system_message(elgg_echo("moderation:revision_saved_user"));
	
	return $entity->getUrl();
}

function do_moderation_admin_save($entity, $input) {	

	$entity_type = $entity->getSubtype();

	//save changed fields		
	foreach($input as $shortname => $value) {
		echo (" field: [" . $shortname . "]");
		echo (" project [" . $entity ->$shortname . "]");
		echo (" value: [" . $value . "]");

		if ($entity->$shortname != $value ) {				
			$entity->$shortname = $value;		
		}			
	}		

	//fetch entity current revision
	$revision = get_moderation_last_revision($entity);
	
	if ($revision) {
		$revision->state = 'commited';		
		$revision->save();		
	}    	

	$entity_type = $entity->getSubtype() ."s";
	moderation_save_icon($entity, $entity_type, "commit", $revision);

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

function moderation_get_field_icon ($entity, $revision) {

	$filename = "{$entity->getSubtype()}icon/{$entity->guid}/medium/{$entity->icontime}.jpg";

	$img_params = array(
			'src' => $filename,
			'alt' => $title,	
			'width' => '300'
	);
	$output = elgg_view('output/img', $img_params);


	$filename = "{$entity->getSubtype()}icon/{$entity->guid}revision{$revision->guid}/medium/{$revision->icontime}.jpg";	
	$img_params = array(
			'src' => $filename,
			'alt' => $title,
			'width' => '300'	
	);
	$output .= elgg_view('output/img', $img_params);			

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
	
	return false;
}

	
function moderation_get_request_admin_button ($guid) {

	return "";
}

/*Manage $entity's ico, that is a group/entity (project or fundcampaing) 

$save_revision
	NEW --> upload ico
	REVISION --> upload ico but save it as {$entity_guid}'revision#guid.jpg'; 
	COMMIT --> 
		if has_uploaded_icon then
			upload ico 
			DESACTIVATED: delete if exists {$entity_guid}'revision#guid.jpg'; 
		else
			copy {$entity_guid}'revision#guid.jpg' to {$entity_guid}.jpg
			
*/

function moderation_save_icon ($entity, $entity_type, $save_revision, $revision) {

	$has_uploaded_icon = (!empty($_FILES['icon']['type']) && substr_count($_FILES['icon']['type'], 'image/'));

	if ($has_uploaded_icon || $save_revision == "commit") {
		
		switch ($save_revision){
			case "new":
				$prefix_to_upload = "{$entity_type}/" . $entity->guid;
				$entity->icontime = time();
				break;
			case "revision":
				$prefix_to_upload = "{$entity_type}/{$entity->guid}revision{$revision->guid}";				
				$revision->icontime = time();					
				break;
			case "commit":				
				if ($has_uploaded_icon) {
					$prefix_to_upload = "{$entity_type}/{$entity->guid}";
					$entity->icontime = time();
					//$prefix_to_del = "{$entity_type}/" . $entity->guid . "revision{$revision}";
				} else {
					$prefix_copy_to = "{$entity_type}/{$entity->guid}";
					$prefix_copy_from = "{$entity_type}/{$entity->guid}revision{$revision->guid}";
					$entity->icontime = time();					
				}
				break;
			default:
				break;			
		}
		
		if ($prefix_to_upload) {

			elgg_load_library("elgg:{$entity_type}");
			$icon_sizes = elgg_get_config("{$entity_type}_icon_sizes");
		
			$filehandler = new ElggFile();
			$filehandler->owner_guid = $entity->owner_guid;
			$filehandler->setFilename($prefix_to_upload . ".jpg");
			$filehandler->open("write");
			$filehandler->write(get_uploaded_file('icon'));
			$filehandler->close();
			$filename = $filehandler->getFilenameOnFilestore();

			$sizes = array('tiny', 'small', 'medium', 'large');
			$thumbs = array();
			foreach ($sizes as $size) {
				$thumbs[$size] = call_user_func("{$entity_type}_get_resized_and_cropped_image_from_existing_file", $filename,
					$icon_sizes[$size]['w'],
					$icon_sizes[$size]['h']
				);
			}
			
			if ($thumbs['tiny']) { // just checking if resize successful
				$thumb = new ElggFile();
				$thumb->owner_guid = $entity->owner_guid;
				$thumb->setMimeType('image/jpeg');

				foreach ($sizes as $size) {
				
					$thumb->setFilename("{$prefix_to_upload}{$size}.jpg");
					
					$thumb->open("write");
					$thumb->write($thumbs[$size]);
					$thumb->close();		
				}					
			}
		}	


		if ($prefix_copy_from) {			
			$sizes = array('', 'tiny', 'small', 'medium', 'large');
			foreach ($sizes as $size) {				

				$filehandler = new ElggFile();			
				$filehandler->setFilename("{$prefix_copy_from}{$size}.jpg");
				$filehandler->owner_guid = $entity->owner_guid;
				$from = $filehandler->getFilenameOnFilestore();

				$filehandler = new ElggFile();			
				$filehandler->setFilename("{$prefix_copy_to}{$size}.jpg");
				$filehandler->owner_guid = $entity->owner_guid;
				$to = $filehandler->getFilenameOnFilestore();
	
				copy($from, $to);
			}			
		}
	
		/*
		if ($prefix_to_del) {
			$filehandler = new ElggFile();
			$filehandler->setFilename($prefix_to_del);
			if ($filehandler->open("read")) {
				$path = $filehandler->getFilenameOnFilestore();

				$sizes = array('', 'tiny', 'small', 'medium', 'large');
				foreach ($sizes as $size) {
					unlink("$path/{$entity_guid}revision{$revision}{$size}.jpg");
				}
			}
		}
		*/		
	}	
	return true;
}


