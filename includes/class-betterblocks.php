<?php

/**
 * @link       https://thriveweb.com.au
 * @since      1.0.0
 *
 * @package    BetterBlocks
 * @subpackage BetterBlocks/includes
 */

/**
 *
 * @since      1.0.0
 * @package    BetterBlocks
 * @subpackage BetterBlocks/includes
 * @author     Dean Oakley <dean@thriveweb.com.au>
 */
class BetterBlocks {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      BetterBlocks_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 * 
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'BETTERBLOCKS_VERSION' ) ) {
			$this->version = BETTERBLOCKS_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		$this->plugin_name = 'betterblocks';

		$this->betterblocks_load_dependencies();

		$this->betterblocks_define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function betterblocks_load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-betterblocks-loader.php';

		$this->loader = new BetterBlocks_Loader();

	}

	/**
	 * Register all of the hooks related to the plugin functionality.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function betterblocks_define_admin_hooks() {

		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'betterblocks_enqueue_assets' );

		$this->loader->add_action( 'admin_menu', $this, 'betterblocks_add_menu_page' );

		$this->loader->add_action( 'admin_init', $this, 'betterblocks_register_settings' );

		$this->loader->add_action( 'init', $this, 'betterblocks_remove_directory' );
		
		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'betterblocks_force_preview' );
		
		$this->loader->add_action( 'enqueue_block_editor_assets', $this, 'betterblocks_sidebar_acf' );
		
		$this->loader->add_filter( 'use_block_editor_for_post_type', $this, 'betterblocks_post_types', 10, 2 );

		$this->loader->add_filter( 'render_block', $this, 'betterblocks_block_visibility', 10, 3 );

	}

	/**
	 * Register the stylesheets and JavaScript for the plugin.
	 *
	 * @since    1.0.0
	 */
	public function betterblocks_enqueue_assets() {

		wp_enqueue_script( 'jquery-ui-resizable' );

		wp_enqueue_script( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . '../js/betterblocks-admin.js', 
			array( 'jquery', 'jquery-ui-resizable', 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-compose', 'wp-i18n', 'wp-data' ), 
			$this->version, 
			false 
		);

		wp_enqueue_style( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . '../css/betterblocks-admin.css', 
			array(), 
			$this->version, 
			'all' 
		);

	}

	/**
	 * Create admin page to handle other plugin settings.
	 * 
	 * @since    1.0.0
	 */
	public function betterblocks_add_menu_page() {

		add_submenu_page(
			'tools.php',
			esc_html__( 'BetterBlocks Settings', 'betterblocks' ),
			esc_html__( 'BetterBlocks', 'betterblocks'),
			'manage_options',
			'betterblocks-settings',
			array( $this, 'betterblocks_settings_page' ),
		);

	}

	/**
	 * Callback function to render form for all plugin settings.
	 * 
	 * @since    1.0.0
	 */
	function betterblocks_settings_page() {

		if ( !current_user_can( 'manage_options' ) ) {
			wp_die(esc_html__( 'You do not have sufficient permissions to access this page.', 'betterblocks' ) );
		} 
		
		?>
		
		<div class="wrap">

			<h1><?php echo esc_html__( 'BetterBlocks Settings', 'betterblocks' ); ?></h1><br>

			<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
				<?php 
				settings_fields( 'betterblocks_settings_group' );
				do_settings_sections( 'betterblocks-settings' ); ?>

				<?php
				$betterblocks_registered_post_types = get_post_types( array( 'public' => true ), 'objects' );
				$betterblocks_post_types = array_map( 'sanitize_key', (array) get_option( 'betterblocks_post_types', array() ) ); ?>
				<div class="postbox" style="padding: 20px; margin-bottom: 20px;">
					<h3 style="margin-top: 0;"><?php echo esc_html__( 'Post Type Compatibility', 'betterblocks' ); ?></h3>
					<p><?php echo esc_html__( 'Disable the block editor for specific post types. This is useful for content that may be better suited for the classic editor:', 'betterblocks' ); ?></p>
					<?php
					foreach ( $betterblocks_registered_post_types as $post_type ) {
						if ( $post_type->name !== 'attachment' ) { // Exclude 'attachment' (Media)
							?>
							<label style="display: block; margin-bottom: 10px;">
								<input type="checkbox" name="betterblocks_post_types[]" value="<?php echo esc_attr( $post_type->name ); ?>" <?php checked( in_array( $post_type->name, $betterblocks_post_types ) ); ?> />
								<?php echo esc_html( $post_type->label ); ?>
							</label>
							<?php
						}
					} 
					?>
				</div>

				<?php 
				$betterblocks_remove_directory = boolval( get_option( 'betterblocks_remove_directory', 1 ) ); ?>
				<div class="postbox" style="padding: 20px; margin-bottom: 20px;">
					<h3 style="margin-top: 0;"><?php echo esc_html__( 'Block Directory Visibility', 'betterblocks' ); ?></h3>
					<p><?php echo esc_html__( 'Show or hide the block directory in the block editor sidebar which promotes additional blocks available for installation:', 'betterblocks' ); ?></p>
					<label for="betterblocks_remove_directory">
						<input type="checkbox" id="betterblocks_remove_directory" name="betterblocks_remove_directory" value="1" <?php checked( $betterblocks_remove_directory, 1 ); ?> />
						<?php echo esc_html__( 'Hide the block directory in the block editor?', 'betterblocks' ); ?>
					</label>
				</div>

				<?php 
				$betterblocks_force_preview = boolval( get_option( 'betterblocks_force_preview', 1 ) ); ?>
				<div class="postbox" style="padding: 20px; margin-bottom: 20px;">
					<h3 style="margin-top: 0;"><?php echo esc_html__( 'ACF Block Preview Mode', 'betterblocks' ); ?></h3>
					<p><?php echo esc_html__( 'Force ACF (Advanced Custom Fields) blocks to load in preview mode by default, making it easier to see actual content layouts in the editor:', 'betterblocks' ); ?></p>

					<label for="betterblocks_force_preview">
						<input type="checkbox" id="betterblocks_force_preview" name="betterblocks_force_preview" value="1" <?php checked( $betterblocks_force_preview, 1 ); ?> />
						<?php echo esc_html__( 'Force preview mode for ACF blocks?', 'betterblocks' ); ?>
					</label>
				</div>

				<?php 
				$betterblocks_sidebar_acf = boolval( get_option( 'betterblocks_sidebar_acf', 1 ) ); ?>
				<div class="postbox" style="padding: 20px; margin-bottom: 20px;">
					<h3 style="margin-top: 0;"><?php echo esc_html__( 'ACF Fields in Sidebar', 'betterblocks' ); ?></h3>
					<p><?php echo esc_html__( 'Allow ACF (Advanced Custom Fields) fields to appear in the sidebar when an ACF block is selected:', 'betterblocks' ); ?></p>

					<label for="betterblocks_sidebar_acf">
						<input type="checkbox" id="betterblocks_sidebar_acf" name="betterblocks_sidebar_acf" value="1" <?php checked( $betterblocks_sidebar_acf, 1 ); ?> />
						<?php echo esc_html__( 'Show ACF fields in the sidebar?', 'betterblocks' ); ?>
					</label>
				</div>

				<?php submit_button(); ?>

			</form>

		</div>

	<?php

	}

	/**
	 * Set default form values for plugin activation.
	 *
	 * @since    1.0.0
	 * @static
	 */
	public static function activate() {
		if (get_option('betterblocks_post_types') === false) {
			add_option('betterblocks_post_types', array());
		}
		
		if (get_option('betterblocks_remove_directory') === false) {
				add_option('betterblocks_remove_directory', 1);
		}
		
		if (get_option('betterblocks_force_preview') === false) {
			add_option('betterblocks_force_preview', 1);
		}
		
		if (get_option('betterblocks_sidebar_acf') === false) {
			add_option('betterblocks_sidebar_acf', 1);
		}
	}

	/**
	 * Register and save form values within plugin settings page.
	 * 
	 * @since    1.0.0
	 */
	public function betterblocks_register_settings() {

		register_setting( 'betterblocks_settings_group', 'betterblocks_post_types', 'betterblocks_sanitize_post_types' );
    register_setting( 'betterblocks_settings_group', 'betterblocks_remove_directory', 'absint' );
    register_setting( 'betterblocks_settings_group', 'betterblocks_force_preview', 'absint' );
    register_setting( 'betterblocks_settings_group', 'betterblocks_sidebar_acf', 'absint' );

	}

	/**
	 * Sanitize callback for post_type_support setting.
	 * 
	 * @since    1.0.0
	 */

	public function betterblocks_sanitize_post_types( $input ) {

    if ( !is_array( $input ) ) {
			return array();
    }
		
    return array_map( 'sanitize_key', $input );
		
	}

	/**
	 * Callback function for post_type_support plugin setting.
	 * 
	 * @since    1.0.0
	 */
	public function betterblocks_post_types( $current_status, $post_type ) {

		$betterblocks_post_types = get_option( 'betterblocks_post_types', array() );

		if ( !is_array( get_option( 'betterblocks_post_types', array() ) ) ) {
			$betterblocks_post_types = array();
		}
		
		return in_array( $post_type, $betterblocks_post_types ) ? 0 : $current_status;

	}

	/**
	 * Callback function for betterblocks_remove_directory plugin setting.
	 * 
	 * @since    1.0.0
	 */
	public function betterblocks_remove_directory() {

		if ( get_option( 'betterblocks_remove_directory', 0 ) ) {
			remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets' );
		}

	}

	/**
	 * Callback function for betterblocks_force_preview plugin setting.
	 * 
	 * @since    1.0.0
	 */
	public function betterblocks_force_preview() {

		if ( !get_option( 'betterblocks_force_preview', 0 ) ) {
			return;
		}

		wp_enqueue_script(
			'betterblocks-force-preview',
			plugin_dir_url(__FILE__) . '../js/betterblocks-force-preview.js',
			array( 'jquery', 'wp-dom-ready', 'wp-data', 'wp-blocks', 'wp-element' ),
			$this->version,
			true
		);

	}

	/**
	 * Callback function for betterblocks_sidebar_acf plugin setting.
	 * 
	 * @since    1.0.0
	 */
	public function betterblocks_sidebar_acf() {

		if ( !get_option( 'betterblocks_sidebar_acf', 0 ) ) {
			wp_add_inline_style( 'wp-block-editor', '.block-editor .acf-block-panel { display: none !important; }' );
		}

	}

	/**
	 * Conditionally return block content based on visibility status.
	 * 
	 * @since    1.0.0
	 */
	public function betterblocks_block_visibility( $block_content, $block ) {

		return !empty( $block['attrs']['betterblocks_disable_frontend_block'] ) ? '' : $block_content;

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within WordPress.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    BetterBlocks_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
