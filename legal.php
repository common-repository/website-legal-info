<?php
/*
Plugin Name: Website Legal Info
Plugin URI: http://wordpress.org/extend/plugins/website-legal-info/
Description: Creates a shortcode to automaticaly incorporate legal information in Wordpress-based websites.
Version: 0.1
Author: Lopo Lencastre de Almeida - iPublicis.com
Author URI: http://w3.ipublicis.com/category/wordpress/wpfr
Donate link: http://smsh.me/7kit
License: GNU GPL v3 or later

    Copyright (C) 2010 iPublicis!COM

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*** INSTRUCTIONS **************************************************************

   1. Upload the file to WordPress plugins directory and activate via Plugins panel.
   2. In a new page add [legalinfo] and save.

*********************************************************************************/

/**
 * Load the framework file
 */
require_once( WP_PLUGIN_DIR . '/wordpress-plugin-framework-reloaded/framework-reloaded.php' );

/**
 * Where you put all your class code
 */
class legalMain extends WordpressPluginFrameworkReloaded {
	/**
	 * @var legalMain - Static property to hold our singleton instance
	 */
	static $instance = false;
	
	protected function _init() {

		/**
		 * Definition of global values
		 */
		$this->_hook = 'legal-info';
		$this->_pluginDir = str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		$this->_file = plugin_basename(__FILE__);
		$this->_slug = untrailingslashit( $this->_pluginDir );
		$this->_appLogoFile = plugin_dir_url( __FILE__ ).'/images/ipublicis-logo-32.png';
		$this->_pageTitle = "Website Legal Info";
		$this->_menuTitle = "Legal Info";
		$this->_accessLevel = 'manage_options';
		$this->_optionGroup = 'legal-info-options';
		$this->_optionNames = array('legal-info');
		$this->_appFeed = 'http://w3.ipublicis.com/newswire/ipublicis/feed';
		$this->_donationID = '7kit';
		$this->_wishlistID = 'A7HJYTOILQO5';
		$this->_contactURL = 'http://w3.ipublicis.com/contact-us';
		/*$this->_dashboardWidget = array( 	'inc' => 'iPublicis!COM',
																	'url' => 'http://w3.ipublicis.com/',
																	'rss' => 'http://w3.ipublicis.com/rss.xml', 
																	'ico' => plugin_dir_url( __FILE__ ).'/images/ipublicis-logo-32.png'  );*/
		//$this->_sidebarNews = array(  false, false );

		/**
		 * Add filters and actions
		 */
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		add_filter( $this->_slug .'-opt-legal-info', array( $this, 'filterSettings' ) );
	}

	protected function _postSettingsInit() {
		add_shortcode( $this->_slug, array( $this, 'legalInfoDisplay' ) );
	}

	public function addOptionsMetaBoxes() {
		add_meta_box( $this->_slug . '-settings', __('Settings', $this->_slug), array($this, 'settingsLegalMetaBox'), $this->_slug, 'main');
		add_meta_box( $this->_slug . '-how-to', __('How to use it ', $this->_slug), array($this, 'howToLegalMetaBox'), $this->_slug, 'main');
	}

	public function settingsLegalMetaBox() {
		?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row" colspan="2">
							<strong><?php 
							_e('The following is the required information that will be used in the Legal Information output.', $this->_slug); ?>
							</strong>
						</th>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Website Owner:', $this->_slug); ?></th>
						<td>
							<input id="legal-info_owner" name="legal-info[owner]" type="text" class="regular-text code" size="80" value="<?php echo attribute_escape( $this->_settings['legal-info']['owner'] ); ?>">
							<p class="description"><?php _e("The owner of the site, company or private person.", $this->_slug); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Contact Postal Address:', $this->_slug); ?></th>
						<td>
							<textarea name="legal-info[postal]" id="legal-info_postal"  rows="5" cols="58"><?php 
							echo attribute_escape( $this->_settings['legal-info']['postal'] ); ?></textarea>
							<p class="description"><?php _e("This is required for people to contact you by snail mail.", $this->_slug);?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Branding:', $this->_slug); ?></th>
						<td>
							<input id="legal-info_brand" name="legal-info[brand]" type="text" class="regular-text code" size="80" value="<?php 
								echo attribute_escape( $this->_settings['legal-info']['brand'] ); ?>">
							<p class="description"><?php _e("Your website may use another brand name besides the name below.", $this->_slug);?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('The name of the website:', $this->_slug); ?></th>
						<td>
							<strong><?php echo get_bloginfo( 'name' ); ?></strong>
							<p class="description"><?php _e('This is defined in <a href="/wp-admin/options-general.php">Settings -> General</a>.', $this->_slug);?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Blog address (URL):'); ?></th>
						<td>
							<strong><?php echo get_bloginfo( 'url' ); ?></strong>
							<p class="description"><?php _e('This is defined in <a href="/wp-admin/options-general.php">Settings -> General</a>.', $this->_slug);?></p>
						</td>
					</tr>
				</table>
		<?php
	}

	public function howToLegalMetaBox() {
		?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row" colspan="2">
							<strong><?php 
							_e('Supplies a <em>shortcode</em> to be inserted in pages to show website\'s legal information.', $this->_slug); ?>
							</strong>
						</th>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Required steps:', $this->_slug); ?></th>
						<td>
							<ol>
								<li><?php
									_e('Create a new page, insert <code>['.$this->_slug.']</code> in content area and save it.', $this->_slug); ?>
								</li>
								<li><?php
									_e('Create a link to that page somewhere in you template or use the functions to show page links.', $this->_slug);?>
								</li>
							</ol>
						</td>
					</tr>
				</table>
		<?php
	}

	/**
	 * Function to instantiate our class and make it a singleton
	 */
	public static function getInstance() {
		if ( !self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function activate() {
		$this->registerOptions();
	}

	public function deactivate() {
		$this->_getSettings();
		$this->unregisterOptions();
	}
	
	public function filterSettings($settings) {
		$defaults = array(
			'owner' => __("My Company, LLC", $this->_slug),
			'brand'	=> __("MyBlogBrand", $this->_slug),
			'postal' => __("My Full Contact Address\nSomewhere\n1234 POBOX\nMy Country", $this->_slug),
		);
		$settings = wp_parse_args($settings, $defaults);

		return $settings;
	}
	
	/**
	 * Funtion that outputs the Legal Information
	 */
	public function legalInfoDisplay() {
		$legalName = get_bloginfo( 'name' );
		$legalDomain = get_bloginfo( 'url' );
		$legalOwner = attribute_escape( $this->_settings['legal-info']['owner'] ); 
		$legalAddress = nl2br( attribute_escape( $this->_settings['legal-info']['postal'] ) ); 
		$legalBrand = "";
		if( !empty( $this->_settings['legal-info']['brand'] ) ) {
			$legalBrand = "<em>".attribute_escape( $this->_settings['legal-info']['brand'] )."</em>, ";
		}
		
		$file = WP_PLUGIN_DIR . '/' . $this->_pluginDir . '.legal-info';
		if( file_exists( $file ) ) {
			$legalInfo = file_get_contents( $file );
		
			$replace = array( 	'<LEGALNAME>' 			=> $legalName, 
											'<LEGALDOMAIN>' 		=> $legalDomain, 
											'<LEGALOWNER>' 		=> $legalOwner, 
											'<LEGALADDRESS>' 	=> $legalAddress, 
											'<LEGALBRAND>' 		=> $legalBrand );

			return str_replace( array_keys( $replace ), $replace, $legalInfo );
		} else {
			return __( 'Legal Info not set yet.', $this->_slug );
		}
	}
	
} /* END OF CLASS */

/**
 * Helper functions
 */
include_once('legal-helper.php');

// Instantiate our class
$legalOLPEP = legalMain::getInstance();


?>