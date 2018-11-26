<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Adds menu links to the admin toolbar.
 */

//Create the pages
function vy_register_menu_page(){

  //Old system
  //add_menu_page('VYPS Game', 'VYPS Game', 'manage_vidyen', 'vyps-combat-game/pages/manage-equipment.php');

  $parent_page_title = "VYPS Combat Game";
  $parent_menu_title = 'VYPS Combat Game';
  $capability = 'manage_options';
  $parent_menu_slug = 'vyps_combat_game';
  $parent_function = 'vyps-combat-game/pages/manage-equipment.php';
  add_menu_page($parent_page_title, $parent_menu_title, $capability, $parent_menu_slug, $parent_function);

  /*
  add_submenu_page(
        'vyps-combat-game/pages/manage-equipment.php',
        'Create equipment',
        'Create equipment',
        'manage_vidyen',
        'vyps-combat-game/pages/manage-equipment.php'
    );
  */

}



add_action('admin_menu', 'vy_register_menu_page');
