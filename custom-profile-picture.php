<?php
/**
 * Plugin Name: Custom Profile Picture
 * Description: Plugin untuk mengubah foto profil pengguna melalui Media Library.
 * Version: 1.0.0
 * Author: Farras Indyawan
 * Author URI: https://kitabuatin.com
 * Text Domain: custom-profile-picture
 */

defined('ABSPATH') or die('Direct script access disallowed.');

define('CPP_PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once CPP_PLUGIN_DIR . 'includes/custom-profile-picture-functions.php';

// Fungsi untuk memuat skrip CSS dan JS
function cpp_enqueue_scripts() {
    wp_enqueue_style('cpp-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
}

add_action('admin_enqueue_scripts', 'cpp_enqueue_scripts');