<?php
header('Content-Type: application/json');
//http://localhost/wordpress/wp_api/wp-json/wp/v2/related-products/?product_id=32
add_action('rest_api_init', function(){
	register_rest_route('wp/v2', 'related-products', [
		'methods' => 'GET',
		'callback' => 'related_products',
	]);
});

function related_products($request = null) {

	$pid = $_GET['product_id'];
	$array = wc_get_related_products( $pid );
	
	$args = array(
		'post_type'      => 'product',
		'post__in'       => $array,
		'posts_per_page' => 10,
		'order'          => 'desc',
	);

	$loop = new WP_Query( $args );
	$product_list = array();
	if ( $loop->have_posts() ) {
		while ( $loop->have_posts() ) : $loop->the_post();
			global $post;

			$product = wc_get_product($post);
            $slug = get_post_field( 'post_name', $post->ID );
            $image_id  = $product->get_image_id();
            $image_url = wp_get_attachment_image_url( $image_id, 'full' );
            $currency = get_woocommerce_currency_symbol();
            $decimals = wc_get_price_decimals();
            $decimal_separator = wc_get_price_decimal_separator();
    		$thousand_separator = wc_get_price_thousand_separator();
            $price = $product->get_price();
            $regular_price = $product->get_regular_price();
            $sale_price = $product->get_sale_price();
            $permalink = get_permalink( $post->ID );
            $product_details = array('id' => $post->ID, 'name' => get_the_title(), 'slug' => $slug, 'image' => $image_url, 'currency' => $currency, 'decimals' => $decimals,'decimal_separator' => $decimal_separator, 'thousand_separator' => $thousand_separator, 'price' => $price, 'regular_price' => $regular_price, 'sale_price' => $sale_price, 'permalink' => $permalink );

            array_push($product_list, (array)$product_details);

		endwhile;
	}
	wp_reset_postdata();
	echo json_encode( $product_list );
}