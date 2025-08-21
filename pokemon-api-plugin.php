<?php
/**
 * Plugin Name: Pokemon API Plugin
 * Description: Displays data from public PokeAPI, with caching and error handling.
 * Version: 1.0
 * Author: Max Behrens
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants.
define('POKEMON_API_PLUGIN_URL', plugin_dir_url(__FILE__));
define('POKEMON_API_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Include required files.
require_once POKEMON_API_PLUGIN_PATH . 'includes/api-handler.php';
require_once POKEMON_API_PLUGIN_PATH . 'includes/shortcode.php';

class ApiDisplayPlugin {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }
    
    public function init() {
        // Initialize API handler and shortcode.
        new ApiHandler();
        new ApiShortcode();
    }
    
    public function enqueue_styles() {
        wp_enqueue_style(
            'api-display-style',
            POKEMON_API_PLUGIN_URL . 'assets/style.css',
            array(),
            '1.0.0'
        );
    }
}

// Initialize the plugin
new ApiDisplayPlugin();