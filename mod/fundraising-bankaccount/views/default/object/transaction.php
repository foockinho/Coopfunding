<?php
/**
 * View transaction objects
 *
 * @package transaction
 */

$verifybutton = elgg_extract('verifybutton', $vars, FALSE);
//$full = elgg_extract('full_view', $vars, FALSE);
$transaction = elgg_extract('entity', $vars, FALSE);

if (!$transaction) {
	return TRUE;
}

$owner = get_entity($transaction->contributor);

if ($transaction->method =="bankaccount") {
    $metadata = elgg_view_menu('entity', array(
    	'entity' => $transaction,
    	'handler' => 'fundraising/bankaccount',
    	'sort_by' => 'priority',
    	'class' => 'elgg-menu-hz',
    ));
}

if (elgg_is_active_plugin('fundraising')) {
    elgg_load_library('coopfunding:fundraising');
    
    $container = $transaction->container_guid;
    
    if ($verifybutton) {
        //$metadata .= "confirm button";
    }
}

$owner_icon = elgg_view_entity_icon($owner, 'tiny');
$owner_link = elgg_view('output/url', array(
	'href' => "profile/$owner->username",
	'text' => $owner->name,
	'is_trusted' => true,
));
$author_text = elgg_echo('byline', array($owner_link));
$date = $transaction->commit_date;


$subtitle = "$author_text $date $transaction->method";
$content = "$transaction->eur_amount" . "€";

$params = array(
		'entity' => $transaction,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'content' => $content,
);

$params = $params + $vars;

$list_body = elgg_view('object/summary', $params);
echo elgg_view_image_block($owner_icon, $list_body);

