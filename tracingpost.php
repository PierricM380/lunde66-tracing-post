<?php

/*
Plugin Name: LuneDev66 Tracing Post
Plugin URI: https://github.com/PierricM380/tracingpost
Description: Grâce à cette extension vous pourrez, en un clic, dupliquer votre article et ainsi vous concentrer sur la construction de votre site ! Dans le menu "Général" accédez aux réglages pour choisir l'option qui vous correspond le mieux.
Version: 1.0
Author: LuneDev66
Author URI: https://pierricmaryedeveloppeurweb.netlify.app/
*/

use Lunedev66\Tracingpost\TracingPostPlugin;

/*
 * Absolute way to folder of WordPress.
 * Shake if it's on Wordpress App
 * */
if ( ! defined( 'ABSPATH' ) )
	exit;

define('TRACING_POST_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Initialize Composer to load Class
require TRACING_POST_PLUGIN_DIR . 'vendor/autoload.php';

$plugin = new TracingPostPlugin(__FILE__);