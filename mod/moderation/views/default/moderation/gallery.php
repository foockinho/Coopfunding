<?php

if (!isset($vars['entity']) || !$vars['entity']) {
	echo elgg_echo('projects:notfound');
	return true;
}

$entity = $vars['entity'];
$entity_type = $entity->getSubtype() . "s";

$owner = $entity->getOwnerEntity();

if (!$owner) {
	// not having an owner is very bad so we throw an exception
	$msg = elgg_echo('InvalidParameterException:IdNotExistForGUID', array('project owner', $entity->guid));
	throw new InvalidParameterException($msg);
}
$title = elgg_view('output/url', array('text' => $entity->name, 'href' => $entity_type . "/edit/" .$entity->alias));
$tags = elgg_view('output/tags', array('value' => $entity->interests));
?>

<div class="projects-gallery-item">
	<p class="projects-gallery-photo"><?php echo elgg_view_entity_icon($entity, 'medium') ?></p>
	<h3><?php echo $title ?></h3>
	<div class="projects-gallery-tags" ><?php echo $tags; ?></div>
	<div class="projects-gallery-info">
		<p class="projects-gallery-subtitle"><?php echo $entity->briefdescription?></p>		
	</div>
	
</div>
