<?php
/**
 * Plugin Name: WooCommerce Featured First then Random Sorting Option
 * Plugin URI: http://squirrel-research.ru/
 * Description: This plugins adds extra product sorting option: Featured First then Random.
 * Author: Vitalii Rizo
 * Author URI: http://squirrel-research.ru
 * Version: 0.9
 * Text Domain: woocommerce-featured-then-random-sorting
 *
 * Copyright: (c) 2016 Vitalii Rizo (kb@kernel-it.ru)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

// Security check:
defined( 'ABSPATH' ) or exit;

// Check if WooCommerce is active
if ( ! wc_featured_than_random_sorting_options::is_woocommerce_active() ) {
  return;
}

// WC version check
if ( version_compare( get_option( 'woocommerce_db_version' ), '2.3.0', '<' ) ) {
  add_action( 'admin_notices', wc_featured_than_random_sorting_options::render_outdated_wc_version_notice() );
  return;
}

// Init plugin after all other plugins loaded:
function init_wc_featured_than_random_sorting_options() {
  wc_featured_than_random_sorting_options();
}
add_action( 'plugins_loaded', 'init_wc_featured_than_random_sorting_options' );


class wc_featured_than_random_sorting_options {
  // Current plugin version:
  const VERSION = '0.9.0';

  // @var wc_featured_than_random_sorting_options single instance of this plugin
  protected static $instance;

  public function __construct() {
    // modify product sorting settings
    add_filter( 'woocommerce_catalog_orderby', array( $this, 'modify_sorting_settings' ) );

    // add new sorting options to orderby dropdown
    add_filter( 'woocommerce_default_catalog_orderby_options', array( $this, 'modify_sorting_settings' ) );

    // add new product sorting arguments
    add_filter( 'woocommerce_get_catalog_ordering_args', array( $this, 'add_new_shop_ordering_args' ) );

    // load translations
    add_action( 'init', array( $this, 'load_translation' ) );

    // Add settings and links in admin panel:
    if ( is_admin() && ! is_ajax() ) {
        // add settings
        add_filter( 'woocommerce_product_settings', array( $this, 'add_settings' ) );

        // add plugin links
        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_links' ) );
    }
  }



  // Ensures only one instance is/can be loaded
  public static function instance() {
    if ( is_null( self::$instance ) ) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  
  /**
   * Singleton pattern: prevent creating more instances by clone and unserialize:
   */
  public function __clone() {
      /* translators: Placeholders: %s - plugin name */
      _doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot clone instances of %s.', 'woocommerce-featured-then-random-sorting' ), 'WooCommerce Extra Product Sorting Options' ), '2.4.0' );
  }

  public function __wakeup() {
      /* translators: Placeholders: %s - plugin name */
      _doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot unserialize instances of %s.', 'woocommerce-featured-then-random-sorting' ), 'WooCommerce Extra Product Sorting Options' ), '2.4.0' );
  }

  // Add plugin links: Settings and Author web site:
  public function add_plugin_links( $links ) {
      $plugin_links = array(
          '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=products&section=display' ) . '">' . __( 'Settings', 'woocommerce-featured-then-random-sorting' ) . '</a>',
          '<a href="http://squirrel-research.ru/" target="_blank">' . __( 'Author', 'woocommerce-featured-then-random-sorting' ) . '</a>',
      );
      return array_merge( $plugin_links, $links );
  }



  /**
   * Load Translations
   */
  public function load_translation() {
      load_plugin_textdomain( 'woocommerce-featured-then-random-sorting', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/languages' );
  }


  /**
   * Checks if WooCommerce is active
   */
  public static function is_woocommerce_active() {
      $active_plugins = (array) get_option( 'active_plugins', array() );

      if ( is_multisite() ) {
          $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
      }

      return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
  }


  /**
   * Renders a notice when WooCommerce version is outdated
   */
  public static function render_outdated_wc_version_notice() {
    $message = sprintf(
      /* translators: %1$s and %2$s are <strong> tags. %3$s and %4$s are <a> tags */
      esc_html__( '%1$sWooCommerce Featured first then Random plugin is inactive.%2$s This plugin requires WooCommerce 2.3 or newer. Please %3$supdate WooCommerce to version 2.3 or newer%4$s', 'woocommerce-featured-then-random-sorting' ),
      '<strong>',
      '</strong>',
      '<a href="' . admin_url( 'plugins.php' ) . '">',
      '&nbsp;&raquo;</a>'
    );

    printf( '<div class="error"><p>%s</p></div>', $message );
  }


  /********************/
  /** Plugin methods **/
  /********************/

  /**
   * Add Settings to WooCommerce Settings > Products page after "Default Product Sorting" setting
   */
  public function add_settings( $settings ) {

    $updated_settings = array();

    foreach ( $settings as $setting ) {

      $updated_settings[] = $setting;

      if ( isset( $setting['id'] ) && 'woocommerce_default_catalog_orderby' === $setting['id'] ) {

        $new_settings = array(
          array(
            'title'    => __( 'Custom Default Sorting Label', 'woocommerce-featured-then-random-sorting' ),
            'id'       => 'wc_custom_default_sorting_name',
            'type'     => 'text',
            'default'  => '',
            'desc_tip' => __( 'You can change Default sorting label to any other, for&nbsp;example, &quot;Our Sorting&quot;', 'woocommerce-featured-then-random-sorting' ),
          ),
          array(
            'title'    => __( 'Custom Featured First then Random Sorting Label', 'woocommerce-featured-then-random-sorting' ),
            'id'       => 'wc_rename_featured_than_random_sorting',
            'type'     => 'text',
            'default'  => '',
            'desc_tip' => __( 'You can change Featured First then Random Sorting label to any other, for&nbsp;example, &quot;Best Sorting Ever&quot;', 'woocommerce-featured-then-random-sorting' ),
          ),
        );

        $updated_settings = array_merge( $updated_settings, $new_settings );

      }
    }

    return $updated_settings;
  }


	/**
	 * Change "Default Sorting" to custom name and add new sorting options.
   * It is added to admin + frontend dropdown.
	 */
	public function modify_sorting_settings( $sortby ) {
    
    // Get custom sorting labels if they are exist:
		$new_default_name = get_option( 'wc_custom_default_sorting_name' );
		$new_featured_than_random_name = get_option( 'wc_rename_featured_than_random_sorting' );

		if ( $new_default_name ) {
			$sortby = str_replace( __("Default sorting", 'woocommerce'), $new_default_name, $sortby );
		}
    
		if ( $new_featured_than_random_name ) {
      $sortby['featured_first_then_random'] = $new_featured_than_random_name;
		} else {
      $sortby['featured_first_then_random'] = __( 'Show featured items first then random', 'woocommerce-featured-then-random-sorting' );
    }

		return $sortby;
	}


	/**
	 * Add sorting option to WC sorting arguments
	 */
	public function add_new_shop_ordering_args( $sort_args ) {

		// If we have the orderby via URL, let's pass it in
		// This means we're on a shop / archive, so if we don't have it, use the default
		if ( isset( $_GET['orderby'] ) ) {
			$orderby_value = wc_clean( $_GET['orderby'] );
		} else {
			$orderby_value = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
		}

		// Since a shortcode can be used on a non-WC page, we won't have $_GET['orderby']
		// Grab it from the passed in sorting args instead for non-WC pages
		// Don't use this on WC pages since it breaks the default
		if ( ! is_woocommerce() && isset( $sort_args['orderby'] ) ) {
			$orderby_value = $sort_args['orderby'];
		}

		$fallback = apply_filters( 'wc_featured_than_random_sorting_options_fallback', 'title', $orderby_value );
		$fallback_order = apply_filters( 'wc_featured_than_random_sorting_options_fallback_order', 'ASC', $orderby_value );

		switch( $orderby_value ) {

			case 'featured_first_then_random':
    
        session_start();

        // Reset seed on load of initial archive page
        if( ! get_query_var( 'paged' ) || get_query_var( 'paged' ) == 0 || get_query_var( 'paged' ) == 1 ) {
            if( isset( $_SESSION['seed'] ) ) {
                unset( $_SESSION['seed'] );
            }
        }

        // Get seed from session variable if it exists
        $seed = false;
        if( isset( $_SESSION['seed'] ) ) {
            $seed = $_SESSION['seed'];
        }

        // Set new seed if none exists
        if ( ! $seed ) {
            $seed = rand();
            $_SESSION['seed'] = $seed;
        }

        // Order by two parameters: meta key and random.
        $sort_args['orderby']  = array( 'meta_value' => 'DESC', 'rand('. $seed .')' => 'ASC', $fallback => $fallback_order );
        $sort_args['meta_key'] = '_featured';
      
      // I'm going to add more sorting options later so that I use switch/case construction
        
			break;

		}

		return $sort_args;
	}
} // class wc_featured_than_random_sorting_options end


/**
 * Returns the One True Instance of WC Featured then Random Sorting
 */
function wc_featured_than_random_sorting_options() {
  return wc_featured_than_random_sorting_options::instance();
}
