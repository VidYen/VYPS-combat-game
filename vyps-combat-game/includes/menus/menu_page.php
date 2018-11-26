<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Adds menu links to the admin toolbar. */

//Menu action
function vyps_cg_main_menu_page(){

  $parent_page_title = "VYPS Combat Game";
  $parent_menu_title = 'VYPS CG';
  $capability = 'manage_options';
  $parent_menu_slug = 'vyps_points';
  $parent_function = 'vyps_cg_main_menu_display_page';

  add_menu_page($parent_page_title, $parent_menu_title, $capability, $parent_menu_slug, $parent_function);

}

add_action('admin_menu', 'vyps_cg_main_menu_page');

function vyps_cg_main_menu_display_page() {

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
