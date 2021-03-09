<?php

/**
 * Storefront automatically loads the core CSS even if using a child theme as it is more efficient
 * than @importing it in the child theme style.css file.
 *
 * Uncomment the line below if you'd like to disable the Storefront Core CSS.
 *
 * If you don't plan to dequeue the Storefront Core CSS you can remove the subsequent line and as well
 * as the sf_child_theme_dequeue_style() function declaration.
 */
//add_action( 'wp_enqueue_scripts', 'sf_child_theme_dequeue_style', 999 );

/**
 * Dequeue the Storefront Parent theme core CSS
 */
function sf_child_theme_dequeue_style() {
    wp_dequeue_style( 'storefront-style' );
    wp_dequeue_style( 'storefront-woocommerce-style' );
}
/**
 * Note: DO NOT! alter or remove the code above this text and only add your custom PHP functions below this text.
 */

/** Require Files */
require 'home_api.php';
require 'registration.php';
require 'login.php';
require 'change_password.php';
require 'forgot_password.php';
require 'related_products.php';
require 'search.php';
require 'price_filter.php';


/** Convert Symbol */
add_filter('woocommerce_currency_symbol','kwd_change_symbol',1,2);
function kwd_change_symbol($currency_symbol, $currency) {
    if($currency == 'KWD')
        $currency_symbol = 'KD';
    return $currency_symbol;
}

//function get_product_price(){
//	global $wpdb;
//
//	$args       = wc()->query->get_main_query();
//
//	$tax_query  = isset( $args->tax_query->queries ) ? $args->tax_query->queries : array();
//	$meta_query = isset( $args->query_vars['meta_query'] ) ? $args->query_vars['meta_query'] : array();
//
//	foreach ( $meta_query + $tax_query as $key => $query ) {
//		if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
//			unset( $meta_query[ $key ] );
//		}
//	}
//
//	$meta_query = new \WP_Meta_Query( $meta_query );
//	$tax_query  = new \WP_Tax_Query( $tax_query );
//
//	$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
//	$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );
//
//	$sql  = "SELECT min( FLOOR( price_meta.meta_value ) ) as min_price, max( CEILING( price_meta.meta_value ) ) as max_price FROM {$wpdb->posts} ";
//	$sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " . $tax_query_sql['join'] . $meta_query_sql['join'];
//	$sql .= "  WHERE {$wpdb->posts}.post_type IN ('product')
//      AND {$wpdb->posts}.post_status = 'publish'
//      AND price_meta.meta_key IN ('_price')
//      AND price_meta.meta_value > '' ";
//	$sql .= $tax_query_sql['where'] . $meta_query_sql['where'];
//
//	$search = \WC_Query::get_main_search_query_sql();
//	if ( $search ) {
//		$sql .= ' AND ' . $search;
//	}
//
//	$prices = $wpdb->get_row( $sql ); // WPCS: unprepared SQL ok.
//
//	return [
//		'min' => floor( $prices->min_price ),
//		'max' => ceil( $prices->max_price )
//	];
//}