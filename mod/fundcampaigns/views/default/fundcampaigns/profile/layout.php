<?php
/**
 * Layout of the campaigns profile page
 *
 * @package Coopfunding
 * @subpackage fundcampaigns
 * 
 * @uses $vars['entity']
 */

echo elgg_view('fundcampaigns/profile/summary', $vars);
echo elgg_view('fundcampaigns/profile/widgets', $vars);

