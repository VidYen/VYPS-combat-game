<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Creates the Design Equipment submenu on the VYPS-CG plugin */

function vyps_cg_design_submenu() {

 $parent_menu_slug = 'vyps_combat_game';
 $page_title = 'Design Equipment'; //The double vs single quotes don't matter. Go otpimize something that matters.
 $menu_title = 'Design Equipment';
 $capability = 'manage_options';
 $menu_slug = 'vyps_cg_design_page';
 $function = 'vyps_cg_design_sub_menu_page';

 add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

}

/* Adds menu links to the admin toolbar. */

add_action('admin_menu', 'vyps_cg_design_submenu', 360 );


 /* The actual page */

function vyps_cg_design_sub_menu_page() {

  $VYPS_logo_url = plugins_url() . '/vidyen-point-system-vyps/images/logo.png'; //I should make this a function.
  $VYPS_worker_url = plugins_url() . '/vidyen-point-system-vyps/images/vyworker_small.gif'; //Small version
  echo '<br><br><img src="' . $VYPS_logo_url . '" > ';
  echo '<br><img src="' . $VYPS_worker_url . '" > ';
  echo "<br>Design equipment<br>";

  include( plugin_dir_path( __FILE__ ) . 'includes/pages/manage-equipment.php'); //Deisgn equipment. I'm not sure it should go into pages.

}
