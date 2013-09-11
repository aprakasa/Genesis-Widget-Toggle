<?php
/**
 * Registers a new admin page, providing content and corresponding menu item
 * for the plugin Settings page.
 *
 * @package Genesis Widget Toggle
 * @subpackage Admin
 *
 * @since 0.2
 */
class GWT_Admin_settings extends Genesis_Admin_Boxes {
 
	/**
	 * Create an admin menu item and settings page.
	 * 
	 * @since 0.2
	 */
	function __construct() { 
		/** unique page ID for Genesis Admin */
		$page_id = 'genesis-widget-toggle'; 
		/** define the menu and page titles */
		$menu_ops = array(
			'submenu' => array(
				'parent_slug' => 'genesis',
				'page_title'  => __( 'Genesis Widget Toggle Settings', 'widgettoggle' ),
				'menu_title'  => __( 'Widget Toggle', 'widgettoggle' ),
			)
		);
		/** Genesis Widget Toggle Setting Fields */
		$settings_field = GWT_SETTINGS;
 		/** Genesis Page Opt */
		$page_ops = array();	
		/** Genesis Widget Toggle default values */
		$default_settings = array(
			'toggle_text'		=> '',
			'gwt_background'	=> '#333333',
			'gwt_border'		=> '#222222',
			'text_color'		=> '#aaaaaa',
			'link_color'		=> '#FFE000',
			'hover_color'		=> '#ffffff',
			'max_width'			=> '960',
			'position'   		=> 'absolute',
			'load_css' 			=> 1,
		);
		/** Create the admin page */
		$this->create( $page_id, $menu_ops, $page_ops, $settings_field, $default_settings ); 
		/**  Initialize the Sanitization Filter */
		add_action( 'genesis_settings_sanitizer_init', array( $this, 'sanitization_filters' ) ); 
	}
 
	/** 
	 * Set up Sanitization Filters
	 *
	 * @since 0.2
	 */	
	function sanitization_filters() {
		genesis_add_option_filter( 'no_html', $this->settings_field,
			array(
				'toggle_text',
				'gwt_background',
				'gwt_border',
				'text_color',
				'link_color',
				'hover_color',
				'max_width'	) ); 
		genesis_add_option_filter( 'one_zero', $this->settings_field,
			array( 'load_css' ) );
	}

	/**
	 * Load the necessary scripts for this admin page.
	 *
	 * @since 0.2
	 *
	 */
	function scripts() {		
		/** Load parent scripts as well as Genesis admin scripts */
		parent::scripts();
		genesis_load_admin_js();		
		/** Register WP Color picker style if not registered */
		if ( ! wp_style_is( 'wp-color-picker','registered' ) )
			wp_register_style( 'wp-color-picker', GWT_ASSETS_URI . 'css/color-picker.min.css' );
		/** Register WP Color picker script if not registered */
		if ( ! wp_script_is( 'wp-color-picker', 'registered' ) ) {
			wp_register_script( 'iris', GWT_ASSETS_URI . 'js/iris.min.js', array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
			wp_register_script( 'wp-color-picker', GWT_ASSETS_URI . 'js/color-picker.min.js', array( 'jquery', 'iris' ) );
			$colorpicker_l10n = array(
				'clear' 		=> __( 'Clear', 'widgettoggle' ),
				'defaultString' => __( 'Default', 'widgettoggle' ),
				'pick' 			=> __( 'Select Color', 'widgettoggle' )
			);
			wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
		}
		/** Enqueue WP Color pickter style */
		wp_enqueue_style( 'wp-color-picker' );
		/** Enqueue WP Color pickter script */
		wp_enqueue_script( 'iris' );
		wp_enqueue_script( 'wp-color-picker' );		
	}
 
	/**
	 * Register metaboxes Settings page
	 *
	 * @since 0.2
	 */
	function metaboxes() { 		
 		add_meta_box( 'general-settings', __( 'General Settings', 'widgettoggle' ), array( $this, 'general_settings' ), $this->pagehook, 'main', 'high');
	}

	/**
	 * Callback function for widget_settings()
	 *
	 * @since 0.2
	 */
	function general_settings(){
		?>

		<p>
			<label style="width: 180px; margin: 0 40px 0 0; display: inline-block;" for="<?php echo $this->get_field_name( 'max_width' ); ?>"><?php _e( 'Max-width', 'widgettoggle' ); ?></label>
			<input type="number" name="<?php echo $this->get_field_name( 'max_width' );?>" id="<?php echo $this->get_field_id( 'max_width' );?>" min="640" max="1920" value="<?php echo (int)$this->get_field_value( 'max_width' ); ?>" /> px
		</p>

		<p>
			<label style="width: 180px; margin: 0 40px 0 0; display: inline-block;" for="<?php echo $this->get_field_id( 'position' ); ?>"><?php _e( 'Position', 'widgettoggle' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'position' ); ?>" id="<?php echo $this->get_field_id( 'position' ); ?>">
				<option value="absolute"<?php selected( $this->get_field_value( 'position' ), 'absolute' ); ?>><?php _e( 'Absolute', 'widgettoggle' ); ?></option>
				<option value="fixed"<?php selected( $this->get_field_value( 'position' ), 'fixed' ); ?>><?php _e( 'Fixed', 'widgettoggle' ); ?></option>
			</select>
		</p>

		<p>
			<label style="width: 180px; margin: 0 40px 0 0; display: inline-block;" for="<?php echo $this->get_field_id( 'load_css' ); ?>"><?php _e( 'Load basic css?', 'widgettoggle' ); ?></label>
			<input type = "checkbox" name="<?php echo $this->get_field_name( 'load_css' ); ?>" id="<?php echo $this->get_field_id( 'load_css' ); ?>" value="1"<?php checked( $this->get_field_value( 'load_css' ) ); ?> />
		</p>

		<p>
			<label style="width: 180px; margin: 0 40px 0 0; display: inline-block;" for="<?php echo $this->get_field_name( 'toggle_text' ); ?>"><?php _e( 'Toggle Button Text', 'widgettoggle' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'toggle_text' ); ?>" id="<?php echo $this->get_field_id( 'toggle_text' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'toggle_text' ) ); ?>" size="50" />
			<span class="description" style="display:block"><?php echo sprintf( __( 'This field will replace the %s icon', 'widgettoggle' ), '<code>&#43;</code>' );?></span>
		</p>
		<hr class="div">

		<h4><?php _e( 'Color settings', 'widgettoggle' );?></h4>

		<p>
			<label style="width: 180px; margin: 0 40px 20px 0; display: inline-block;" for="<?php echo $this->get_field_name( 'gwt_background' ); ?>"><?php _e( 'Background color', 'widgettoggle' ); ?></label>
			<input data-default-color="#333333" name="<?php echo $this->get_field_name( 'gwt_background' );?>" id="<?php echo $this->get_field_id( 'gwt_background' );?>" class="gwt-color"  type="text" value="<?php echo $this->get_field_value( 'gwt_background' ); ?>" />
		</p>

		<p>
			<label style="width: 180px; margin: 0 40px 20px 0; display: inline-block;" for="<?php echo $this->get_field_name( 'gwt_border' ); ?>"><?php _e( 'Border color', 'widgettoggle' ); ?></label>
			<input data-default-color="#222222" name="<?php echo $this->get_field_name( 'gwt_border' );?>" id="<?php echo $this->get_field_id( 'gwt_border' );?>" class="gwt-color"  type="text" value="<?php echo $this->get_field_value( 'gwt_border' ); ?>" />
		</p>

		<p>
			<label style="width: 180px; margin: 0 40px 20px 0; display: inline-block;" for="<?php echo $this->get_field_name( 'text_color' ); ?>"><?php _e( 'Text color', 'widgettoggle' ); ?></label>
			<input data-default-color="#aaaaaa" name="<?php echo $this->get_field_name( 'text_color' );?>" id="<?php echo $this->get_field_id( 'text_color' );?>" class="gwt-color"  type="text" value="<?php echo $this->get_field_value( 'text_color' ); ?>" />
		</p>

		<p>
			<label style="width: 180px; margin: 0 40px 20px 0; display: inline-block;" for="<?php echo $this->get_field_name( 'link_color' ); ?>"><?php _e( 'Link color', 'widgettoggle' ); ?></label>
			<input data-default-color="#FFE000" name="<?php echo $this->get_field_name( 'link_color' );?>" id="<?php echo $this->get_field_id( 'link_color' );?>" class="gwt-color"  type="text" value="<?php echo $this->get_field_value( 'link_color' ); ?>" />
		</p>

		<p>
			<label style="width: 180px; margin: 0 40px 20px 0; display: inline-block;" for="<?php echo $this->get_field_name( 'hover_color' ); ?>"><?php _e( 'Hover color', 'widgettoggle' ); ?></label>
			<input data-default-color="#ffffff" name="<?php echo $this->get_field_name( 'hover_color' );?>" id="<?php echo $this->get_field_id( 'hover_color' );?>?" class="gwt-color"  type="text" value="<?php echo $this->get_field_value( 'hover_color' ); ?>" />
		</p>

		<hr class="div">

		<p><strong><?php _e( 'Support', 'widgettoggle' );?> : </strong><?php echo sprintf( __( 'Feedback, questions or suggestion just visit %sWordPress plugin support%s page.', 'widgettoggle' ) , '<a target="_blank" href="http://wordpress.org/support/plugin/genesis-widget-toggle">', '</a>' )?></p>

		<script>
			jQuery(document).ready(function($) {
				$( '.gwt-color' ).wpColorPicker();
			});
		</script>

		<?php
	}
 
}