<?php

/**
 * Plugin Name: Conditional Blocks
 * Author URI: https://conditionalblocks.com/
 * Description:  Create personalized content by using conditions on all WordPress blocks.
 * Author: Conditional Blocks
 * Version: 3.2.1
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: conditional-blocks
 *
 * Requires at least:   5.5
 * Requires PHP:        7.4
 * 
 * @package conditional_blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This constant name is the same the free & pro version, as only one can be active at a time.
 */
if ( ! defined( 'CONDITIONAL_BLOCKS_PATH' ) ) {
	define( 'CONDITIONAL_BLOCKS_PATH', __FILE__ );
}

/**
 * This constant name is the same the free & pro version, as only one can be active at a time.
 *
 * Note version could be a string such as x.x.x-beta2.
 */
if ( ! defined( 'CONDITIONAL_BLOCKS_VERSION' ) ) {
	define( 'CONDITIONAL_BLOCKS_VERSION', '3.2.1' );
}

/**
 * int the plugin.
 *
 * @DEVS: Don't rely on these for integrations as they may change, use the constants instead or refer to docs.
 */
class CONBLOCK_Init {
	/**
	 * Access all plugin constants
	 *
	 * @var array
	 */
	public $constants;

	/**
	 * Access notices class.
	 *
	 * @var class
	 */
	private $notices;

	/**
	 * Plugin init.
	 */
	public function __construct() {

		$this->constants = array(
			'name' => 'Conditional Blocks',
			'version' => '3.2.1',
			'slug' => plugin_basename( __FILE__, ' . php' ),
			'base' => plugin_basename( __FILE__ ),
			'name_sanitized' => basename( __FILE__, '. php' ),
			'path' => plugin_dir_path( __FILE__ ),
			'url' => plugin_dir_url( __FILE__ ),
			'file' => __FILE__,
		);

		// include Notices.
		include_once plugin_dir_path( __FILE__ ) . 'classes/class-admin-notices.php';
		// Set notices to class.
		$this->notices = new conblock_admin_notices();
		// Activation.
		register_activation_hook( __FILE__, array( $this, 'activation' ) );
		// Load text domain.
		add_action( 'init', array( $this, 'load_textdomain' ) );
		// Load plugin when all plugins are loaded.
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Load plugin textdomain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'conditional-blocks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}


	/**
	 * Plugin init.
	 */
	public function init() {

		
		require_once plugin_dir_path( __FILE__ ) . 'functions/functions.php';
				require_once plugin_dir_path( __FILE__ ) . 'functions/languages.php';
		require_once plugin_dir_path( __FILE__ ) . 'classes/class-register.php';
		require_once plugin_dir_path( __FILE__ ) . 'classes/class-rest.php';
		require_once plugin_dir_path( __FILE__ ) . 'classes/class-render.php';
		require_once plugin_dir_path( __FILE__ ) . 'classes/class-enqueue.php';
		require_once plugin_dir_path( __FILE__ ) . 'integrations/easy-digital-downloads.php';
		require_once plugin_dir_path( __FILE__ ) . 'integrations/advanced-custom-fields.php';
		require_once plugin_dir_path( __FILE__ ) . 'integrations/paid-memberships-pro.php';
		require_once plugin_dir_path( __FILE__ ) . 'integrations/meta-box.php';
		require_once plugin_dir_path( __FILE__ ) . 'integrations/gtranslate.php';
	}

	public function activation() {
				$text = __(
			'Thank you for installing Conditional Blocks! You can now start adding conditions to your blocks on posts & pages.',
			'conditional-blocks'
		) . ' <a class="button button-secondary" target="_blank" href="' . esc_url( 'https://conditionalblocks.com/docs/?utm_source=conditional-blocks-free&utm_medium=referral&utm_campaign=activation-notice' ) . '">' . __( 'Learn more', 'conditional-blocks' ) . '</a>';
		$this->notices->add_notice(
			'success',
			$text
		);
		
			}
}

new CONBLOCK_Init();


