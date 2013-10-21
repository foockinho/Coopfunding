<?php

$entity = $vars['entity'];

if ($entity) {
	
	if ($entity->getSubtype() == 'project'){
		$isMember = $entity->isMember();
	} else{		
		$project = get_entity($entity->container_guid);
		$isMember = $project->isMember();		
	}

	if (elgg_is_admin_logged_in() || $isMember) {

		elgg_load_library('coopfunding:fundraising');

		$guid = $entity->guid;

		$body = elgg_view('output/url', array(
			'text' => elgg_echo('fundraising:allcontributions', array($entity->alias)),
			'href' => "fundraising/view/{$guid}",
			'class' => "" 
		));  

		echo elgg_view_module('aside', elgg_echo('fundraising'), $body);
	}
}

