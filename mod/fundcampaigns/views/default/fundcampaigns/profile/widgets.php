<?php
/**
 * Profile widgets/tools
 *
 * @package Coopfunding
 * @subpackage fundcampaigns
 */

// tools widget area
echo '<ul id="fundcampaigns-tools" class="elgg-gallery elgg-gallery-fluid mtl clearfix">';

// enable tools to extend this area
echo elgg_view("fundcampaigns/tool_latest", $vars);

echo "</ul>";

