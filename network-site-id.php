<?php
/**
 * Plugin Name: Network site ID
 * Plugin URI:  http://github.com/widoz/network-site-id
 * Author:      Guido Scialfa
 * Author URI:  http://www.guidoscialfa.com
 * Version:     2.1.0
 * License:     Gpl2
 * Description: Show the blogs ID on admin bar in network installation
 * Git URI:     http://github.com/widoz/network-site-id
 *
 *    Copyright (C) 2014  Guido Scialfa <dev@guidoscialfa.com>
 *
 *    This program is free software; you can redistribute it and/or
 *    modify it under the terms of the GNU General Public License
 *    as published by the Free Software Foundation; either version 2
 *    of the License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program; if not, write to the Free Software
 *    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Prevent Direct Access.
defined( 'ABSPATH' ) || die;

/**
 * Class SZ_Network_Site_ID
 *
 * @since 1.0.0
 */
class SZ_Network_Site_ID {

	/**
	 * Class Instance
	 *
	 * @since 1.0
	 *
	 * @var object The Singleton
	 */
	private static $_network_site_id = null;

	/**
	 * Nodes
	 *
	 * @since 1.0.1
	 *
	 * @var array The Nodes List
	 */
	private $_nodes;

	/**
	 * Create new menu node
	 *
	 * @since 1.0.1
	 *
	 * @param  object $wp_admin_bar The admin bar object.
	 *
	 * @return void
	 */
	public function sz_add_nodes( $wp_admin_bar ) {

		if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
			return;
		}

		$args    = array();               // Node arguments.
		$site_id = get_current_blog_id(); // Get Site ID.

		if ( count( $this->_nodes ) > 0 ) {
			foreach ( $this->_nodes as $node ) {
				switch ( $node ) {
					case 'site-id' :
						$args = array(
							'id'    => $node,
							'title' => __( 'Site ID' ) . ' ' . $site_id,
							'meta'  => array(
								'class' => 'sz-node',
							),
						);
						break;

					case 'theme-info' :
						$curr_theme = wp_get_theme();
						$args       = array(
							'id'    => $node,
							'title' => '<span class="ab-label">' . __( 'Theme' ) . '</span> ' . $curr_theme->name . ' ' . $curr_theme->version,
							'meta'  => array(
								'class' => 'sz-node',
							),
						);
						break;
				}

				// Add node to admin menu.
				$wp_admin_bar->add_node( $args );
			}
		}
	}

	/**
	 * Add Site ID to the menu items
	 *
	 * @since 1.0
	 *
	 * @param  object $wp_admin_bar The WP_Admin_Bar instance.
	 *
	 * @return void
	 */
	public function sz_add_site_id_to_item( $wp_admin_bar ) {

		if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
			return;
		}

		// Need direct access to item object.
		foreach ( (array) $wp_admin_bar as $node => $array ) {
			// Get and modify menu title.
			foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {
				$menu_id                  = 'blog-' . $blog->userblog_id;                           // Set menu ID.
				$title                    = $blog->userblog_id . ' - ' . $array[ $menu_id ]->title; // Get title.
				$array[ $menu_id ]->title = $title;                                                 // Change title.
			}
			break;
		}
	}

	/**
	 * Add site ID column to the sites list table
	 *
	 * @since 1.0
	 *
	 * @param array $sites_columns Associative array with id and columns name.
	 *
	 * @return array $sites_columns The filtered array
	 */
	public function sz_wpmu_blogs_columns( $sites_columns ) {

		if ( ! is_array( $sites_columns ) ) {
			$sites_columns = (array) $sites_columns;
		}

		$tmp           = array_splice( $sites_columns, 0, 1 );
		$sites_columns = array_merge( $tmp, array( 'id' => 'ID' ), $sites_columns );

		return $sites_columns;
	}

	/**
	 * Show the ID of the blog
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function manage_sites_custom_column( $column, $value ) {

		echo $value;
	}

	/**
	 * Singleton
	 *
	 * @since 1.0
	 *
	 * @return object SZ_Network_Site_ID instance
	 */
	public static function get_instance() {

		if ( null === self::$_network_site_id ) {
			self::$_network_site_id = new self;
		}

		return self::$_network_site_id;
	}

	/**
	 * Admin Enqueue Scripts
	 *
	 * @since 2.1.0
	 */
	public function sz_admin_enqueue_scripts() {

		wp_enqueue_style( 'sz-network-style', plugin_dir_url( __FILE__ ) . '/assets/css/admin/style.css', array(), '1.0.0', 'screen' );
	}

	/**
	 * Construct
	 */
	private function __construct() {

		// Nodes.
		$this->_nodes = array( 'site-id', 'theme-info' );

		// Add ID to the Menu Item.
		add_action( 'admin_bar_menu', array( $this, 'sz_add_site_id_to_item' ), 21 );
		// Admin bar menu.
		add_action( 'admin_bar_menu', array( $this, 'sz_add_nodes' ), 999 );
		// Admin Style.
		add_action( 'admin_enqueue_scripts', array( $this, 'sz_admin_enqueue_scripts' ) );

		// Add ID to the sites list table.
		add_action( 'manage_sites_custom_column', array( $this, 'manage_sites_custom_column' ), 10, 2 );
		// Add columns to sites list table.
		add_filter( 'wpmu_blogs_columns', array( $this, 'sz_wpmu_blogs_columns' ), 10, 1 );
	}
}

// Get instance.
SZ_Network_Site_ID::get_instance();
