<?php
var_dump("owner_block_de fundcampaign"); exit();
$fundcampaign = $vars['entity'];

$title = elgg_view('output/url', array(
	'text' => $fundcampaign->name,
	'href' => $fundcampaign->getURL(),
));
$tags = elgg_view('output/tags', array('value' => $fundcampaign->interests));
$description = $fundcampaign->briefdescription;
$friendly_time = elgg_view('output/friendlytime', array('time' => $fundcampaign->time_created));

echo <<<HTML
<div class="fundcampaigns-owner-block mbl">
	<h3>$title</h3>
	$tags
	<div class="elgg-description">$description</div>
	<div class="elgg-subtext">$friendly_time</div>
</div>
HTML;
