<?php

 /*
Plugin Name:  VYPS Combat Game
Plugin URI:   https://wordpress.org/plugins/vidyen-point-system-vyps/
Description: VidYen Point System game. Spend points by playing games. [cg-my-equipment], [cg-buy-equipment], [cg-battle-log], [cg-battle-log-all], [cg-battle]
Version:      0.1.12
Author:       VidYen, LLC
Author URI:   https://vidyen.com/
License:      GPLv2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

/*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, version 2 of the License
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* See <http://www.gnu.org/licenses/>.
*/

//The usual WordPress security stuff.
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** NOTE: Some developer statemetns before we get started:

CG is a bit of a different beast than VYPS itself, but its designed to work with it. This was not originally writen by Felty but Oclin though things going forward will be rewrote by Felty
Somet issues, is that Oclin (while a good coder) is like most coders in profession and comments only say what he is doing but not why. And I will try to rectify it. Also normative bracketing
and variables and functions will have to be done by me as I am more familiar with the WordPress repository requirements. Also worth of note... I am going to make this only use points that
were created or gained by the VY256 miner. (all you have to tdo is look at the reason *coughs*) anyways... There will be a pro version that will allow the use of all points.
Also I'm going to put in some branding in there as well. I've renamed the table structure and various other minor things, but will continue ad naseum. I will need to create the rules
so that players (or at least admins) now how the math actually works.

I have also decided to make the main php a lot cleaner. I'm almost wanting to recreate the VYPS base and do the same. The Codex feels that SQL should be done on this page so not
going to make an include for that.

***/

//Allright... I don't like this at all. Not one bit. It is hard to read and hard to follow:
//Going to rewrite to make better redabbility. Also it's possible to have VYPS game stand alone so don't need to check for vyps.

//Check for admin rights.
function VYPS_check_if_true_admin(){

	//I'm going to be a little lenient and if you can edit users maybe you should be able to edit their point since you can just
	//Change roles at that point. May reconsider.
	if( current_user_can('install_plugin') OR current_user_can('edit_users') ){

		//echo "You good!"; //Debugging
		return;

	} else {

		echo "<br><br>You need true administrator rights to see this page!"; //Debugging
		exit; //Might be a better solution to iform before exit like an echo before hand, but well....
	}

}

//Hook and install the vyps_cg_intall hook
register_activation_hook(__FILE__, 'vyps_cg_install');

//Note, I had to check my own base to see if I used vyps_cg as a funciton anywheres
function vyps_cg_install(){

  global $wpdb;

  $charset_collate = $wpdb->get_charset_collate();

  //Resources table. This is same as the vyps_points table actually
  $table_name_cg_resources = $wpdb->prefix . 'vyps_cg_resources';

  $sql = "CREATE TABLE {$table_name_cg_resources} (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    name tinytext NOT NULL,
    icon text NOT NULL,
    PRIMARY KEY  (id)
  ) {$charset_collate};";

  //Resource Log. NOTE: I'm going to have to figure out how to transfer this out eventually
  $table_name_cg_resources_log = $wpdb->prefix . 'vyps_cg_resources_log';

  $sql .= "CREATE TABLE {$table_name_points_log} (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    reason varchar(128) NOT NULL,
    user_id mediumint(9) NOT NULL,
    time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    resource_id varchar(11) NOT NULL,
    resource_amount double(64, 0) NOT NULL,
    adjustment varchar(100) NOT NULL,
    vyps_cg_meta_id varchar(64) NOT NULL,
    vyps_cg_meta_data varchar(128) NOT NULL,
    vyps_cg_meta_amount double(64,0) NOT NULL,
    vyps_cg_meta_subid1 mediumint(9) NOT NULL,
    vyps_cg_meta_subid2 mediumint(9) NOT NULL,
    vyps_cg_meta_subid3 mediumint(9) NOT NULL,
    PRIMARY KEY  (id)
  ) {$charset_collate};";

  //Equipment table creation
  $table_name_cg_equipment = $wpdb->prefix . 'vyps_cg_equipment';

  $sql .= "CREATE TABLE {$table_name_cg_equipment} (
    id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT(400) NOT NULL,
    icon VARCHAR(255) NOT NULL,
    point_type_id INTEGER(10) NOT NULL,
    point_cost DECIMAL(16,8) NOT NULL,
    point_sell DECIMAL(16,8) NOT NULL,
    manpower VARCHAR(255) NOT NULL,
    manpower_use INTEGER(10) NOT NULL,
    speed_modifier INTEGER(10) NOT NULL,
    morale_modifier INTEGER(10) NOT NULL DEFAULT 0,
    combat_range INTEGER(10) NOT NULL,
    soft_attack INTEGER(10) NOT NULL DEFAULT 0,
    hard_attack INTEGER(10) NOT NULL DEFAULT 0,
    armor INTEGER(10) NOT NULL DEFAULT '1',
    entrenchment INTEGER(255) NOT NULL DEFAULT '1',
    support INTEGER(1) NOT NULL, /* 0 = no support, 1 = support */
    faction VARCHAR(255) NOT NULL DEFAULT '',
    model_year INTEGER(5) NOT NULL DEFAULT '1970',
    PRIMARY KEY  (id)
  ) {$charset_collate};";

  //Tracking table creation
  $table_name_cg_tracking = $wpdb->prefix . 'vyps_cg_tracking';

  $sql .= "CREATE TABLE {$table_name_cg_tracking} (
    id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
    item_id VARCHAR(255) NOT NULL,
    username VARCHAR(25) NOT NULL,
    battle_id MEDIUMINT(9), /* what battle it was lost in */
    captured_from VARCHAR(25), /* if captured, where from */
    captured_id MEDIUMINT(9), /* what battle it was captured in */
    PRIMARY KEY  (id)
  ) $charset_collate;";

  //Battles table creation
  $table_name_cg_battles = $wpdb->prefix . 'vyps_cg_battles';

  $sql .= "CREATE TABLE {$table_name_cg_battles} (
    id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
    winner VARCHAR(255) NOT NULL,
    loser VARCHAR(255) NOT NULL,
    battle_id INT NOT NULL,
    tie INT NOT NULL,
    battle_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY  (id)
  ) $charset_collate;";

  //Pending battles table creation
  $table_name_cg_pending_battles = $wpdb->prefix . 'vyps_cg_pending_battless';

  $sql .= "CREATE TABLE {$$table_name_cg_pending_battles} (
    id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
    user_one VARCHAR(255) NOT NULL,
    user_two VARCHAR(255),
    battled INTEGER(1) DEFAULT 0,
    PRIMARY KEY  (id)
  ) $charset_collate;";

  //The upgrade check
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

  //Execute the SQL creation
  dbDelta($sql);

}

/*** I just blew out a whole lot of the old code because I didn't like it and didn't want to use it. I'll see what breaks and work my way back in ***/
/*** For now I am going to not have it uninstall any of the tables until I'm sure its working -Felty                                              ***/

/*** Menu Includes ***/
include( plugin_dir_path( __FILE__ ) . 'includes/menus/menu-page.php'); //Main page to display
include( plugin_dir_path( __FILE__ ) . 'includes/menus/design-page.php'); //Equipment design page
include( plugin_dir_path( __FILE__ ) . 'includes/menus/shortcode-page.php'); //BTW I'm going to include the VY256 miner here since it's straight forward

/*** SHORTCODE INCLUDES ***/

include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/battle-log-all-shortcode.php'); //Battle log displaying all battles
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/battle-log-shortcode.php'); //Just current user battle log
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/battle-shortcode.php'); //Running the actual battle shortcode
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/buy-equipment-shortcode.php'); //Buy equipment into inventory
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/my-equipment-shortcode.php'); //Displays inventory
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-256.php'); //Ported miner
