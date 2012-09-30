<?php
/*
Plugin Name: Genesis Widget Toggle
Plugin URI: https://github.com/aryaprakasa/genesis-widget-toggle
Description: Genesis widget toggle add additional widget area with toggle.
Author: Arya Prakasa
Author URI: http://prakasa.me/

Version: 0.1

License: GNU General Public License v2.0 (or later)
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

/**
 * The main class that handles the entire output, content filters, etc., for this plugin.
 *
 * @package Genesis Widget Toggle
 * @since 0.1
 */
class Genesis_Widget_Toggle {
	
	/** Constructor */
	function __construct() {
		
		register_activation_hook( __FILE__, array( $this, 'widgettoggle_activation' ) );
		
		define( 'GTW_VERSION', '0.1' );
		define( 'GTW_SETTINGS_FIELD', 'gwt-settings' );
		define( 'GWT_PLUGIN_DIR', dirname( __FILE__ ) );

		add_filter( 'widget_text', 'do_shortcode' );

		add_action( 'genesis_init', array( $this, 'widgettoggle_init' ), 99 );

		add_action('wp_enqueue_scripts', array( $this, 'gwt_script_and_style' ), 999);
		
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
		wp_die( sprintf( __( 'Sorry, you cannot run Genesis Widget Toggle without WordPress %s and <a href="%s">Genesis %s</a>, or greater.', 'widgettoggle' ), $wp_version, 'http://www.studiopress.com/themes/genesis', $genesis_version ) );
		
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

		/** Hook the widget area at genesis_before */
		add_action( 'genesis_before', array( $this, 'gwt_add_sidebar_area' ), 5);

	}

	/**
	 * Function to hook Widget Toggle to 'genesis_after'.
	 *
	 * @since 0.1
	 */
	function gwt_add_sidebar_area(){
		if ( is_active_sidebar( 'gwt-widget-left' ) || is_active_sidebar( 'gwt-widget-middle' ) || is_active_sidebar( 'gwt-widget-right' ) ) : ?>
			<div id="gwt-widget-toggle">
				<div class="wrap">
					<?php
					if ( is_active_sidebar( 'gwt-widget-left' ) ) {
						echo '<div id="gwt-widget-left">';
						dynamic_sidebar( 'gwt-widget-left' );
						echo '</div>'."\n";
					}

					if ( is_active_sidebar( 'gwt-widget-middle' ) ) {
						echo '<div id="gwt-widget-middle">';
						dynamic_sidebar( 'gwt-widget-middle' );
						echo '</div>'."\n";
					}

					if ( is_active_sidebar( 'gwt-widget-right' ) ) {
						echo '<div id="gwt-widget-right">';
						dynamic_sidebar( 'gwt-widget-right' );
						echo '</div>'."\n";
					}

					?>
				</div>
			</div>
			<div id="gwt-widget-toggle-controls">
				<p><a class="hide-widget-toggle" href="#"><?php _e( 'Toggle', 'widgettoggle' )?></a></p>
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
			wp_enqueue_script( 'gwt-scipts', plugin_dir_url( __FILE__ ) .'gwt-script.js', 'jquery', GTW_VERSION, true );
			wp_enqueue_style( 'gwt-style', plugin_dir_url( __FILE__ ) .'gwt-style.css', array(), GTW_VERSION, 'all' );
		}
	}
	
}

$Genesis_Widget_Toggle = new Genesis_Widget_Toggle;