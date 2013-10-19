<?php
/**
 * Campaigns profile summary
 *
 * Icon and profile fields
 *
 * @package Coopfunding
 * @subpackage fundcampaign
 *
 * @uses $vars['fundcampaign']
 */

if (!isset($vars['entity']) || !$vars['entity']) {
	echo elgg_echo('fundcampaigns:notfound');
	return true;

}

$fundcampaign = $vars['entity'];
$owner = $fundcampaign->getOwnerEntity();

if (!$owner) {
	// not having an owner is very bad so we throw an exception
	$msg = elgg_echo('InvalidParameterException:IdNotExistForGUID', array('fundcampaign owner', $fundcampaign->guid));
	throw new InvalidParameterException($msg);
}

?>
<div class="fundcampaigns-profile">
	<div class="elgg-image">
		<div class="fundcampaigns-profile-icon">
			<?php
				echo elgg_view_entity_icon($fundcampaign, 'large', array(
					'href' => '',
					'width' => '100%',
					'height' => '',
				));
			?>
		</div>
	</div>

	<div class="fundcampaigns-profile-description elgg-body">
		<?php
			echo elgg_view('output/longtext', array('value' => $fundcampaign->description));
		?>
	</div>
</div>
<?php
?>

