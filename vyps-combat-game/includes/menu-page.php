<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Adds menu links to the admin toolbar.
 */
function vy_register_menu_page()
{
    add_menu_page('VYPS Game', 'VYPS Game', 'manage_vidyen', 'vyps-combat-game/pages/manage-equipment.php');

    add_submenu_page(
        'vyps-combat-game/pages/manage-equipment.php',
        'Create equipment',
        'Create equipment',
        'manage_vidyen',
        'vyps-combat-game/pages/manage-equipment.php'
    );
}
add_action('admin_menu', 'vy_register_menu_page');
