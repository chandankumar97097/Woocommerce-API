<?php
add_action('rest_api_init', function(){
	register_rest_route('wc/v3', 'search', [
		'methods' => 'POST',
		'callback' => 'search_product',
	]);
});

function search_product($request = null) {
	$paged = $request->get_param( 'page' );
	$search_term = $request->get_param( 'search' );
	$paged = ( isset( $paged ) || ! ( empty( $paged ) ) ) ? $paged : 1;

	if ( $search_term !== '' ) {
		$args = array(
			'paged' => $paged,
			'post_type' => 'product',
			's' => $search_term,
			'orderby' => 'title',
			'posts_per_page' => 10,
			'order' => 'desc',
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
	}else{
		$code = 404; $status = false;
		$msg = 'Product Not Found!';
		$json = array('code'=>$code,'status'=>$status,'msg'=>$msg);
		echo json_encode($json);
	}
}