<?php
/*
Plugin Name: Genesis Widget Toggle
Plugin URI: https://github.com/aryaprakasa/genesis-widget-toggle
Description: Genesis widget toggle add additional widget area with toggle.
Author: Arya Prakasa
Author URI: http://prakasa.me/

Version: 0.3

License: GNU General Public License v2.0 (or later)
License URI: http://www.opensource.org/licenses/gpl-license.php
*/


if ( ! class_exists( 'Genesis_Widget_Toggle' ) ) :

/**
 * The main class that handles the entire output, content filters, etc., for this plugin.
 *
 * @package 	Genesis Widget Toggle
 * @since 		0.1
 */
class Genesis_Widget_Toggle {
	
	/**
	 * PHP5 constructor method.
	 *
	 * @since  0.1
	 */
	public function __construct() {
		/** Register activation hook */
		register_activation_hook( __FILE__, array( $this, 'activation' ) );
		/** Load Plugin Constants*/
		add_action( 'plugins_loaded', array( $this, 'constants' ), 1 );
		/* Internationalize the text strings used. */
		add_action( 'plugins_loaded', array( &$this, 'i18n' ), 2 );
		/** Hook Genesis Widget Toggle Admin Settings */
		add_action( 'genesis_init', array( $this, 'admin' ), 15 );
		/** Register widget area */
		add_action( 'genesis_init', array( $this, 'register_sidebar' ), 15 );
		/** Hook Genesis Widget Toggle Admin Settings */
		add_action( 'genesis_admin_menu', array( $this, 'admin_init' ), 15 );
		/** Hook the widget area at wp_footer() */
		add_action( 'wp_footer', array( $this, 'widget_toggle' ), 5 );
		/** Hook the dynamic_style() area at wp_head() */
		add_action( 'wp_head', array( $this, 'dynamic_style' ), 8 );
		/** Load plugin script and styles */
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts_and_styles' ), 999 );
	}

	/**
	 * Defines constants used by the plugin.
	 *
	 * @since  0.2
	 */
	public function constants(){
		/** Define plugin constants */
		define( 'GWT_VERSION', '0.3' );
		define( 'GWT_SETTINGS', 'gwt-settings' );
		/** Set constant path to directory. */
		define( 'GWT_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'GWT_ADMIN_DIR', GWT_PLUGIN_DIR . trailingslashit( 'admin' ) );
		define( 'GWT_ASSETS_DIR', GWT_PLUGIN_DIR . trailingslashit( 'assets' ) );
		/** Set the constant path to directory URI. */
		define( 'GWT_PLUGIN_URI', trailingslashit( plugin_dir_url(__FILE__) ) );
		define( 'GWT_ADMIN_URI', GWT_PLUGIN_URI . trailingslashit( 'admin' ) );
		define( 'GWT_ASSETS_URI', GWT_PLUGIN_URI . trailingslashit( 'assets' ) );
	}

	/**
	 * Loads the translation files.
	 *
	 * @since  0.3
	 */
	public function i18n() {
		/* Load the translation of the plugin. */
		load_plugin_textdomain( 'widgettoggle', false, 'genesis-widget-toggle/languages' );
	}

	/**
	 * Hook the admin page for Genesis Widget Toggle.
	 *
	 * @since  0.2
	 */
	public function admin() {
		/** Deactivate if not running Genesis 1.8.0 or greater */
		if ( ! class_exists( 'Genesis_Admin_Boxes' ) )
			add_action( 'admin_init',  array( $this, 'deactivation' ), 10, 0 );
		/** Admin Menu */
		if ( is_admin() )
			require_once( GWT_ADMIN_DIR . 'admin.php' );
	} 

	/**
	 * Add the Theme Settings Page
	 *
	 * @since 0.2
	 */
	function admin_init() {
		global $_gwt_admin_settings;
		$_gwt_admin_settings = new GWT_Admin_settings;	 	
	}

	/**
	 * This function runs all Genesis dependency hook and functions.
	 *
	 * @since 0.1
	 */
	function register_sidebar() {		
		genesis_register_sidebar( array(
			'id'			=> 'gwt-widget-left',
			'name'			=> __( 'Widget Toggle Left', 'widgettoggle' ),
			'description'	=> __( 'Widget Toggle left area.', 'widgettoggle' ) ) );
		genesis_register_sidebar( array(
			'id'			=> 'gwt-widget-middle',
			'name'			=> __( 'Widget toggle Middle', 'widgettoggle' ),
			'description'	=> __( 'Widget toggle middle area.', 'widgettoggle' ) ) );
		genesis_register_sidebar( array(
			'id'			=> 'gwt-widget-right',
			'name'			=> __( 'Widget Toggle Right', 'widgettoggle' ),
			'description'	=> __( 'Widget toggle right area.', 'widgettoggle' ) ) );
	}

	/**
	 * Function to hook Widget Toggle to 'wp_footer'.
	 *
	 * @since 0.1
	 */
	function widget_toggle(){
		if (is_active_sidebar( 'gwt-widget-left' ) ||
			is_active_sidebar( 'gwt-widget-middle' ) ||
			is_active_sidebar( 'gwt-widget-right' ) ) :

			echo '<div class="widget-toggle-container">';				
				genesis_markup( array(
					'html5'   => '<aside %s>',
					'xhtml'   => '<div class="widget-toggle">',
					'context' => 'widget-toggle' ) );
					
					echo '<div class="wrap">';

						if ( is_active_sidebar( 'gwt-widget-left' ) ) {
							echo '<div class="gwt-widget-left">'."\n";
							dynamic_sidebar( 'gwt-widget-left' );
							echo '</div>'."\n";
						}
						if ( is_active_sidebar( 'gwt-widget-middle' ) ) {
							echo '<div class="gwt-widget-middle">'."\n";
							dynamic_sidebar( 'gwt-widget-middle' );
							echo '</div>'."\n";
						}
						if ( is_active_sidebar( 'gwt-widget-right' ) ) {
							echo '<div class="gwt-widget-right">'."\n";
							dynamic_sidebar( 'gwt-widget-right' );
							echo '</div>'."\n";
						}

					echo '</div>';

				echo genesis_html5() ? '</aside>' : '</div>';
				$toggle = genesis_get_option( 'toggle_text', GWT_SETTINGS ) ? genesis_get_option( 'toggle_text', GWT_SETTINGS ) : '<i class="toggle-icon"></i>';
				echo '<div class="widget-toggle-control"><a class="hide-widget-toggle" href="#">'. $toggle .'</a></div>';
			echo '</div>';

		endif;
	}

	/**
	 * Function to handle script and styles.
	 *
	 * @since 0.2
	 */
	function scripts_and_styles(){
		if (is_active_sidebar( 'gwt-widget-left' ) ||
			is_active_sidebar( 'gwt-widget-middle' ) ||
			is_active_sidebar( 'gwt-widget-right' ) ) {
			wp_enqueue_script( 'gwt-script', GWT_ASSETS_URI .'js/gwt-script.js', array( 'jquery' ), GWT_VERSION, true );			
			if ( genesis_get_option( 'load_css', GWT_SETTINGS ) == 1 )
				wp_enqueue_style( 'gwt-style', GWT_ASSETS_URI .'css/gwt-style.css', array(), GWT_VERSION, 'all' );
		}
	}

	/**
	 * Function to dynamic styles.
	 *
	 * @since 0.2
	 */
	public function dynamic_style(){
		$css = '';

		if ( genesis_get_option( 'position', GWT_SETTINGS ) )
			$css .= '.widget-toggle-container { position: '. genesis_get_option( 'position', GWT_SETTINGS ) .'}';

		if ( genesis_get_option( 'gwt_background', GWT_SETTINGS ) )
			$css .= '.widget-toggle { background-color: '. genesis_get_option( 'gwt_background', GWT_SETTINGS ) .'}';

		if ( genesis_get_option( 'max_width', GWT_SETTINGS ) )
			$css .= '.widget-toggle .wrap { max-width: '. genesis_get_option( 'max_width', GWT_SETTINGS ) .'px}';

		if ( genesis_get_option( 'gwt_border', GWT_SETTINGS ) ) {
			$css .= '
				.widget-toggle-control { border-color: '. genesis_get_option( 'gwt_border', GWT_SETTINGS ).' }
				.widget-toggle-control .hide-widget-toggle,
				.widget-toggle-control .show-widget-toggle { background-color: '. genesis_get_option( 'gwt_border', GWT_SETTINGS ) .' }
			';
		}

		if ( genesis_get_option( 'text_color', GWT_SETTINGS ) ) {
			$css .= '
				.widget-toggle,
				.widget-toggle h1,
				.widget-toggle h2,
				.widget-toggle h3,
				.widget-toggle h4,
				.widget-toggle h5,
				.widget-toggle h6,
				.widget-toggle p,
				.widget-toggle label{ color: '.genesis_get_option( 'text_color', GWT_SETTINGS ).' }
			';
		}

		if ( genesis_get_option( 'link_color', GWT_SETTINGS ) ) {
			$css .= '
				.widget-toggle a,
				.widget-toggle iframe a{ color: '. genesis_get_option( 'link_color', GWT_SETTINGS ) .' }
			';
		}

		if ( genesis_get_option( 'hover_color', GWT_SETTINGS ) ) {
			$css .= '
				.widget-toggle a:hover,
				.widget-toggle iframe a:hover{ color: '. genesis_get_option( 'hover_color', GWT_SETTINGS ) .' }
			';
		}

		if ( $css <> '' ) {
			$css = "<!-- Genesis Widget Toggle -->\n<style type='text/css'>". str_replace( array( "\n", "\t", "\r" ), '', $css ) ."</style>\n";
			echo $css;
		}

	}

	/**
	 * This function runs on plugin activation. It checks to make sure Genesis
	 * or a Genesis child theme is active. If not, it deactivates itself.
	 *
	 * @since 0.1
	 */
	function activation() {
		if ( 'genesis' != basename( TEMPLATEPATH ) ) {
			$this->deactivation( '1.8', '3.3' );
		}
	}

	/**
	 * Deactivate Widget Toggle.
	 *
	 * This function deactivates Widget Toggle.
	 *
	 * @since 0.1
	 */
	function deactivation( $genesis_version = '1.8', $wp_version = '3.3' ) {		
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( sprintf( __( 'Sorry, you cannot run Genesis Widget Toggle without WordPress %s and <a href="%s">Genesis %s</a>, or greater.', 'widgettoggle' ), $wp_version, 'http://ayothemes.com/go/genesis/', $genesis_version ) );	
	}
	
}

global $_genesis_widget_toggle;
$_genesis_widget_toggle = new Genesis_Widget_Toggle;

endif;