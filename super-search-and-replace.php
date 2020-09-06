<?php
if (!defined('ABSPATH')) {
    die('DEAD END');
}

/**
 * Super Serach Replace
 *
 * @package           SuperSerachReplace
 * @author            Mayur Saptal
 * @copyright         2020 Mayur Saptal
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Super Serach Replace
 * Plugin URI:        https://saptal.in/Super-Serach-Replace
 * Description:       This is an ultimate plugin which replaces Text from the complete WordPress installation which includes database as well as from file types PHP, CSS, JS, Text.
 * Version:           1.0.0
 * Requires at least: 4.3
 * Requires PHP:      7.0
 * Author:            Mayur Saptal
 * Author URI:        https://saptal.in
 * Text Domain:       super-search-and-replace
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

/*
Super Serach Replace is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Super Serach Replace is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Super Serach Replace. If not, see http://www.gnu.org/licenses/gpl-2.0.txt.
*/

require_once  __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'SuperSerachReplace.php';

add_action('admin_menu', function () {
    $settingsPage = add_options_page('Super Search Replace', 'Super Search Replace', 'manage_options', 'super-search-replace', function () {
        require_once  __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'setting_page.php';
    });
    /**
     * Include the ember admin scripts only on pages where it's needed.
     */
    add_action("admin_enqueue_scripts", function ($hook) use ($settingsPage) {
        if ($hook !== $settingsPage) {
            return;
        }
        // Remove default jQuery since Ember provides its own.
        wp_dequeue_script('jquery');
        wp_enqueue_script('ember-vendor', plugins_url("assets/js/super-search-replace.js", __FILE__));
    });
});

add_action('wp_ajax_ssr_handel', 'super_search_replace_ajax');


function super_search_replace_ajax()
{
    header("Content-Type: application/json");
    if (empty($_POST['action'])) {
        exit();
    }
    $ssr = new SuperSerachReplace();
    $action = $_POST['perfom'];
    $response = array();

    if ($action == 'ssr_status') {
        $response[]  =  $ssr->getLogEnd(wp_upload_dir()['basedir'], 1);
    }

    if ($action == 'ssr_start_replace') {
        $search = $_POST['search'];
        $replace = $_POST['replace'];
        if (!empty($search) && !empty($replace)) {
            if ($ssr->getLogEnd(wp_upload_dir()['basedir'], 1) == 'completed!' || empty($ssr->getLogEnd(wp_upload_dir()['basedir'], 1))) {
                $ssr->setLogPath(wp_upload_dir()['basedir'])->setSearchFor($search)->setReplaceWith($replace)->searchReplace()->clearLog();
                flush_rewrite_rules();
                $response[]  =  $ssr->getLogEnd(wp_upload_dir()['basedir'], 1);
            }
        }
    }

    echo json_encode($response);

    //Don't forget to always exit in the ajax function.
    exit();
}
