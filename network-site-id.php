<?php
/**
 * Plugin Name: Network site ID
 *
 * Author:      Guido Scialfa
 * Author URI:  http://www.guidoscialfa.com
 * Version:     1.0
 * License:     Gpl
 * Description: Show the blogs ID on admin bar in network installation
 * GitHub Plugin URI: https://github.com/widoz/network-site-id
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

// Prevent Direct Access
if( ! defined( 'ABSPATH' ) ) exit;

class SZ_Network_Site_ID {

	/**
	 * Class Instance
	 *
	 * @var object
	 */
	private static $network_site_id = false;

	/**
	 * Create new menu node
	 *
	 * @since 1.0
	 *
	 * @param  object $wp_admin_bar The admin bar object
	 *
	 * @return void
	 */
	public function sz_add_site_id( $wp_admin_bar ) {
		if ( !is_super_admin() || !is_admin_bar_showing() ) {
			return;
		}

		// Get Site ID
		$site_id = get_current_blog_id();
		
		// Add node to admin menu
		$wp_admin_bar->add_menu( array(
			'id'    => 'site-id',
			'title' => __( 'Site ID' ) . ' ' . $site_id,
			'meta'  => array(
				'class' => 'sz-network-id',
			),
		) );
	}

	/**
	 * Add Site ID to the menu items
	 *
	 * @since 1.0
	 *
	 * @param  object $wp_admin_bar The WP_Admin_Bar instance
	 *
	 * @return void
	 */
	public function sz_add_site_id_to_item( $wp_admin_bar ) {
		if ( !is_super_admin() || !is_admin_bar_showing() ) {
			return;
		}

		// Need direct access to item object
		foreach ( (array) $wp_admin_bar as $node => $array ) {
			// Get and modify menu title
			foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {
				$menu_id                  = 'blog-' . $blog->userblog_id;                           // Set menu ID
				$title                    = $blog->userblog_id . ' - ' . $array[ $menu_id ]->title; // Get title
				$array[ $menu_id ]->title = $title;                                                 // Change title
			}
			break; // done
		}
	}

	/**
	 * Add Site ID to the sites list table
	 *
	 * @since 1.0
	 *
	 * @param array $sites_columns Associative array with id and columns name
	 *
	 * @return array $sites_columns The filtered array
	 */
	public function sz_wpmu_blogs_columns( $sites_columns ) {
		$tmp = array_splice( $sites_columns, 0, 1 );
		$sites_columns = array_merge( $tmp, array( 'id' => 'ID' ), $sites_columns );
		return $sites_columns;
	}

	/**
	 * Singleton
	 *
	 * @since 1.0
	 *
	 * @return object SZ_Network_Site_ID instance
	 */
	public static function get_instance() {
		if ( ! self::$network_site_id ) {
			self::$network_site_id = new self;
		}

		return self::$network_site_id;
	}

	/**
	 * Construct
	 */
	private function __construct() {
		// Add ID to the Menu Item
		add_action( 'admin_bar_menu',             array( $this, 'sz_add_site_id_to_item' ),           21 );
		// Admin bar menu
		add_action( 'admin_bar_menu',             array( $this, 'sz_add_site_id' ),                  999 );
		// Add ID to the sites list table
		add_action( 'manage_sites_custom_column', array( $this, 'sz_manage_sites_custom_column' ), 10, 2 );

		// Add columns to sites list table
		add_filter( 'wpmu_blogs_columns',         array( $this, 'sz_wpmu_blogs_columns' ),         10, 1 );
	}
}

// Get instance
SZ_Network_Site_ID::get_instance();