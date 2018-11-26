<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Creates the VY256 submenu on the main VYPS plugin */

function vyps_cg_shortcode_submenu() {

 $parent_menu_slug = 'vyps_combat_game';
 $page_title = "Shortcodes";
 $menu_title = 'Shortcodes';
 $capability = 'manage_options';
 $menu_slug = 'vyps_cg_shortcode_page';
 $function = 'vyps_cg_shortcode_sub_menu_page';

 add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

}

/* Adds menu links to the admin toolbar. */

add_action('admin_menu', 'vyps_cg_shortcode_submenu', 360 );


 /* The actual page */

function vyps_cg_shortcode_sub_menu_page() {

  $VYPS_logo_url = plugins_url() . '/vidyen-point-system-vyps/images/logo.png'; //I should make this a function.
  $VYPS_worker_url = plugins_url() . '/vidyen-point-system-vyps/images/vyworker_small.gif'; //Small version
  echo '<br><br><img src="' . $VYPS_logo_url . '" > ';
  echo '<br><img src="' . $VYPS_worker_url . '" > ';

   echo "<br>
  	<h1>Shortcodes and Syntax</h1>
  	<p></p>
  	<p>Show User Their Current Equipment</p>
  	<p><b>[cg-my-equipment]</b></p>
  	<p>Show equipment store user can buy from</p>
    <p><b>[cg-buy-equipment]</b></p>
  	<p>Show user their current battle log</p>
  	<p><b>[cg-battle-log]</b></p>
  	<p>Show user the current battle log of all players</p>
  	<p><b>[cg-battle-log-all]</b></p>
  	<p>Shows queue for current players looking for battle</p>
  	<p><b>[cg-battle]</b></p>
   ";

}
