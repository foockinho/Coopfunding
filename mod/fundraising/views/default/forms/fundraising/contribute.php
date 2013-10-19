<?php

$methods = fundraising_get_methods();

if (empty($methods)) {
	echo elgg_echo('fundraising:contribute:nomethod');
	return;
}
?>
<div>
<?php
echo elgg_view('input/text', array(
	'name' => 'amount',
	'value' => $vars['amount'],
	'placeholder' => elgg_echo('fundraising:amount'),
	'autofocus' => true,
));
?>
</div>
<div class="elgg-footer">
<?php
echo elgg_view('input/hidden', array(
	'name' => 'guid',
	'value' => $vars['entity']->guid,
));
echo elgg_view('input/hidden', array(
	'name' => 'reward_guid',
	'value' => $vars['reward_guid'],
));
foreach (fundraising_get_methods() as $method) {
        echo elgg_view('input/submit', array(
		'name' => 'method',
                'value' => elgg_echo('fundraising:contribute:button:method', array($method)),
	));
}
?>
</div>

<?php
echo '<div>';
echo elgg_echo("fundraising:message");
echo '</div>';

?>
