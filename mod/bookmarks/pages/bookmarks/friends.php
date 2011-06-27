<?php
/**
 * Elgg bookmarks plugin friends page
 *
 * @package ElggBookmarks
 */

$owner = elgg_get_page_owner_entity();

elgg_push_breadcrumb($owner->name, "bookmarks/owner/$owner->username");
elgg_push_breadcrumb(elgg_echo('friends'));

elgg_register_add_button();

$title = elgg_echo('bookmarks:friends');

$content = list_user_friends_objects($owner->guid, 'bookmarks', 10, false);
if (!$content) {
	$content = elgg_echo('bookmarks:none');
}

$params = array(
	'filter_context' => 'friends',
	'content' => $content,
	'title' => $title,
);

$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);