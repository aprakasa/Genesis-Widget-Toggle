<?php
/*
Plugin Name: Genesis Widget Toggle
Plugin URI: https://github.com/aryaprakasa/genesis-widget-toggle
Description: Genesis widget toggle add additional widget area with toggle.
Author: Arya Prakasa
Author URI: http://prakasa.me/

Version: 0.2.1

License: GNU General Public License v2.0 (or later)
License URI: http://www.opensource.org/licenses/gpl-license.php
*/


if ( !class_exists( 'Genesis_Widget_Toggle' ) ) :

/**
 * The main class that handles the entire output, content filters, etc., for this plugin.
 *
 * @package 	Genesis Widget Toggle
 * @since 		0.1
 */
class Genesis_Widget_Toggle {
	
	/** Constructor */
	function __construct() {
		/** Register activation hook */
		register_activation_hook( __FILE__, array( $this, 'widgettoggle_activation' ) );
		/** Define plugin constant */
		define( 'GWT_VERSION', '0.1' );
		define( 'GWT_SETTINGS_FIELD', 'gwt-settings' );
		define( 'GWT_DOMAIN', 'gwt' );
		define( 'GWT_PLUGIN_DIR', dirname( __FILE__ ) );
		define( 'GWT_PLUGIN_URL', trailingslashit( plugin_dir_url(__FILE__) ) );
		/** Allow shortcode at widget text */
		add_filter( 'widget_text', 'do_shortcode' );
		/** Register widget area */
		add_action( 'genesis_init', array( $this, 'widgettoggle_init' ), 15 );
		/** Hook Genesis Widget Toggle Admin Settings */
		add_action( 'genesis_init', array( $this, 'gwt_admin' ), 15 );
		/** Hook Genesis Widget Toggle Admin Settings */
		add_action( 'genesis_admin_menu', array( $this, 'gwt_admin_settings' ), 15 );
		/** Hook the widget area at wp_footer() */
		add_action( 'wp_footer', array( $this, 'gwt_add_sidebar_area' ), 5);
		/** Hook the gwt_dynamic_style() area at wp_head() */
		add_action( 'wp_head', array( $this, 'gwt_dynamic_style' ), 999 );
		/** Load plugin script and styles */
		add_action( 'wp_enqueue_scripts', array( $this, 'gwt_script_and_style' ), 999);
	}
	
	/**
	 * This function runs on plugin activation. It checks to make sure Genesis
	 * or a Genesis child theme is active. If not, it deactivates itself.
	 *
	 * @since 0.1
	 */
	function widgettoggle_activation() {
		if ( 'genesis' != basename( TEMPLATEPATH ) ) {
			$this->widgettoggle_deactivate( '1.8', '3.3' );
		}
	}

	/**
	 * Deactivate Widget Toggle.
	 *
	 * This function deactivates Widget Toggle.
	 *
	 * @since 0.1
	 */
	function widgettoggle_deactivate( $genesis_version = '1.8', $wp_version = '3.3' ) {		
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( sprintf( __( 'Sorry, you cannot run Genesis Widget Toggle without WordPress %s and <a href="%s">Genesis %s</a>, or greater.', 'widgettoggle' ), $wp_version, 'http://ayothemes.com/go/genesis/', $genesis_version ) );	
	}

	function gwt_admin() {
		/** Deactivate if not running Genesis 1.8.0 or greater */
		if ( ! class_exists( 'Genesis_Admin_Boxes' ) )
			add_action( 'admin_init', 'widgettoggle_deactivate', 10, 0 );
		/** Admin Menu */
		if ( is_admin() )
			require_once( GWT_PLUGIN_DIR . '/admin/admin.php' );
	} 

	/**
	 * Add the Theme Settings Page
	 *
	 * @since 1.0.0
	 */
	function gwt_admin_settings() {
		global $_gwt_admin_settings;
		$_gwt_admin_settings = new GWT_Admin_settings;	 	
	}

	/**
	 * This function runs all Genesis dependency hook and functions.
	 *
	 * @since 0.1
	 */
	function widgettoggle_init() {
		
		genesis_register_sidebar( array(
			'id'			=> 'gwt-widget-left',
			'name'			=> __( 'Widget Toggle Left', 'widgettoggle' ),
			'description'	=> __( 'Widget Toggle left area.', 'widgettoggle' ),
		) );

		genesis_register_sidebar( array(
			'id'			=> 'gwt-widget-middle',
			'name'			=> __( 'Widget toggle Middle', 'widgettoggle' ),
			'description'	=> __( 'Widget toggle middle area.', 'widgettoggle' ),
		) );

		genesis_register_sidebar( array(
			'id'			=> 'gwt-widget-right',
			'name'			=> __( 'Widget Toggle Right', 'widgettoggle' ),
			'description'	=> __( 'Widget toggle right area.', 'widgettoggle' ),
		) );

	}

	/**
	 * Function to hook Widget Toggle to 'wp_footer'.
	 *
	 * @since 0.1
	 */
	function gwt_add_sidebar_area(){
		if ( is_active_sidebar( 'gwt-widget-left' ) || is_active_sidebar( 'gwt-widget-middle' ) || is_active_sidebar( 'gwt-widget-right' ) ) : ?>
			<div id="gwt-widget-toggle-wrap">
				<div id="gwt-widget-toggle">
					<div class="wrap">
						<?php
							if ( is_active_sidebar( 'gwt-widget-left' ) ) {
								echo '<div id="gwt-widget-left" class="one-third first">'."\n";
								dynamic_sidebar( 'gwt-widget-left' );
								echo '</div>'."\n";
							}

							if ( is_active_sidebar( 'gwt-widget-middle' ) ) {
								echo '<div id="gwt-widget-middle" class="one-third">'."\n";
								dynamic_sidebar( 'gwt-widget-middle' );
								echo '</div>'."\n";
							}

							if ( is_active_sidebar( 'gwt-widget-right' ) ) {
								echo '<div id="gwt-widget-right" class="one-third">'."\n";
								dynamic_sidebar( 'gwt-widget-right' );
								echo '</div>'."\n";
							}
						?>
					</div>
				</div>
				<div id="gwt-widget-toggle-controls">
					<p><a class="hide-widget-toggle" href="#">Toggle</a></p>
				</div>
			</div>
		<?php endif;
	}

	/**
	 * Function to handle script and styles.
	 *
	 * @since 0.1
	 */
	function gwt_script_and_style(){
		if ( !is_admin() && is_active_sidebar( 'gwt-widget-left' ) || is_active_sidebar( 'gwt-widget-middle' ) || is_active_sidebar( 'gwt-widget-right' ) ){
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'gwt-scipts', trailingslashit( GWT_PLUGIN_URL ) .'assets/js/gwt-script.js', array( 'jquery' ), GWT_VERSION, true );
			
			if ( genesis_get_option( 'load_css', GWT_SETTINGS_FIELD ) == 1 ) {
				wp_enqueue_style( 'gwt-style', trailingslashit( GWT_PLUGIN_URL ) .'assets/css/gwt-style.css', array(), GWT_VERSION, 'all' );
			}

		}
	}

	/**
	 * Function to dynamic styles.
	 *
	 * @since 0.2
	 */
	function gwt_dynamic_style(){

		$css = '';

		if ( genesis_get_option( 'position', GWT_SETTINGS_FIELD ) ) {
			$css .= '#gwt-widget-toggle-wrap { position: '. genesis_get_option( 'position', GWT_SETTINGS_FIELD ) .'}';
		}

		if ( genesis_get_option( 'gwt_background', GWT_SETTINGS_FIELD ) ) {
			$css .= '#gwt-widget-toggle { background-color: '. genesis_get_option( 'gwt_background', GWT_SETTINGS_FIELD ) .'}';
		}

		if ( genesis_get_option( 'max_width', GWT_SETTINGS_FIELD ) ) {
			$css .= '#gwt-widget-toggle .wrap { max-width: '. genesis_get_option( 'max_width', GWT_SETTINGS_FIELD ) .'px}';
		}

		if ( genesis_get_option( 'gwt_border', GWT_SETTINGS_FIELD ) ) {
			$css .= '
				#gwt-widget-toggle-controls { border-color: '.genesis_get_option( 'gwt_border', GWT_SETTINGS_FIELD ).' }
				#gwt-widget-toggle-controls .hide-widget-toggle,
				#gwt-widget-toggle-controls .show-widget-toggle { background-color: '. genesis_get_option( 'gwt_border', GWT_SETTINGS_FIELD ) .' }
			';
		}

		if ( genesis_get_option( 'text_color', GWT_SETTINGS_FIELD ) ) {
			$css .= '
				#gwt-widget-toggle h1,
				#gwt-widget-toggle h2,
				#gwt-widget-toggle h2 a,
				#gwt-widget-toggle h3,
				#gwt-widget-toggle h4,
				#gwt-widget-toggle h5,
				#gwt-widget-toggle h6,
				#gwt-widget-toggle p,
				#gwt-widget-toggle label{ color: '.genesis_get_option( 'text_color', GWT_SETTINGS_FIELD ).' }
			';
		}

		if ( genesis_get_option( 'link_color', GWT_SETTINGS_FIELD ) ) {
			$css .= '
				#gwt-widget-toggle a,
				#gwt-widget-toggle iframe a{ color: '. genesis_get_option( 'link_color', GWT_SETTINGS_FIELD ) .' }
			';
		}

		if ( genesis_get_option( 'hover_color', GWT_SETTINGS_FIELD ) ) {
			$css .= '
				#gwt-widget-toggle a:hover,
				#gwt-widget-toggle iframe a:hover{ color: '. genesis_get_option( 'hover_color', GWT_SETTINGS_FIELD ) .' }
			';
		}

		if ( $css <> '' ) {
			$css = "<!-- Genesis Widget Toggle -->\n<style type='text/css'>" .str_replace( array( "\n", "\t", "\r" ), '', $css ). "</style>\n";
			echo $css;
		}

	}
	
}

global $_genesis_widget_toggle;
$_genesis_widget_toggle = new Genesis_Widget_Toggle;

endif;