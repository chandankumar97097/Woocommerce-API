<?php

/** Price Range Filter */
add_action('rest_api_init', function(){
	register_rest_route('wc/v3', 'price-filter', [
		'methods' => 'GET',
		'callback' => 'price_filter',
	]);
});

function price_filter($request = null) {
	$paged = $request->get_param( 'page' );
	$min = $request->get_param( 'min_price' );
	$max = $request->get_param( 'max_price' );
	$order = $request->get_param( 'order' );
	$per_page = $request->get_param( 'per_page' );
	$paged = ( isset( $paged ) || ! ( empty( $paged ) ) ) ? $paged : 1;


	$args = array(
		'paged' => $paged,
		'post_type' => 'product',
		'orderby' => 'meta_value_num',
		'meta_key' => '_price',
		'posts_per_page' => $per_page,
		'order' => $order,
	);

	if ($min && $max)
		$args['meta_query'] = array(
			array(
				'key' => '_price',
				'value' => array($min,$max),
				'compare' => 'BETWEEN',
				'type' => 'NUMERIC'
			)
		);

	$loop = new WP_Query( $args );
	$product_list = array();
	if ( $loop->have_posts() ) {
		while ( $loop->have_posts() ) : $loop->the_post();
			global $post;

			$product         = wc_get_product( $post );
			$slug            = get_post_field( 'post_name', $post->ID );
			$image_id        = $product->get_image_id();
			$image_url       = wp_get_attachment_image_url( $image_id, 'full' );
			$currency        = get_woocommerce_currency_symbol();
			$price           = $product->get_price();
			$regular_price   = $product->get_regular_price();
			$sale_price      = $product->get_sale_price();
			$permalink       = get_permalink( $post->ID );
			$product_details = array( 'id'            => $post->ID,
			                          'name'          => get_the_title(),
			                          'slug'          => $slug,
			                          'image'         => $image_url,
			                          'currency'      => $currency,
			                          'price'         => $price,
			                          'regular_price' => $regular_price,
			                          'sale_price'    => $sale_price,
			                          'permalink'     => $permalink
			);

			array_push( $product_list, (array) $product_details );

		endwhile;
	}
	wp_reset_postdata();
	echo json_encode( $product_list );
	exit();
}



