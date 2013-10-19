<?php

if (!isset($vars['entity']) || !$vars['entity']) {
	echo elgg_echo('fundcampaigns:notfound');
	return true;
}

$fundcampaign = $vars['entity'];
$owner = $fundcampaign->getOwnerEntity();

if (!$owner) {
	// not having an owner is very bad so we throw an exception
	$msg = elgg_echo('InvalidParameterException:IdNotExistForGUID', array('campaign owner', $fundcampaign->guid));
	throw new InvalidParameterException($msg);
}
$title = elgg_view('output/url', array('text' => $fundcampaign->name, 'href' => 'fundcampaigns/view/' . $fundcampaign->guid));
$tags = elgg_view('output/tags', array('value' => $fundcampaign->interests));
?>

<div class="fundcampaigns-gallery-item">
	<p class="fundcampaigns-gallery-photo"><?php echo elgg_view_entity_icon($fundcampaign, 'medium') ?></p>
	<h3><?php echo $title ?></h3>
	<div class="fundcampaigns-gallery-tags" ><?php echo $tags; ?></div>
	<div class="fundcampaigns-gallery-info">
		<p class="fundcampaigns-gallery-subtitle"><?php echo $fundcampaign->briefdescription?></p>
	</div>
</div>
