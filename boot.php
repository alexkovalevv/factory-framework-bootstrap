<?php
	/**
	 * Factory Bootstrap
	 *
	 * @author Paul Kashtanoff <paul@byonepress.com>, Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 2013, OnePress Ltd, (c) 2017 Webcraftic Ltd
	 *
	 * @package factory-bootstrap
	 * @since 1.0.0
	 */

	// module provides function only for the admin area
	if( !is_admin() ) {
		return;
	}

	if( defined('FACTORY_BOOTSTRAP_000_LOADED') ) {
		return;
	}
	define('FACTORY_BOOTSTRAP_000_LOADED', true);

	define('FACTORY_BOOTSTRAP_000_DIR', dirname(__FILE__));
	define('FACTORY_BOOTSTRAP_000_URL', plugins_url(null, __FILE__));

	// sets version of admin interface
	define('FACTORY_BOOTSTRAP_000_VERSION', 'FACTORY_BOOTSTRAP_000');
	if( !defined('FACTORY_FLAT_ADMIN') ) {
		define('FACTORY_FLAT_ADMIN', true);
	}

	include_once(FACTORY_BOOTSTRAP_000_DIR . '/includes/functions.php');