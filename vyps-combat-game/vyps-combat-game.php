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

***/

if (! class_exists('VYPS')) {
    class VYPS
    {
        public function __construct()
        {
            global $wpdb;

            $this->includes();

            /** Table Names */

            //tracks equipment available
            $wpdb->vyps_cg_equipment  = $wpdb->prefix . 'vyps_cg_equipment';
            //tracks the user's army
            $wpdb->vyps_cg_tracking    = $wpdb->prefix . 'vyps_cg_tracking';
            //battle log
            $wpdb->vyps_cg_battles    = $wpdb->prefix . 'vyps_cg_battles';
            //pending battles
            $wpdb->vyps_cg_pending_battles    = $wpdb->prefix . 'vyps_cg_pending_battles';

            $wpdb->vyps_points = $wpdb->prefix . 'vyps_points';
        }

        //build extra menus
        private function includes() {

            include_once plugin_dir_path(__file__) . '/includes/menus/menu-page.php'; //Main menu page. Will show menu of existin eqipment and other
            include_once plugin_dir_path(__file__) . '/includes/menus/shortcode-page.php'; //Shortcodes listed in an easy place for them to see.
            include_once plugin_dir_path(__file__) . '/includes/menus/design-page.php'; //Shortcodes listed in an easy place for them to see.
            include_once plugin_dir_path(__file__) . '/includes/shortcodes/battle-log-shortcode.php';
            include_once plugin_dir_path(__file__) . '/includes/shortcodes/battle-log-all-shortcode.php';
            include_once plugin_dir_path(__file__) . '/includes/shortcodes/battle-shortcode.php';
            include_once plugin_dir_path(__file__) . '/includes/shortcodes/buy-equipment-shortcode.php';
            include_once plugin_dir_path(__file__) . '/includes/shortcodes/my-equipment-shortcode.php';

        }
    }

    $vidyen = new VYPS();
}

/**
 * Creates tables and adds roles
 */
function vyps_cg_activate()
{
    global $wpdb;

    //add ability for admins to manage vidyen
    $role = get_role('administrator');
    if (! $role->has_cap('manage_vidyen')) {
        $role->add_cap('manage_vidyen');
    }

    $charset_collate = $wpdb->get_charset_collate();

    $table['vyps_cg_equipment'] = "CREATE TABLE $wpdb->vyps_cg_equipment (
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
    ) $charset_collate;";

    $table['vyps_cg_tracking'] = "CREATE TABLE $wpdb->vyps_cg_tracking (
      id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
      item_id VARCHAR(255) NOT NULL,
      username VARCHAR(25) NOT NULL,
      battle_id MEDIUMINT(9), /* what battle it was lost in */
      captured_from VARCHAR(25), /* if captured, where from */
      captured_id MEDIUMINT(9), /* what battle it was captured in */
      PRIMARY KEY  (id)
    ) $charset_collate;";

    $table['vyps_cg_battles'] = "CREATE TABLE $wpdb->vyps_cg_battles (
      id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
      winner VARCHAR(255) NOT NULL,
      loser VARCHAR(255) NOT NULL,
      battle_id INT NOT NULL,
      tie INT NOT NULL,
      battle_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY  (id)
    ) $charset_collate;";

    $table['vyps_cg_pending_battles'] = "CREATE TABLE $wpdb->vyps_cg_pending_battles (
      id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
      user_one VARCHAR(255) NOT NULL,
      user_two VARCHAR(255),
      battled INTEGER(1) DEFAULT 0,
      PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($table['vyps_cg_equipment']);
    dbDelta($table['vyps_cg_tracking']);
    dbDelta($table['vyps_cg_battles']);
    dbDelta($table['vyps_cg_pending_battles']);
}
register_activation_hook(__FILE__, 'vyps_cg_activate');


/**
 * Deletes tables
 */
function vyps_cg_deactivate()
{
    global $wpdb;

    /*
     * @var $table_name
     * name of table to be dropped
     * prefixed with $wpdb->prefix from the database
     */
    $table_name_log = $wpdb->prefix . 'vyps_cg_battles';
    $wpdb->query("DROP TABLE IF EXISTS $table_name_log");
}
register_deactivation_hook(__FILE__, 'vyps_cg_deactivate');
