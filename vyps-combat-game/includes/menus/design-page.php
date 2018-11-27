<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Adds menu links to the admin toolbar.
 */

//Create the pages
function vvyps_cg_design_menu_page(){

  //Old system
  //add_menu_page('VYPS Game', 'VYPS Game', 'manage_vidyen', 'vyps-combat-game/pages/manage-equipment.php');


  $parent_menu_slug = 'vyps_combat_game';
  $page_title = 'Shortcodes';
  $menu_title = 'Shortcodes';
  $capability = 'manage_vidyen';
  $menu_slug = 'vyps_cg_design_page';
  $function = 'vyps_cg_design_sub_menu_page';

}

add_action('admin_menu', 'vyps_cg_design_sub_menu_page');
