<?php
/**
 * Plugin Name: Network site ID
 *
 * Author:      Guido Scialfa
 * Author URL:  http://www.guidoscialfa.com
 * Version:     1.0
 * License:     Gpl2
 * Description: Show the sites ID on network installation
 *
 *    Copyright (C) 2013  Guido Scialfa <dev@guidoscialfa.com>
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

if( ! class_exists( 'SZ_Network_Site_ID' ) ) :
class SZ_Network_Site_ID
{
	/**
	 * Class Instance
	 *
	 * @var object
	 */
	private static $network_site_id = false;

	/**
	 * Create new menu node
	 *
	 * @param object $wp_admin_bar The admin bar object
	 */
	public function sz_add_site_id( $wp_admin_bar )
	{
		if( !is_super_admin() || !is_admin_bar_showing() )
			return;

		// Get Site ID
		$site_id = get_current_blog_id();
		
		// Add node to admin menu
		$wp_admin_bar->add_menu( array(
			'id'    => 'site-id',
			'title' => __( 'Site ID:' ) . $site_id,
			'meta'  => array(
				'class' => 'sz-network-id',
			),
		) );
	}

	/**
	 * Singleton
	 */
	public static function get_instance()
	{
		if( ! self::$network_site_id )
		{
			self::$network_site_id = new self;
		}

		return self::$network_site_id;
	}

	/**
	 * Construct
	 */
	private function __construct()
	{
		// Admin bar menu
		add_action( 'admin_bar_menu', array( $this, 'sz_add_site_id' ), 999 );
	}
}
endif;

// Get instance
SZ_Network_Site_ID::get_instance();