<?php
//generate 20 random posts
function twenty_posts_generator() {
	$posts_count = wp_count_posts('post');
	$posts_count = $posts_count->publish + $posts_count->future + $posts_count->draft;
	if ($posts_count < 20) {
		for ($i = $posts_count; $i < 20; $i++) {
			//check if 20 post already exist
			$posts_count = wp_count_posts('post');
			$posts_count = $posts_count->publish + $posts_count->future + $posts_count->draft;
			if ($posts_count >= 20) {
				return;
			}
			//function
			$content = file_get_contents('http://loripsum.net/api/7/link/ul');
			$title = file_get_contents('http://loripsum.net/api/1/headers');
			$title = explode('<h1>', $title);
			$title = array_splice($title, 1, 1);
			$title = explode('</h1>', $title[0]);
			$title = $title[0];
			$post_data = array(
				'post_content'   => $content,
				'post_title' => $title
			);
			$post_id = wp_insert_post( wp_slash($post_data) );
			wp_publish_post($post_id);
		}
	}
}
twenty_posts_generator();


// loadmore action
add_action( 'wp_ajax_loadmore', 'loadmore_action' );
add_action( 'wp_ajax_nopriv_loadmore', 'loadmore_action' );

function loadmore_action() {

	$paged = ! empty( $_POST[ 'paged' ] ) ? $_POST[ 'paged' ] : 1;
	$paged++;

	$order = 'ASC';
	$orderby = 'title';
	if (array_key_exists('sort_by', $_POST)) {
		$order = $_POST['sort_by'];
		if (strpos($order, '-')) {
			$sort_data = explode('-', $order);
			$order = $sort_data[0];
			$orderby = 'meta_value_num';
			$meta_key = $sort_data[1];
		}
	}
	if ($meta_key) {
		$args = array(
			'post_type' => 'post',
			'order' => $order,
			'orderby' => $orderby,
			'meta_key' => $meta_key,
			'paged' => $paged
		);
	} else {
		$args = array(
			'post_type' => 'post',
			'order' => $order,
			'orderby' => $orderby,
			'paged' => $paged
		);
	}

	$new_posts = new WP_Query($args);
	while( $new_posts->have_posts() ) {
		$new_posts->the_post();
		$post_size = get_post_meta(get_the_ID(), 'post_length', true);
		get_template_part( 'template-parts/post', 'list' );
	}

	wp_die();
}

//add length meta field
add_action('save_post', 'length_meta_field');
function length_meta_field($post_ID) {
	$content = get_the_content('', '', $post_ID);
	$content_size = strlen($content);
	update_post_meta($post_ID, 'post_length', $content_size);
}


//add page in wordpress menu
add_action( 'admin_menu', 'misha_menu_page' );

function misha_menu_page() {

	add_menu_page(
		'Sorting Feature',
		'Sorting feature',
		'manage_options',
		'sorting-feature',
		'sort_page_content', // callback function /w content
		'dashicons-sort',
		6
	);

}

function sort_page_content(){

	echo '<form method="post" action="options.php">';

	settings_fields( 'sort_group' ); // settings group name
	do_settings_sections( 'sorting-feature' ); // just a page slug
	submit_button();

	echo '</form></div>';

}
add_action( 'admin_init',  'misha_register_setting' );

function misha_register_setting(){

	register_setting(
		'sort_group', // settings group name
		'sort_possibility' // option name
	);

	add_settings_section(
		'sort_checkbox', // section ID
		'',
		'',
		'sorting-feature' // page slug
	);

	add_settings_field(
		'sort_possibility',
		'Сортировать по длине поста',
		'checkbox_field_html', // function which prints the field
		'sorting-feature', // page slug
		'sort_checkbox'
	);

}

function checkbox_field_html(){


	$options = get_option( 'sort_possibility' );
	$checked = '';
	if ($options) {
		$checked = 'checked';
	}
	$html = '<input type="checkbox" id="checkbox_example" name="sort_possibility" value="1" '. $checked .'/>';
	echo  $html;

}

function strposX($haystack, $needle, $number){
	if($number == '1'){
		return strpos($haystack, $needle);
	}elseif($number > '1'){
		return strpos($haystack, $needle, strposX($haystack, $needle, $number - 1) + strlen($needle));
	}else{
		return null;
	}
}

function add_cat_after_p($p_number, $content) {
	$p_pos = strposX($content, '</p>' , $p_number);
	if ($p_pos) {
		$content = substr_replace($content, '<img src="'. get_template_directory_uri() . '/assets/images/cat.jpg">', $p_pos + 4, 0);
	}
	return $content;
}