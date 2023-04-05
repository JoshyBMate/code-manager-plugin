<?php
require_once "sendinblueAPI.php";
//EMAIL API
function sendinblue_email_single($name, $email, $code){
	//working_directory/emailBuilder.php

	$url = "https://test.openform.online/staging/codeblue/my-account?" . urlencode($name) . "&" . $email;

	$credentials = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', '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');
	$apiInstance = new SendinBlue\Client\Api\TransactionalEmailsApi(new GuzzleHttp\Client(),$credentials);

	$sendSmtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail([
		'subject' => 'CodeBlue Training Invitation',
		'sender' => ['name' => 'CodeBlue CPR', 'email' => 'josh@openform.co.za'],
		'replyTo' => ['name' => 'CodeBlue CPR', 'email' => 'no-reply@openform.co.za'],
		'to' => [[ 'name' => $name, 'email' => $email]],
		'htmlContent' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Training Registration</title></head><body bgcolor="#2400ba" style="background:#2400ba"><table cellpadding="50" cellspacing="0" border="0" width="100%"><tr><td valign="middle" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:690px;background-color:#fff;padding-left:30px;padding-right:30px;padding-bottom:100px"><tr><td align="center" style="padding-top:70px"><img src="https://test.openform.online/staging/codeblue/wp-content/uploads/2023/04/mailer_logo.jpg" style="width:230px" alt="CodeBlue Logo"></td></tr><tr><td align="center" style="padding-top:30px;padding-bottom:30px;font-family:Arial;color:#2400ba;font-size:16px;line-height:24px"><p>Hi {{params.name}},<br>You’ve been enrolled to participate in an online course/training that will give you a recognised certificate on completion.</p></td></tr><tr><td align="center" style="padding-top:0;font-family:Arial;color:#ff0f82;font-size:40px;line-height:40px;font-weight:700"><p style="margin:0">REGISTER FOR YOUR<br>TRAINING NOW</p></td></tr><tr><td align="center" style="padding-top:0;font-family:Arial;color:#2400ba;font-size:16px;line-height:20px;font-weight:400"><table style="padding-top:30px" width="300px"><tr><td align="center">Your unique invitation code is</td></tr><tr><td style="background:#efefef;padding:20px;letter-spacing:5px;font-size:30px;width:100%" align="center">{{params.code}}</td></tr><tr><td align="center" style="padding-top:60px"><a href="{{params.link}}" style="background-color:#2400ba;color:#fff;text-decoration:none;padding:10px 20px;font-weight:700;font-size:20px">REGISTER ACCOUNT</a></td></tr></table></td></tr></table><table><tr><td style="padding-top:30px;font-family:Arial;color:#fff;font-size:10px">Email proudly brought to you by <a href="https://www.openform.co.za/" style="color:#fff">Openform</a></td></tr></table></td></tr></table></body></html>',
		'params' => ['code' => $code, 'link' => $url, 'name' => $name]
	]);

	try {
		$result = $apiInstance->sendTransacEmail($sendSmtpEmail);
		return true;
	} catch (Exception $e) {
	    error_log(print_r($e,true));
	    return $e;
	}
}



//LOADING THE JS SCRIPTS ONTO THE RELEVANT PAGES
function load_scripts(){
	if(is_page(array('become-a-lifesaver',"become-an-advertiser","become-a-sponsor","become-a-trainer"))){
		wp_enqueue_script( 'form', get_stylesheet_directory_uri() . '/js/form.js', array(), '1.0.0', true );
	}
	else if(is_page(array('contact'))){
		wp_enqueue_script( 'map', get_stylesheet_directory_uri() . '/js/map.js', array(), '1.0.0', true );
		wp_enqueue_script( 'form', get_stylesheet_directory_uri() . '/js/form.js', array(), '1.0.0', true );
	}else if(is_single()){
		wp_enqueue_script( 'product', get_stylesheet_directory_uri() . '/js/product.js', array(), '1.0.0', true );
	}else if(is_page('shop')){
		wp_enqueue_script( 'shop', get_stylesheet_directory_uri() . '/js/shop.js', array(), '1.0.0', true );
		wp_localize_script( 'shop', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}else if(is_page("events")){
		wp_enqueue_script( 'blog', get_stylesheet_directory_uri() . '/js/blog.js', array(), '1.0.0', true );
	}else if(is_page("about")){
		wp_enqueue_script( 'about', get_stylesheet_directory_uri() . '/js/about.js', array(), '1.0.0', true );
	}else if(is_front_page()){
		wp_enqueue_script( 'home', get_stylesheet_directory_uri() . '/js/home.js', array(), '1.0.0', true );
	}
}
add_action( 'wp_enqueue_scripts', 'load_scripts' );
//////////////////////////////////////////////////////

//REGISTERING NEW FIELDS INTO THE REST API
function register_custom_fields() {
	register_rest_field( 'product', 'short_meta_data', array(
		'get_callback' => function ( $post ) {
			return get_field( 'short_meta_data', $post->ID );
		},
		'update_callback' => function ( $value, $post, $field_name ) {
			update_field( $field_name, $value, $post->ID );
		},
		'schema' => array(
			'type' => 'string',
			'description' => 'Short Meta Data',
			'context' => array( 'view', 'edit' ),
		),
	) );

	register_rest_field( 'product', 'course_duration', array(
		'get_callback' => function ( $post ) {
			return get_field( 'course_duration', $post->ID );
		},
		'update_callback' => function ( $value, $post, $field_name ) {
			update_field( $field_name, $value, $post->ID );
		},
		'schema' => array(
			'type' => 'string',
			'description' => 'Course Duration',
			'context' => array( 'view', 'edit' ),
		),
	) );
}
add_action( 'rest_api_init', 'register_custom_fields' );
///////////////////////////////////////////////////////////////////

// Replacing add-to-cart button in shop pages and archives pages (forn non logged in users)
add_filter( 'woocommerce_loop_add_to_cart_link', 'conditionally_change_loop_add_to_cart_link', 10, 2 );
function conditionally_change_loop_add_to_cart_link( $html, $product ) {
	if ( ! is_user_logged_in() ) {
		$link = get_permalink($product_id);
		$button_text = __( "View product", "woocommerce" );
		$html = '<a href="'.$link.'" class="button alt add_to_cart_button">'.$button_text.'</a>';
	}
	return $html;
}

// Avoid add to cart for non logged user (or not registered)
add_filter( 'woocommerce_add_to_cart_validation', 'logged_in_customers_validation', 10, 3 );
function logged_in_customers_validation( $passed, $product_id, $quantity) {
	if( ! is_user_logged_in() ) {
		$passed = false;

		// Displaying a custom message
		$message = __("You need to be logged in to be able adding to cart…", "woocommerce");
		$button_link = get_permalink( get_option('woocommerce_myaccount_page_id') );
		$button_text = __("Login or register", "woocommerce");
		$message .= ' <a href="'.$button_link.'" class="button wc-forward wp-element-button" style="float:right;">'.$button_text.'</a>';

		wc_add_notice( $message, 'error' );
	}
	return $passed;
}

//AJAX FILTER CODE
function filter_products_by_category() {
	// Get the category slug and sorting parameter from the AJAX request
	$category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
	$sorting = isset( $_POST['sorting'] ) ? sanitize_text_field( $_POST['sorting'] ) : '';

	// Build the query to get the products of the category and sort them
	$args = array(
		"post_type" => "product",
		"posts_per_page" => -1, // Show all products
		"orderby" => $sorting,
	);

	if (!empty($category) && $category !== "all") {
		$args["tax_query"] = array(
			array(
				"taxonomy" => "product_cat",
				"field" => "slug",
				"terms" => $category,
			),
		);
	}

	if ($sorting === "price_asc") {
		$args["meta_key"] = "_price";
		$args["orderby"] = "meta_value_num";
		$args["order"] = "ASC";
	} elseif ($sorting === "price_desc") {
		$args["meta_key"] = "_price";
		$args["orderby"] = "meta_value_num";
		$args["order"] = "DESC";
	} elseif ($sorting === "popularity") {
		$args["meta_key"] = "total_sales";
		$args["orderby"] = "meta_value_num";
		$args["order"] = "DESC";
	} else {
		$args["orderby"] = $sorting;
	}

	$query = new WP_Query($args);

	// Loop through the products and display them
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			wc_get_template_part( 'content', 'product' );
		}
	} else {
		echo '<p>No products found.</p>';
	}

	wp_die();
}
add_action( 'wp_ajax_filter_products_by_category', 'filter_products_by_category' );
add_action( 'wp_ajax_nopriv_filter_products_by_category', 'filter_products_by_category' );