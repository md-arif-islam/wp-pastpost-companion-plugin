<?php
/*
Plugin Name: PastPost Companion Plugin
Plugin URI:
Description: Companion Plugin for PastPost Website
Version: 1.6
Author: Arif Islam
Author URI: https://arifislam.techviewing.com
License: GPLv2 or later
Text Domain: pastpost-companion
Domain Path: /languages/
*/

// Load Plugin Textdomain
function pastpostc_load_text_domain() {
	load_plugin_textdomain( 'pastpost-companion', false, dirname( __FILE__ ) . "/languages" );
}

add_action( 'plugins_loaded', 'pastpostc_load_text_domain' );

// Assets
function pastpost_companion_assets(){
	wp_enqueue_style( 'custom-pastpost-css', plugins_url( "/assets/css/style.css", __FILE__ ), null, time() );
}
add_action('wp_enqueue_scripts','pastpost_companion_assets');

// Slider Post type
include( plugin_dir_path( __FILE__ ) . 'post-types/slider.php' );

// Shortcode
// estimate_reading_time_output
function estimate_reading_time() {
	$totalWords = str_word_count( strip_tags( get_the_content() ) );
	$minutes    = floor( $totalWords / 200 );

	echo "<h5 class='read__time'>${minutes} min read</h5>";
}

add_shortcode( 'estimate_reading_time_output', 'estimate_reading_time' );


// Shortcode
// single_post_tags_output
function single_post_tags() {
	$tags = get_the_tags( get_the_ID() );
	foreach ( $tags as $tag ) {
		$tag_link = get_category_link( $tag->term_id );
		echo "<a class='tagged_with' href='${$tag_link}' title='{$tag->name}'><p>{$tag->name}</p></a>";
	}
}

add_shortcode( 'single_post_tags_output', 'single_post_tags' );


// Re-arrange Comments fields
function comment_form_fields_custom_order( $fields ) {

	$comment_field = $fields["comment"];
	$author_field  = $fields["author"];
	$email_field   = $fields["email"];
	$url_field     = $fields["url"];
	$cookies_field = $fields["cookies"];

	unset( $fields["comment"] );
	unset( $fields["author"] );
	unset( $fields["email"] );
	unset( $fields["url"] );
	unset( $fields["cookies"] );

	$fields["author"]  = $author_field;
	$fields["email"]   = $email_field;
	$fields["url"]     = $url_field;
	$fields["cookies"] = $cookies_field;
	$fields["comment"] = $comment_field;

	return $fields;
}

add_filter( "comment_form_fields", "comment_form_fields_custom_order" );


// comment_form_submit_button_text
function comment_form_submit_button_text( $submit_button ) {
	$submit_button = "<button>Send</button>";

	return $submit_button;
}

add_filter( "comment_form_submit_button", "comment_form_submit_button_text" );


// comment_form_change_cookies_consent
function comment_form_change_cookies_consent( $fields ) {
	$commenter         = wp_get_current_commenter();
	$consent           = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';
	$fields['cookies'] = '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . ' />' .
	                     '<label for="wp-comment-cookies-consent">Your modified text here</label></p>';

	return $fields;
}

add_filter( 'comment_form_default_fields', 'comment_form_change_cookies_consent' );


// comment_form_default_fields_markup
function comment_form_default_fields_markup( $fields ) {

	$commenter = wp_get_current_commenter();
	$req       = get_option( 'require_name_email' );
	$aria_req  = ( $req ? " aria-required='true'" : '' );

	$fields['author'] = '<div class="ast-comment-formwrap ast-row"><p class="comment-form-author ' . astra_attr( 'comment-form-grid-class' ) . '">' .
	                    '<label for="author" class="comments_label">' . esc_html( astra_default_strings( 'string-comment-label-name', false ) ) . '</label><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
	                    '" placeholder="" size="30"' . $aria_req . ' /></p>';
	$fields['email']  = '<p class="comment-form-email ' . astra_attr( 'comment-form-grid-class' ) . '">' .
	                    '<label for="email" class="comments_label">' . esc_html( astra_default_strings( 'string-comment-label-email', false ) ) . '</label><input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) .
	                    '" placeholder="" size="30"' . $aria_req . ' /></p>';
	$fields['url']    = '<p class="comment-form-url ' . astra_attr( 'comment-form-grid-class' ) . '"><label for="url">' .
	                    '<label for="url" class="comments_label">' . esc_html( astra_default_strings( 'string-comment-label-website', false ) ) . '</label><input id="url" name="url" type="text" value="' . esc_url( $commenter['comment_author_url'] ) .
	                    '" placeholder="" size="30" /></label></p></div>';

	return apply_filters( 'astra_comment_form_default_fields_markup', $fields );
}


add_filter( 'comment_form_default_fields', 'comment_form_default_fields_markup' );


// comment_form_default_markup
function comment_form_default_markup( $args ) {

	$all_post_type_support = apply_filters( 'astra_comment_form_all_post_type_support', false );
	if ( 'post' == get_post_type() || $all_post_type_support ) {
		$args['id_form']           = 'ast-commentform';
		$args['title_reply']       = astra_default_strings( 'string-comment-title-reply', false );
		$args['cancel_reply_link'] = astra_default_strings( 'string-comment-cancel-reply-link', false );
		$args['label_submit']      = astra_default_strings( 'string-comment-label-submit', false );
		$args['comment_field']     = '<div class="ast-row comment-textarea"><fieldset class="comment-form-comment"><legend class ="comment-form-legend"></legend><div class="comment-form-textarea ' . astra_attr( 'ast-grid-lg-12' ) . '"><label for="comment" class="comments_label">Comment</label><textarea id="comment" name="comment" placeholder="" cols="45" rows="8" aria-required="true"></textarea></div></fieldset></div>';
	}

	return apply_filters( 'astra_comment_form_default_markup', $args );
}

add_filter( 'comment_form_defaults', 'comment_form_default_markup' );

