<?php
/**
 * Elgg Fundcampaigns plugin edit action.
 *
 * @package Coopfunding
 * @subpackage fundcampaigns
 */


elgg_make_sticky_form('fundcampaigns');

/**
 * wrapper for recursive array walk decoding
 */
function profile_array_decoder(&$v) {
	$v = _elgg_html_decode($v);
}

// Get fundcampaign fields
$input = array();
foreach (elgg_get_config('fundcampaigns') as $shortname => $valuetype) {
	$input[$shortname] = get_input($shortname);

	// @todo treat profile fields as unescaped: don't filter, encode on output
	if (is_array($input[$shortname])) {
		array_walk_recursive($input[$shortname], 'profile_array_decoder');
	} else {
		$input[$shortname] = _elgg_html_decode($input[$shortname]);
	}

	if ($valuetype == 'tags') {
		$input[$shortname] = string_to_tag_array($input[$shortname]);
	}

}

$input['name'] = htmlspecialchars(get_input('name', '', false), ENT_QUOTES, 'UTF-8');
$input['alias'] = htmlspecialchars(get_input('alias', '', false), ENT_QUOTES, 'UTF-8');

$user = elgg_get_logged_in_user_entity();

if ($fundcampaign_guid = (int)get_input('fundcampaign_guid')) {
	$fundcampaign = new ElggObject($fundcampaign_guid);
	if (!$fundcampaign->canEdit()) {
		register_error(elgg_echo("fundcampaigns:cantedit"));
		forward(REFERER);
	}

	//MODERATION PLUGIN INTERCEPTION___________________________________
	$input['access_id'] = (int)get_input('vis', '', false);
	if ($fundcampaign->state != "in_progress") {
		$params = array ('entity'=> $fundcampaign, 'input' => $input); 
		if (elgg_is_active_plugin('moderation')) {	
			$forward_url = elgg_trigger_plugin_hook('moderation:save', 'entity', $params);
			elgg_clear_sticky_form('fundcampaigns');
			forward($forward_url);	
		}
	}	
	//_____________________________________MODERATION PLUGIN INTERCEPTION
} else {
	$fundcampaign = new ElggObject();
	$fundcampaign->subtype = 'fundcampaign';
	$is_new_fundcampaign = true;
	$fundcampaign->state = "in_progress"; //__MODERATION PLUGIN INTERCEPTION
}

elgg_load_library('elgg:fundcampaigns');

if (!isset($input['alias'])) {
	register_error(elgg_echo('fundcampaigns:alias:missing'));
	forward(REFERER);
} elseif (!preg_match("/^[a-zA-Z0-9\-]{2,32}$/", $input['alias'])) {
	register_error(elgg_echo('fundcampaigns:alias:invalidchars'));
	forward(REFERER);
} elseif ($fundcampaign->alias != $input['alias'] && fundcampaigns_get_from_alias($input['alias'])) {
	register_error(elgg_echo('fundcampaigns:alias:already_used'));
	forward(REFERER);
}

// Assume we can edit or this is a new fundcampaign
if (sizeof($input) > 0) {
	foreach($input as $shortname => $value) {
		// update access collection name if fundcampaign name changes
		if (!$is_new_fundcampaign && $shortname == 'name' && $value != $fundcampaign->name) {
			$fundcampaign_name = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
			$ac_name = sanitize_string(elgg_echo('fundcampaigns:fundcampaign') . ": " . $fundcampaign_name);
			$acl = get_access_collection($fundcampaign->group_acl);
			if ($acl) {
				// @todo Elgg api does not support updating access collection name
				$db_prefix = elgg_get_config('dbprefix');
				$query = "UPDATE {$db_prefix}access_collections SET name = '$ac_name'
					WHERE id = $fundcampaign->group_acl";
				update_data($query);
			}
		}

		$fundcampaign->$shortname = $value;
	}
}

// Validate create
if (!$fundcampaign->name) {
	register_error(elgg_echo("fundcampaigns:notitle"));
	forward(REFERER);
}

// Set fundcampaign tool options
$tool_options = elgg_get_config('fundcampaigns_tool_options');
if ($tool_options) {
	foreach ($tool_options as $fundcampaign_option) {
		$option_toggle_name = $fundcampaign_option->name . "_enable";
		$option_default = $fundcampaign_option->default_on ? 'yes' : 'no';
		$fundcampaign->$option_toggle_name = get_input($option_toggle_name, $option_default);
	}
}

// fundcampaign membership
$fundcampaign->membership = ACCESS_PRIVATE;

if ($is_new_fundcampaign) {
	$fundcampaign->access_id = ACCESS_PUBLIC;
}

$fundcampaign->container_guid = get_input('project');
$old_owner_guid = $is_new_fundcampaign ? 0 : $fundcampaign->owner_guid;
$new_owner_guid = (int) get_input('owner_guid');
$project = (int) get_input('project');
$owner_has_changed = false;
$old_icontime = null;
if (!$is_new_fundcampaign && $new_owner_guid && $new_owner_guid != $old_owner_guid) {
	// verify new owner is member and old owner/admin is logged in
	if (is_fundcampaign_member($fundcampaign_guid, $new_owner_guid) && ($old_owner_guid == $user->guid || $user->isAdmin())) {
		$fundcampaign->owner_guid = $new_owner_guid;
		$fundcampaign->container_guid = $project;

		$metadata = elgg_get_metadata(array(
			'guid' => $fundcampaign->guid,
			'limit' => false,
		));
		if ($metadata) {
			foreach ($metadata as $md) {
				if ($md->owner_guid == $old_owner_guid) {
					$md->owner_guid = $new_owner_guid;
					$md->save();
				}
			}
		}

		// @todo Remove this when #4683 fixed
		$owner_has_changed = true;
		$old_icontime = $fundcampaign->icontime;
	}
}


if (get_input('is_active') == "YES"){
    //get other active to make no active;
    $entity = fundcampaigns_get_active_campaign($fundcampaign->container_guid);

    if ($entity && $entity != $fundcampaign && $entity->is_active) {
        $entity->is_active = false;
        $entity->save();
    }
}
$fundcampaign->is_active = get_input('is_active') == "YES";
$fundcampaign->save();

$must_move_icons = ($owner_has_changed && $old_icontime);

// Invisible fundcampaign support
// @todo this requires save to be called to create the acl for the fundcampaign. This
// is an odd requirement and should be removed. Either the acl creation happens
// in the action or the visibility moves to a plugin hook
$visibility = (int)get_input('vis', '', false);
if ($visibility != ACCESS_PUBLIC && $visibility != ACCESS_LOGGED_IN) {
	$visibility = $fundcampaign->group_acl;
}

if ($fundcampaign->access_id != $visibility) {
	$fundcampaign->access_id = $visibility;
}

$fundcampaign->save();


//MODERATION PLUGIN INTERCEPTION____________________________________
//If it is new but never moderated, didn't trigger moderation edit hook, for icon save.
if (elgg_is_active_plugin("moderation")) {

	elgg_load_library ("elgg:moderation");
	moderation_save_icon($fundcampaign, "fundcampaigns", "new", null);
//_____________________________________MODERATION PLUGIN INTERCEPTION


} else {

	$has_uploaded_icon = (!empty($_FILES['icon']['type']) && substr_count($_FILES['icon']['type'], 'image/'));

	if ($has_uploaded_icon) {

		elgg_load_library('elgg:fundcampaigns');

		$icon_sizes = elgg_get_config('fundcampaigns_icon_sizes');

		$prefix = "fundcampaigns/" . $fundcampaign->guid;

		$filehandler = new ElggFile();
		$filehandler->owner_guid = $fundcampaign->owner_guid;
		$filehandler->setFilename($prefix . ".jpg");
		$filehandler->open("write");
		$filehandler->write(get_uploaded_file('icon'));
		$filehandler->close();
		$filename = $filehandler->getFilenameOnFilestore();

		$sizes = array('tiny', 'small', 'medium', 'large');

		$thumbs = array();
		foreach ($sizes as $size) {
			$thumbs[$size] = fundcampaigns_get_resized_and_cropped_image_from_existing_file(
				$filename,
				$icon_sizes[$size]['w'],
				$icon_sizes[$size]['h']
			);

		}

		if ($thumbs['tiny']) { // just checking if resize successful
			$thumb = new ElggFile();
			$thumb->owner_guid = $fundcampaign->owner_guid;
			$thumb->setMimeType('image/jpeg');

			foreach ($sizes as $size) {
				$thumb->setFilename("{$prefix}{$size}.jpg");
				$thumb->open("write");
				$thumb->write($thumbs[$size]);
				$thumb->close();
			}

			$fundcampaign->icontime = time();
		}

	}

	// @todo Remove this when #4683 fixed
	if ($must_move_icons) {

		$filehandler = new ElggFile();
		$filehandler->setFilename('fundcampaigns');
		$filehandler->owner_guid = $old_owner_guid;
		$old_path = $filehandler->getFilenameOnFilestore();

		$sizes = array('', 'tiny', 'small', 'medium', 'large');

		if ($has_uploaded_icon) {
			// delete those under old owner
			foreach ($sizes as $size) {
				unlink("$old_path/{$fundcampaign_guid}{$size}.jpg");
			}
		} else {
			// move existing to new owner
			$filehandler->owner_guid = $fundcampaign->owner_guid;
			$new_path = $filehandler->getFilenameOnFilestore();

			foreach ($sizes as $size) {
				rename("$old_path/{$fundcampaign_guid}{$size}.jpg", "$new_path/{$fundcampaign_guid}{$size}.jpg");
			}
		}
	}
}
// fundcampaign saved so clear sticky form
elgg_clear_sticky_form('fundcampaigns');
system_message(elgg_echo("fundcampaigns:saved"));

forward($fundcampaign->getURL());
