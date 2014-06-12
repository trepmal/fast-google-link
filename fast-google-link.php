<?php
/**
 * Plugin Name: Fast Google Link
 * Description: Get first google result for selected text and insert link
 * Plugin URI: http://trepmal.com/
 * Version: 1.0
 * Author: Kailey Lampert
 * Author URI: http://kaileylampert.com/
 */

add_action( 'init', 'mce_wrapper_init', 9 );
function mce_wrapper_init() {
	if ( ! is_admin() ) return;
	new Fast_Google_Link();
}

class Fast_Google_Link {

	var $pluginname = 'FGL';
	var $internalVersion = 100;

	/**
	 * ::__construct()
	 * the constructor
	 *
	 * @return void
	 */
	function __construct()  {

		add_filter('tiny_mce_version',       array( $this, 'change_tinymce_version') );

		add_action('init',                   array( $this, 'addbuttons') );

		add_action('wp_ajax_fgl_fetch_link', array( $this, 'fetch_link') );

	}

	function fetch_link() {
		$data = $_GET['selection'];

		//Get the headers
		$response = wp_remote_head( 'http://www.google.com/search?hl=en&q='. urlencode( $data ) .'&btnI=1' );
		$headers  = wp_remote_retrieve_headers( $response );

		if ( isset( $headers['location'] ) ) {
			wp_send_json_success( $headers['location'] );
		} else {
			wp_send_json_error();
		}

	}

	/**
	 * ::addbuttons()
	 *
	 * @return void
	 */
	function addbuttons() {

		// Don't bother doing this stuff if the current user lacks permissions
		if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') )
			return;

		// Add only in Rich Editor mode
		if ( get_user_option('rich_editing') == 'true') {

			// add the button for wp2.5 in a new way
			add_filter('mce_external_plugins', array( $this, 'add_tinymce_plugin' ) );
			add_filter('mce_buttons', array( $this, 'register_button' ), 0 );
		}
	}

	/**
	 * ::register_button()
	 * used to insert button in wordpress 2.5x editor
	 *
	 * @return $buttons
	 */
	function register_button( $buttons ) {

		array_push( $buttons, 'separator', $this->pluginname );

		return $buttons;
	}

	/**
	 * ::add_tinymce_plugin()
	 * Load the TinyMCE plugin : editor_plugin.js
	 *
	 * @return $plugin_array
	 */
	function add_tinymce_plugin( $plugin_array ) {

		$plugin_array[ $this->pluginname ] =  plugins_url( 'editor_plugin.js', __FILE__ );

		return $plugin_array;
	}

	/**
	 * ::change_tinymce_version()
	 * A different version will rebuild the cache
	 *
	 * @return $version
	 */
	function change_tinymce_version( $version ) {
		$version = $version + $this->internalVersion;
		return $version;
	}

}
