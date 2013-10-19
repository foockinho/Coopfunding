<?php
/**
 * Elgg moderation plugin language pack
 *
 * @package Coopfunding
 * @subpackage moderation
 */

$language = array(

	/**
	 * Menu items and titles
	 */
	'moderation:verified'=> 'This project has been sent for verification.',
	'moderation:notverified' => 'Verification request could not be done.',
	'moderation:toverifylist' => 'Projects to verify',
	
	'moderation:noprojects' => 'There are not projects to verify'

);

add_translation(basename(__FILE__, '.php'), $language);
