<?php

$projects = $vars['list'];
if (!$projects) {
	echo elgg_echo('moderation:noprojects');
	return true;
}

echo '<div class="moderation-container">';
	echo $projects;
echo '</div>';

echo '<div class="elgg-foot moderation-buttonbank">';

	echo elgg_view('input/submit', array(
		'value' => elgg_echo('verify'),
		'name' => 'verify',
		'class' => 'elgg-button-moderation elgg-requires-confirmation',
		'title' => elgg_echo('moderation:plural'),
	));	

echo '</div>';
