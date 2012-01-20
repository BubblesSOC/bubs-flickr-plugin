<?php
/*
Plugin Name: Bubs' Flickr Plugin
Plugin URI: http://bubblessoc.net/
Description: Fetches your recent Flickr photos via the Flickr REST API and cURL.
Version: 0.1
Author: Bubs
Author URI: http://bubblessoc.net/

	Needs Inline Documentation: http://codex.wordpress.org/Inline_Documentation
*/

class BFPClass {
	
	var $cache_array;
	var $source;
	var $error;
	
	function BFPClass() {
		$this->cache_array = array();
		$this->error = "";
	}
	
	function bfp_activate() {
		
		// Make sure bfp_cache, bfp_last_checked are in the database
		// add_option() does nothing if option already exists
		add_option('bfp_cache', $this->cache_array);
		add_option('bfp_last_checked', time());
		
		// The following options should remain in database for convenience when updating Wordpress
		// Let's make sure they are in the database.  If not, add to database.
		
		// get_option() NOTE: If the desired option does not exist, or no value is associated with it, FALSE will be returned.
		
		if ( !get_option('bfp_username') ) {
			add_option('bfp_username', '');
		}
		
		
		if ( !get_option('bfp_nsid') ) {
			add_option('bfp_nsid', '');
		}
		
		
		if ( !get_option('bfp_photo_count') ) {
			add_option('bfp_photo_count', '');
		}
		
		
		if ( !get_option('bfp_before_list') ) {
			add_option('bfp_before_list', '');
		}
		
		
		if ( !get_option('bfp_photo_style') ) {
			add_option('bfp_photo_style', '');
		}
		
		
		if ( !get_option('bfp_after_list') ) {
			add_option('bfp_after_list', '');
		}
	}
	
	function bfp_deactivate() {
		
		// Remove bfp_cache, bfp_last_checked from the database
		delete_option('bfp_cache');
		delete_option('bfp_last_checked');
	}
	
	function bfp_admin_menu() {
		add_options_page('Bubs\' Flickr Plugin Settings', 'Bubs\' Flickr Plugin', 'administrator', basename(__FILE__), array($this, 'bfp_options_page'));
	}
	
	function bfp_options_page() {
		// http://codex.wordpress.org/Creating_Options_Pages
		include('bfp-settings.php');
	}
	
	function bfp_register_settings() {
		
		// The options allowed to be updated via the Settings page
		register_setting( 'bfp_options-group', 'bfp_username' );
		register_setting( 'bfp_options-group', 'bfp_nsid' );
		register_setting( 'bfp_options-group', 'bfp_photo_count' );
		register_setting( 'bfp_options-group', 'bfp_before_list' );
		register_setting( 'bfp_options-group', 'bfp_photo_style' );
		register_setting( 'bfp_options-group', 'bfp_after_list' );
	}
	
	function bfp_fetch_photos() {
		
		$time_diff = time() - get_option('bfp_last_checked');
		
		// If 60 seconds has passed since last cache or the cache is empty, fetch from Flickr
		if ($time_diff > 60 || !get_option('bfp_cache')) {
			
			// Validate bfp_photo_count
			$photo_count = get_option('bfp_photo_count');
			
			if ( !is_numeric($photo_count) || $photo_count < 1 || $photo_count > 500 ) {
				$photo_count = 3;
			}
			
			// Validate bfp_username
			$username = get_option('bfp_username');
			
			if ( empty($username) ) {
				$username = get_option('bfp_nsid');
			}
			
			// Fetch Flickr Photos
			$url = "http://api.flickr.com/services/rest/?method=flickr.people.getPublicPhotos&api_key=d7ca2582f96a038159e2c7c48e894210&user_id=" . get_option('bfp_nsid') . "&extras=date_upload,date_taken,url_sq,url_t,url_s,url_m,url_o&per_page=" . $photo_count;
			$session = curl_init( $url );
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($session, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($session, CURLOPT_FAILONERROR, true);
			$result = curl_exec($session);
			$curl_error = curl_error($session);
			curl_close($session);
			
			if ($result) {
				$xml = simplexml_load_string($result);
				
				if ($xml) {
					
					// Check status of response - ok or fail
					if ($xml['stat'] == "ok") {
						
						foreach ($xml->photos->photo as $photo) {
						
							// Don't cache if isn't public
							//if ( (string)$photo['ispublic'] != "1" ) continue;
							
							$photo_cache = array();
							$photo_cache['title'] = htmlentities( (string)$photo['title'], ENT_QUOTES, get_option('blog_charset') );
							$photo_cache['dateupload'] = (string)$photo['dateupload'];
							$photo_cache['datetaken'] = (string)$photo['datetaken'];
							$photo_cache['url_sq'] = (string)$photo['url_sq'];
							
							$photo_cache['url_t'] = (string)$photo['url_t'];
							$photo_cache['height_t'] = (string)$photo['height_t'];
							$photo_cache['width_t'] = (string)$photo['width_t'];
							
							$photo_cache['url_s'] = (string)$photo['url_s'];
							$photo_cache['height_s'] = (string)$photo['height_s'];
							$photo_cache['width_s'] = (string)$photo['width_s'];
							
							$photo_cache['url_m'] = (string)$photo['url_m'];
							$photo_cache['height_m'] = (string)$photo['height_m'];
							$photo_cache['width_m'] = (string)$photo['width_m'];
							
							$photo_cache['url_o'] = (string)$photo['url_o'];
							$photo_cache['height_o'] = (string)$photo['height_o'];
							$photo_cache['width_o'] = (string)$photo['width_o'];
	
							$photo_cache['url'] = "http://www.flickr.com/photos/" . $username . "/" . (string)$photo['id'] . "/";
							
							array_push($this->cache_array, $photo_cache);
						}
						
						update_option('bfp_cache', $this->cache_array);
						update_option('bfp_last_checked', time());
						$this->source = "Flickr";
					}
					else {
						// Flickr REST Error
						$this->error = "Flickr REST error: " . $xml->err['msg'];
					}
				}
				else {
					// SimpleXML Error
					$this->error = "Failed loading XML";
				}
			}
			else {
				// cURL error
				$this->error = "cURL error: " . $curl_error;
			}
			
			// If there was an error, try using cache
			if ( $this->error != "" ) {
				
				if ( get_option('bfp_cache') ) {
					$this->cache_array = get_option('bfp_cache');
					$this->source = "Cache (Error)";
				}
				else {
					$this->source = "Nowhere (Error)";
				}
			}
		}
		else {
			// Fetch from Cache
			$this->cache_array = get_option('bfp_cache');
			$this->source = "Cache";
		}
	}
	
	function bfp_print_photos() {
		
		// Fetch Recent Photos
		$this->bfp_fetch_photos();
		
		// Debugging
		echo "<!-- Fetching from " . $this->source . " -->\n";
		
		if ( $this->error != "" ) {
			echo "<!-- " . $this->error . " -->\n";
		}
		
		if ( !empty($this->cache_array) ) {
			
			echo get_option('bfp_before_list') . "\n";
			
			foreach ($this->cache_array as $item) {
				
				$photo_style = get_option('bfp_photo_style');
				$photo_style = str_replace( '%photo_url%', $item['url'], $photo_style );
				$photo_style = str_replace( '%photo_title%', $item['title'], $photo_style );
				$photo_style = str_replace( '%img_sq%', $item['url_sq'], $photo_style );
				
				$photo_style = str_replace( '%img_t%', $item['url_t'], $photo_style );
				$photo_style = str_replace( '%width_t%', $item['width_t'], $photo_style );
				$photo_style = str_replace( '%height_t%', $item['height_t'], $photo_style );
				
				$photo_style = str_replace( '%img_s%', $item['url_s'], $photo_style );
				$photo_style = str_replace( '%width_s%', $item['width_s'], $photo_style );
				$photo_style = str_replace( '%height_s%', $item['height_s'], $photo_style );
				
				$photo_style = str_replace( '%img_m%', $item['url_m'], $photo_style );
				$photo_style = str_replace( '%width_m%', $item['width_m'], $photo_style );
				$photo_style = str_replace( '%height_m%', $item['height_m'], $photo_style );
				
				$photo_style = str_replace( '%img_o%', $item['url_o'], $photo_style );
				$photo_style = str_replace( '%width_o%', $item['width_o'], $photo_style );
				$photo_style = str_replace( '%height_o%', $item['height_o'], $photo_style );
				
				echo $photo_style . "\n";
			}
			
			echo get_option('bfp_after_list') . "\n";
		}
	}
}

$bfpClass = new BFPClass();

// When plugin is activated, call the bfp_activate() method
register_activation_hook('bubs-flickr-plugin/bubs-flickr-plugin.php', array($bfpClass, 'bfp_activate'));

// When plugin is deactivated, call the bfp_deactivate() method
register_deactivation_hook('bubs-flickr-plugin/bubs-flickr-plugin.php', array($bfpClass, 'bfp_deactivate'));

if ( is_admin() ) {
	
	// Add Settings Page
	add_action('admin_menu', array($bfpClass, 'bfp_admin_menu'));
	
	// Whitelist Options for Settings Page
	add_action('admin_init', array($bfpClass, 'bfp_register_settings'));
}

// Display your Recent Flickr Photos
function bfp_recent_flickr_photos() {
	global $bfpClass;
	
	$bfpClass->bfp_print_photos();
}
?>
