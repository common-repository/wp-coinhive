<?php
/*
Plugin Name: Coinhive WordPress
Description: Coinhive is a JS tool to have the visitors of your site mine Monero in the background. This is a WordPress plugin to get it on your site easily.
Version: 1.1.1
Author: web3devs
Author URI: http://web3devs.com
License: GPLv2 or later
*/

function coinhive_wordpress_add_javascript() {
	$value = get_option('coinhive_sitekey');
	$threads = get_option('coinhive_threads');
	$throttle = get_option('coinhive_throttle');
	
	if ($threads) {
		$options = ",{ threads:".$threads.", throttle: ".$throttle." }";
	} else {
		$options = "";
	}
	
	echo "<button id='miningbutton' onClick='startminer();'>Start Mining</button>";
	
	wp_enqueue_script('coinhive-script','https://coin-hive.com/lib/coinhive.min.js',array());
	wp_add_inline_script('coinhive-script','var miner = new CoinHive.Anonymous("'.esc_textarea($value).'"'.esc_textarea($options).');','after');
	wp_add_inline_script('coinhive-script','function startminer() { miner.start();console.log(miner); }');

}

add_shortcode( 'add_miner', 'coinhive_wordpress_add_javascript' );


function coinhive_settings_display() {
	
	if (isset($_POST['coinhive_sitekey']) && (strlen($_POST['coinhive_sitekey']) == 32)) {
		check_admin_referer( 'coinhive-sitekey' );
        update_option('coinhive_sitekey', sanitize_text_field($_POST['coinhive_sitekey']));
    }
    if (isset($_POST['coinhive_threads']) && is_numeric($_POST['coinhive_threads'])) {
        update_option('coinhive_threads', sanitize_text_field($_POST['coinhive_threads']));
    }
    if (isset($_POST['coinhive_throttle']) && is_numeric($_POST['coinhive_throttle'])) {
        update_option('coinhive_throttle', sanitize_text_field($_POST['coinhive_throttle']));
    }

    $value = esc_textarea(get_option('coinhive_sitekey'));
    $threads = esc_textarea(get_option('coinhive_threads'));
	
    echo '<h1>Coinhive Settings</h1>';
    echo '<form method="POST">';
    wp_nonce_field( 'coinhive-sitekey' );
    echo '<label>Site Key</label><input type="text" name="coinhive_sitekey" value="'.$value.'" />';
    echo '<br /><label>Number of Threads (leave blank for default settings)</label><input type="text" name="coinhive_threads" value="'.$threads.'" />';
    echo '<br /><label>Throttle (leave blank for default settings)</label><input type="text" name="coinhive_throttle" value="'.$throttle.'" />';
    echo '<br /><input type="submit" value="Save" class="button button-primary button-large">';
    echo '</form>';
}

function coinhive_settings_create() {
    add_menu_page( 'Coinhive Settings', 'Coinhive Settings', 'manage_options', 'coinhive_settings', 'coinhive_settings_display', '');
}
add_action('admin_menu', 'coinhive_settings_create');