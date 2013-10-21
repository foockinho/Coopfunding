<?php
/**
 * Elgg moderation plugin language pack
 *
 * @package Coopfunding
 * @subpackage moderation
 */

$language = array(

	'moderation:moderation'=> 'Moderation',
	
	'moderation:manage'=> 'Moderate projects and campaigns',
	'moderation:manage:new petitions'=> 'List of new projects and campaigns',
	'moderation:manage:nonewpetitions'=> '(There is not new projects and campaigns)',
	'moderation:manage:revision'=> 'List of changing request',
	'moderation:manage:norevisions'=> '(There is not changing request for projects or campaigns)',
	
	'moderation:deletewarning'=> 'Are you sure that you want to delete this entity?',

	'moderation:request'=> 'Ready! Ask for publish!',
	'moderation:requestwarning'=> 'Ready? Ask for publish?',			
	'moderation:senttoverify'=> 'Your new entity has been sent to verify.',
	'moderation:notsenttoverify'=> 'Could not send to verify.',

	'moderation:revision_saved'=> 'The revision has been commited.',
	'moderation:revision_saved_user'=> 'Changes has been sent to moderator.',
	
);

add_translation(basename(__FILE__, '.php'), $language);
