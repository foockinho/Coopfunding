<?php
/**
 * Icon display
 *
 * @package Coopfunding
 * @subpackage Projects
 */

require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

$input_guid = get_input('project_guid');

if (elgg_is_active_plugin("moderation")){
	
	$codes = split('revision', $input_guid);

	if ($codes) {
		$project_guid = $codes[0];
		$revision_guid = $codes[1];
		$revision = get_entity($revision_guid);
	}
}

$project = get_entity($project_guid);

if ($revision) {
	$icontime = $revision->icontime;
	$guid = $revision_guid;
	$filename = $input_guid;	
} else{
	$icontime = $project->icontime;
	$guid = $project->guid;	
	$filename = $project->guid;	
}

$etag = $icontime . $guid;

/* @var ElggGroup $project */
if (!($project instanceof ElggGroup)) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

// If is the same ETag, content didn't changed.

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == "\"$etag\"") {
	header("HTTP/1.1 304 Not Modified");
	exit;
}

$size = strtolower(get_input('size'));
if (!in_array($size, array('large', 'medium', 'small', 'tiny', 'master', 'topbar')))
	$size = "medium";

$success = false;

$filehandler = new ElggFile();
$filehandler->owner_guid = $project->owner_guid;
$filehandler->setFilename("projects/" . $filename . $size . ".jpg");

if ($filehandler->open("read")) {
	if ($contents = $filehandler->read($filehandler->size())) {
		$success = true;
	}
}

if (!$success) {
	$location = elgg_get_plugins_path() . "projects/graphics/default{$size}.gif";
	$contents = @file_get_contents($location);
}

header("Content-type: image/jpeg");
header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', strtotime("+10 days")), true);
header("Pragma: public");
header("Cache-Control: public");
header("Content-Length: " . strlen($contents));
header("ETag: \"$etag\"");

echo $contents;


