<?php
/**
 * Plugin Name: Arabic-Indic Numerals for Arabic Wordpress
 * Plugin URI: https://github.com/jvarn/arabic-indic-numerals-for-arabic-wp
 * Description: Converts numbers in dates into Arabic-Indic numerals
 * Version: 1.0.3
 * Author: Jeremy Varnham
 * Author URI: https://abuyasmeen.com/
 * License: GPL2
 * Text Domain: arabic-indic-numerals-for-arabic-wp
 * Domain Path: /languages
 *
 * For the sake of simplicity let's just say English numbers and Arabic numbers.
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

/**
 * Replaces English numbers with Arabic numbers in given string.
 *
 * @since 0.15
 *
 * @param string	$string A string containing English numbers
 * @return string	A string containing Arabic numbers
 */
function convert_numbers_to_arabic( $string ) {
	$arabic_numbers = array('۰', '۱', '۲', '۳', '٤', '۵', '٦', '۷', '۸', '۹', '.', '،');
	$english_numbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.', ',');
	return str_replace($english_numbers, $arabic_numbers, $string);;
}

/**
 * If the blog language is Arabic, hook into date and time functions
 * and convert the numbers to Arabic.
 *
 * @since 0.15
 *
 * @param string	$the_date the date as returned by get_the_time or get_the_date
 * @return string	The date with Arabic numbers
 */
function make_arabic_date( $the_date, $force=null ) {
	if ( get_bloginfo( 'language' ) == 'ar' || $force ) {
		$the_date = convert_numbers_to_arabic( $the_date );
	}
	return $the_date;
}
if( !is_admin() ){
	add_filter( 'get_the_time', 'make_arabic_date' );
	add_filter( 'get_the_date', 'make_arabic_date' );
}

/**
 * Shortcode for inserting a the Arabic date of the current post.
 * [arabic_date]
 *
 * @since 1.0.0
 *
 * @param array $atts Shortcode attributes
 * @return string The post date in Arabic
 */
function jlv_arabic_date_shortcode( $atts ) {
	$the_date = get_the_date();
	return make_arabic_date( $the_date, true );
}
add_shortcode( 'arabic_date', 'jlv_arabic_date_shortcode' );

/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function jlv_arabic_indic_numerals_load_textdomain() {
  load_plugin_textdomain( 'arabic-indic-numerals-for-arabic-wp', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

add_action( 'init', 'jlv_arabic_indic_numerals_load_textdomain' );
?>