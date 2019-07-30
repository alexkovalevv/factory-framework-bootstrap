<?php
/**
 * Factory Bootstrap
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>
 * @since         1.0.0
 * @package       factory-bootstrap
 * @copyright (c) 2018, Webcraftic Ltd
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// module provides function only for the admin area
if ( ! is_admin() ) {
	return;
}

if ( defined( 'FACTORY_BOOTSTRAP_000_LOADED' ) ) {
	return;
}

define( 'FACTORY_BOOTSTRAP_000_VERSION', '4.1.6' );
define( 'FACTORY_BOOTSTRAP_000_LOADED', true );

define( 'FACTORY_BOOTSTRAP_000_DIR', dirname( __FILE__ ) );
define( 'FACTORY_BOOTSTRAP_000_URL', plugins_url( null, __FILE__ ) );

if ( ! defined( 'FACTORY_FLAT_ADMIN' ) ) {
	define( 'FACTORY_FLAT_ADMIN', true );
}

include_once( FACTORY_BOOTSTRAP_000_DIR . '/includes/functions.php' );