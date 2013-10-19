
<?php
/**
 * Edit/create a campaign wrapper
 *
 * @package Coopfunding
 * @subpackage fundcampaigns
 *
 * @uses $vars['entity'] campaign object
 */


$entity = elgg_extract('entity', $vars, null);

$form_vars = array(
	'enctype' => 'multipart/form-data',
	'class' => 'elgg-form-alt',
	'fundcampaign' => get_input('fundcampaign')
);

echo elgg_view_form('fundcampaigns/edit', $form_vars, fundcampaigns_prepare_form_vars($entity));

