<?php
/**
 * Request entity verification
 * 
 * @package Coopfunding
 * @subpackage moderation
 */
		
$guid = (int) get_input('guid');
$entity = get_entity($guid);

if (!$entity->canEdit()) {
	register_error(elgg_echo('moderation:notsenttoverify'));
	forward(REFERER);
}

if (($entity) && ($entity instanceof ElggGroup)) {
	$entity->state = 'request';
	system_message(elgg_echo('moderation:senttoverify'));
} else {
	register_error(elgg_echo('moderation:notsenttoverify'));
}

forward($entity->getUrl());
