<?php
	/**
	 * This file manages assets of the Factory Bootstap.
	 *
	 * @author Alex Kovalev <alex@byonepress.com>
	 * @author Paul Kashtanoff <paul@byonepress.com>
	 * @copyright (c) 2018, OnePress Ltd
	 *
	 * @package factory-bootstrap
	 * @since 1.0.0
	 */
	
	add_action('wbcr_factory_bootstrap_000_plugin_created', 'wbcr_factory_bootstrap_000_plugin_created');
	
	/**
	 * @param Wbcr_Factory000_Plugin $plugin
	 */
	function wbcr_factory_bootstrap_000_plugin_created($plugin)
	{
		$manager = new Wbcr_FactoryBootstrap000_Manager($plugin);
		$plugin->setBootstap($manager);
	}
	
	if( !class_exists('Wbcr_FactoryBootstrap000_Manager') ) {
		
		/**
		 * The Bootstrap Manager class.
		 *
		 * @since 3.2.0
		 */
		class Wbcr_FactoryBootstrap000_Manager {
			
			/**
			 * A plugin for which the manager was created.
			 *
			 * @since 3.2.0
			 * @var Wbcr_Factory000_Plugin
			 */
			public $plugin;
			
			/**
			 * Contains scripts to include.
			 *
			 * @since 3.2.0
			 * @var string[]
			 */
			public $scripts = array();
			
			/**
			 * Contains styles to include.
			 *
			 * @since 3.2.0
			 * @var string[]
			 */
			public $styles = array();
			
			/**
			 * Createas a new instance of the license api for a given plugin.
			 *
			 * @since 1.0.0
			 */
			public function __construct(Wbcr_Factory000_Plugin $plugin)
			{
				$this->plugin = $plugin;
				
				add_action('admin_enqueue_scripts', array($this, 'loadAssets'));
				add_filter('admin_body_class', array($this, 'adminBodyClass'));
			}
			
			/**
			 * Includes the Bootstrap scripts.
			 * @since 3.2.0
			 * @param array|string $scripts
			 */
			public function enqueueScript($scripts)
			{
				if( is_array($scripts) ) {
					foreach($scripts as $script) {
						if( !in_array($script, $this->scripts) ) {
							$this->scripts[] = $script;
						}
					}
				} else {
					if( !in_array($scripts, $this->scripts) ) {
						$this->scripts[] = $scripts;
					}
				}
			}
			
			/**
			 *  * Includes the Bootstrap styles.
			 *
			 * @since 3.2.0
			 * @param array|string $styles
			 */
			public function enqueueStyle($styles)
			{
				
				if( is_array($styles) ) {
					foreach($styles as $style) {
						if( !in_array($style, $this->styles) ) {
							$this->styles[] = $style;
						}
					}
				} else {
					if( !in_array($styles, $this->styles) ) {
						$this->styles[] = $styles;
					}
				}
			}
			
			/**
			 * Loads Bootstrap assets.
			 *
			 * @see admin_enqueue_scripts
			 *
			 * @since 3.2.0
			 * @return void
			 */
			public function loadAssets($hook)
			{
				
				do_action('wbcr_factory_000_bootstrap_enqueue_scripts', $hook);
				do_action('wbcr_factory_000_bootstrap_enqueue_scripts_' . $this->plugin->getPluginName(), $hook);
				
				$dependencies = array();
				if( !empty($this->scripts) ) {
					$dependencies[] = 'jquery';
					$dependencies[] = 'jquery-ui-core';
					$dependencies[] = 'jquery-ui-widget';
				}
				
				foreach($this->scripts as $script) {
					switch( $script ) {
						case 'plugin.iris':
							$dependencies[] = 'jquery-ui-widget';
							$dependencies[] = 'jquery-ui-slider';
							$dependencies[] = 'jquery-ui-draggable';
							break;
					}
				}
				
				// Issue #FB-3:
				// Tests if we can access load-styles.php and load-scripts.php remotely.
				// If yes, we use load-styles.php and load-scripts.php to load, merge and compress css and js.
				// Otherwise, every resource will be loaded separatly.
				
				$is_wp_content_access_tested = $this->plugin->getOption('factory_wp_content_access_tested', false);
				
				if( !$is_wp_content_access_tested ) {
					$this->plugin->updateOption('factory_css_js_compression', false);
					$this->plugin->updateOption('factory_wp_content_access_tested', true);
					
					if( function_exists('wp_remote_get') ) {
						$result = wp_remote_get(FACTORY_BOOTSTRAP_000_URL . '/includes/load-scripts.php?test=1');
						if( !is_wp_error($result) && $result && isset($result['body']) && $result['body'] == 'success' ) {
							$this->plugin->updateOption('factory_css_js_compression', true);
						}
					}
				}
				
				$compression = $this->plugin->getOption('factory_css_js_compression', false);
				
				if( !$compression ) {
					
					$id = md5(FACTORY_BOOTSTRAP_000_VERSION);
					
					$is_first = true;
					foreach($this->scripts as $script_to_load) {
						$script_to_load = sanitize_text_field($script_to_load);
						wp_enqueue_script($script_to_load . '-' . $id, FACTORY_BOOTSTRAP_000_URL . "/assets/js/$script_to_load.js", $is_first
							? $dependencies
							: false, $this->plugin->getPluginVersion());
						$is_first = false;
					}
					
					foreach($this->styles as $style_to_load) {
						$style_to_load = sanitize_text_field($style_to_load);
						wp_enqueue_style($style_to_load . '-' . $id, FACTORY_BOOTSTRAP_000_URL . "/assets/flat/css/$style_to_load.css", array(), $this->plugin->getPluginVersion());
					}
					// - //
					
				} else {
					
					$load_scripts_out = join(',', $this->scripts);
					$load_styles_out = join(',', $this->styles);
					
					if( defined('WP_DEBUG') && WP_DEBUG ) {
						$load_scripts_out .= "&debug=true";
						$load_styles_out .= "&debug=true";
					}
					
					if( !empty($this->styles) ) {
						$id = md5($load_styles_out . FACTORY_BOOTSTRAP_000_VERSION);
						wp_enqueue_style('wbcr-factory-bootstrap-000-' . $id, FACTORY_BOOTSTRAP_000_URL . '/includes/load-styles.php?folder=flat&load=' . $load_styles_out, array(), $this->plugin->getPluginVersion());
					}
					
					if( !empty($this->scripts) ) {
						$id = md5($load_scripts_out . FACTORY_BOOTSTRAP_000_VERSION);
						wp_enqueue_script('wbcr-factory-bootstrap-000-' . $id, FACTORY_BOOTSTRAP_000_URL . '/includes/load-scripts.php?load=' . $load_scripts_out, $dependencies, $this->plugin->getPluginVersion());
					}
					
					// Issue #FB-4:
					// Some themes and plugins contain the functions which remove arguments from the scripts and styles paths.
					// If we use the compression, we need to check whether the paths are the same.
					
					add_filter('script_loader_src', array($this, 'testKeepingArgsInPaths'), 99999, 2);
					add_filter('style_loader_src', array($this, 'testKeepingArgsInPaths'), 99999, 2);
				}
				
				$user_id = get_current_user_id();
				$color_name = get_user_meta($user_id, 'admin_color', true);
				
				if( $color_name !== 'fresh' ) {
					wp_enqueue_style('wbcr-factory-bootstrap-000-colors', FACTORY_BOOTSTRAP_000_URL . '/assets/flat/css/bootstrap.' . $color_name . '.css');
				}
				
				if( $color_name == 'light' ) {
					$primary_dark = '#037c9a';
					$primary_light = '#04a4cc';
				} elseif( $color_name == 'blue' ) {
					$primary_dark = '#d39323';
					$primary_light = '#e1a948';
				} elseif( $color_name == 'coffee' ) {
					$primary_dark = '#b78a66';
					$primary_light = '#c7a589';
				} elseif( $color_name == 'ectoplasm' ) {
					$primary_dark = '#839237';
					$primary_light = '#a3b745';
				} elseif( $color_name == 'ocean' ) {
					$primary_dark = '#80a583';
					$primary_light = '#9ebaa0';
				} elseif( $color_name == 'midnight' ) {
					$primary_dark = '#d02a21';
					$primary_light = '#e14d43';
				} elseif( $color_name == 'sunrise' ) {
					$primary_dark = '#c36822';
					$primary_light = '#dd823b';
				} else {
					$primary_dark = '#0074a2';
					$primary_light = '#2ea2cc';
				}
				
				?>
				
				<script>
					if( !window.factory ) {
						window.factory = {};
					}
					if( !window.factory.factoryBootstrap000 ) {
						window.factory.factoryBootstrap000 = {};
					}
					window.factory.factoryBootstrap000.colors = {
						primaryDark: '<?php echo $primary_dark ?>',
						primaryLight: '<?php echo $primary_light ?>'
					};
				</script>
			<?php
			}
			
			/**
			 * Tests whether the scripts and styles path contain query arguments or them were removed.
			 *
			 * See 'script_loader_src'
			 * See 'style_loader_src'
			 *
			 * @param string $src
			 * @param string $handle
			 * @return mixed
			 */
			public function testKeepingArgsInPaths($src, $handle)
			{
				if( substr($handle, 0, 22) !== 'wbcr-factory-bootstrap-000-' ) {
					return $src;
				}
				
				$parts = explode('?', $src);
				if( count($parts) > 1 ) {
					return $src;
				}
				
				$this->plugin->updateOption('factory_css_js_compression', false);
				
				return $src;
			}
			
			/**
			 * Adds the body classes: 'factory-flat or 'factory-volumetric'.
			 *
			 * @since 3.2.0
			 * @param string $classes
			 * @return string
			 */
			public function adminBodyClass($classes)
			{
				$classes .= FACTORY_FLAT_ADMIN
					? ' factory-flat '
					: ' factory-volumetric ';
				
				return $classes;
			}
		}
	}