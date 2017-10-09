<?php

/* functions, filters and hooks for ellak.gr child theme */

# axil 2015-09-06
# http://diywpblog.com/wordpress-optimization-remove-query-strings-from-static-resources/
function _remove_script_version( $src ){
	$parts = explode( '?ver', $src );
        return $parts[0];
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );

# Mediawiki SSO
add_action('wp_logout', 'mw_logout');
function mw_logout() {
    $cookiesSet = array_keys($_COOKIE);
    for ($x=0;$x<count($cookiesSet);$x++) setcookie($cookiesSet[$x],"",time()-1);
}

add_action( 'after_setup_theme', 'ellak_theme_setup' );
function ellak_theme_setup() {
	// remove generatepress action hooks
	remove_action( 'generate_before_content',
		'generate_featured_page_header_inside_single', 10 );
	remove_action( 'generate_credits', 'generate_add_footer_info' );

	// child theme translations in /languages
	load_child_theme_textdomain( 'gpchild-ellak', get_template_directory()
		. '/languages' );

	// hide admin bar for subscribers
	$user = wp_get_current_user();
	if( in_array( 'subscriber', $user->roles ) ) {
		show_admin_bar( false );
	}
}

// enqueue extra scripts and styles
add_action( 'wp_enqueue_scripts', 'ellak_font_awesome' );
function ellak_font_awesome() {
	// Font Awesome
	wp_enqueue_style( 'font-awesome',
		'//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );

	// Facebook SDK
	wp_enqueue_script( 'facebook-sdk', get_stylesheet_directory_uri() . '/js/facebook.js', array(), '2.3', true );
	
	// Vue.js
//	wp_register_script( 'vue-js', 'https://unpkg.com/vue@2.4.2/dist/vue.min.js', null, '2.4.2', false );
//	wp_enqueue_script( 'vue-js');
	wp_enqueue_script( 'ellak-vue-js', get_stylesheet_directory_uri().'/js/vue.js', null, '2.4.2', false );
}

//enqueue boostrap scripts
add_action('wp_enqueue_scripts', 'enqueue_bootstrap');
function enqueue_bootstrap(){
    //the css file
    wp_enqueue_style('bootstrap_css_3.3', get_stylesheet_directory_uri().'/bootstrap.min.css');
    
    //the js file
    wp_enqueue_script('bootstrap_js_3.3', get_stylesheet_directory_uri().'/js/bootstrap.min.js', array('jquery'), '3.3', false);
}


/** 
 * enqueue the scripts for the edu_fos and the edu_quest archive page
 **/
add_action('wp_enqueue_scripts', 'enqueue_edu_fos_js');
function enqueue_edu_fos_js(){
    wp_enqueue_script('edu_fos_js', get_stylesheet_directory_uri().'/js/edu_fos_js.js', array('jquery'), '0.1', false);
    if(is_page_template('template-edu_quest_results.php')){
      wp_enqueue_script('edu_quest_js', get_stylesheet_directory_uri().'/js/edu_quest_js.js', array('ellak-vue-js', 'underscore'), '0.1', false);
    }
}

/**
 * Enqueue the style file for the edu_fos archive page
 **/
if(!function_exists('archive_edu_fos_style')){
    function archive_edu_fos_style(){
        //if(is_page_template('archive-github_contributor.php')){
            wp_enqueue_style('archive-edu_fos-style', get_stylesheet_directory_uri().'/css/archive-edu_fos-style.css');
            wp_enqueue_style('archive-edu_quest_results-style', get_stylesheet_directory_uri().'/css/template-edu_quest_results.css');
        //}
    }
}
add_action('wp_enqueue_scripts', 'archive_edu_fos_style');

// register the edu_quest custom post type
if (!function_exists('register_edu_quest_post_type')){
	function register_edu_quest_post_type(){
		$labels=array(
				'name' => 'FOSS in Unis',
				'singular_name' => 'FOSS in Unis',
				'add_new'=>'Add New',
				'edit_item'=>'Edit Item',
				'view_item'=>'View Item'
				);
		$args=array(
				'supports' => array('title', 'custom-fields'),
				'label' => 'Quest Entries',
				'labels' => $labels,
				'public' => true,
				'description' => 'Post type for the eellak questionary for free software in universities.',
				'has_archive' => true,
				'show_ui' => true,
				'show_in_menu' => true, 
				'exclude_from_search' => false,
				'publicly_queryable' => true,
				'show_in_rest' => true
		);
		
		if (get_current_blog_id()===11){
			register_post_type('edu_quest_post_type', $args);
		}
	}
}
add_action('init', 'register_edu_quest_post_type');

// add clearfix class in the header container
add_filter( 'generate_inside_header_class', 'ellak_inside_header_classes' );
function ellak_inside_header_classes( $classes ) {
	$classes[] = 'clearfix';
	return $classes;
}

// add greek subset in embedded fonts
add_filter( 'generate_fonts_subset', 'ellak_fonts_subset' );
function ellak_fonts_subset() {
	return 'latin,latin-ext,greek';
}

// load the ellak news bar if available
add_action( 'generate_before_header', 'ellak_load_newsbar' );
function ellak_load_newsbar() {
	if( function_exists( 'ellak_newsbar' ) ) {
		ellak_newsbar();
	}
}

// add slider in #primary, only in home. requires 'Advanced Post Slider' plugin
add_action( 'generate_before_main_content', 'ellak_slider' );
function ellak_slider() {
	if( is_front_page() && function_exists( "get_smooth_slider_recent" ) ){ get_smooth_slider_recent(); }
}

// social links
add_action( 'generate_before_header_content', 'ellak_social_links' );
function ellak_social_links() { ?>
	<div class="header-social-links">
		<ul class="social-links">
			<li class="social-link-opinion"><a href="https://ellak.gr/pite-mas-ti-gnomi-sas/" title="Πείτε μας τη γνώμη σας" target="_blank"><span>Πείτε μας τη γνώμη σας</span></a></li>
			<li class="social-link-facebook"><a href="https://www.facebook.com/eellak" title="Facebook" target="_blank"><span>Facebook</span></a></li>
      <li class="social-link-twitter"><a href="https://www.twitter.com/eellak" title="Twitter" target="_blank"><span>Twitter</span></a></li>
			<li class="social-link-github"><a href="https://github.com/eellak" title="GitHub" target="_blank"><span>GitHub</span></a></li>
			<li class="social-link-vimeo"><a href="https://www.vimeo.com/eellak" title="Vimeo" target="_blank"><span>Vimeo</span></a></li>
			<li class="social-link-flickr"><a href="https://flickr.com/photos/eellak" title="Flickr" target="_blank"><span>Flickr</span></a></li>
			<li class="social-link-rss"><a href="https://ellak.gr/rss-feeds/" title="RSS" target="_blank"><span>RSS</span></a></li>
		</ul>
	</div><!-- .header-social-links -->
<?php }

// footer
add_action( 'generate_credits', 'ellak_credits' );
function ellak_credits() {
	echo __( '<a href="https://mathe.ellak.gr/" target="_blank">Υλοποίηση με χρήση του Ανοικτού Λογισμικού</a>', 'gpchild-ellak-opengov' )
		. ' <a href="https://wordpress.org/" target="_blank">Wordpress</a> | '
		. '<a href="https://ellak.gr/ori-chrisis" target="_blank">'
		. __( 'Όροι Χρήσης & Δήλωση Απορρήτου', 'gpchild-ellak-opengov' ) . '</a> | '
		. __( 'Άδεια χρήσης περιεχομένου:', 'gpchild-ellak' )
		. ' <a href="https://creativecommons.org/licenses/by-sa/4.0/deed.el">'
		. __( 'CC-BY-SA', 'gpchild-ellak' ) . '</a> | '
		. ' <a href="https://ellak.gr/stichia-epikinonias-chartis/">'
		. __( 'Επικοινωνία', 'gpchild-ellak' ) . '</a>';
}
remove_filter( 'wprss_pagination', 'wprss_pagination_links' );

// add publish functionality for the foss-in-unis bulk actions menu in the
// admin-screen for the posts listing.
add_filter('bulk_actions-edit-edu_quest_post_type', 'register_my_bulk_actions');

if(!function_exists('register_my_bulk_actions')){
	function register_my_bulk_actions($bulk_actions){
		$bulk_actions['publish_edu_quest_entry'] = 'Αμεση Δημοσιευση Καταχωρησης';
		return $bulk_actions;
	}
}

// handle the bulk publish option box

add_filter('handle_bulk_actions-edit-edu_quest_post_type', 'handle_bulk_publish_edu_quest_post_type', 10, 3);
if(!function_exists('handle_bulk_publish_edu_quest_post_type')){
	function handle_bulk_publish_edu_quest_post_type($redirect_to, $doaction, $post_ids){
		if($doaction=='publish_edu_quest_entry'){
			$error_var=array(); //the errors array
			$return_successful=true; //returned zero if zero occurs as return value from one post update execution
			foreach ($post_ids as $post_id){
				$update_array=array('ID' => $post_id, 'post_status' => 'publish');
				$return_successful=wp_update_post($update_array, $error_var);
			}
			$redirect_to=add_query_arg('bulk_publish_successful', $return_successful, $redirect_to);
		}
	}
}

// notify the user if the publishing was successful
add_action( 'admin_notices', 'edu_quest_bulk_update_admin_notice' );
function edu_quest_bulk_update_admin_notice() {
  if ( ! empty( $_REQUEST['bulk_publish_successful'] ) ) {
    $publish_successful = intval( $_REQUEST['bulk_publish_successful'] );
    echo '<div id="message" class="updated fade">';
		if($publish_successful){
			echo 'Τα επιλεγμένα posts δημοσιεύτηκαν επιτυχώς.';
		}
		else{
			echo 'Η δημοσίευση απέτυχε. Παρακαλώ, προσπαθήστε εκ νέου.';
		}
		echo '</div>';
  }
}



/*
 * Add the extra columns in the admin panel for Projects list page
 */
add_filter('manage_edu_quest_post_type_posts_columns', 'edu_quest_edit_columns');
add_action('manage_edu_quest_post_type_posts_custom_column',  'edu_quest_custom_columns');
if(!function_exists('edu_quest_edit_columns')){
	function edu_quest_edit_columns($columns){
		//$client_string=__("Client", "ballian");
		$columns = array_merge($columns, array(
				'course' => 'Μάθημα',
				'software' => 'Λογισμικό',
				'software_url' => 'URL λογισμικού',
//				'category' => __('Category', 'ballian'),
//				'is_featured' => __('Is Featured', 'ballian')
			)
		);
		return $columns;
	}
}

if (!function_exists('edu_quest_custom_columns')) {
	function edu_quest_custom_columns($column){
//			$current_language= qtranxf_getLanguage();
			global $post;
			switch ($column) {
				case 'course':
				$terms = get_post_meta( $post->ID , 'edu_quest_course' );
				if (!empty($terms)){
//					foreach($terms as $term){
//						$echoable=qtranxf_use($current_language, $term->name);
//						$echoable_arr[]=$echoable;
//					}
					echo $terms[0];
				}
				else{
					echo 'καμία καταχώρηση';
				}
				break;
				case 'software':
				$terms = get_post_meta( $post->ID , 'edu_quest_software' );
				if (!empty($terms)){
//					foreach($terms as $term){
//						$echoable=qtranxf_use($current_language, $term->name);
//						$echoable_arr[]=$echoable;
//					}
					echo $terms[0];
				}
				else{
					echo 'καμία καταχώρηση';
				}
				break;
				case 'software_url':
					$terms = get_post_meta( $post->ID , 'edu_quest_software_url' );
//					$echoable;
//					$echoable_arr=array();
					if (!empty($terms)){
//						foreach($terms as $term){
//							$echoable=qtranxf_use($current_language, $term->name);
//							$echoable_arr[]=$echoable;
//						}
						echo $terms[0];
					}
					else{
						echo 'καμία καταχώρηση';
					}
					break;
		}
	}
}


/* Include the diadose_bar plugin */
add_action( 'generate_before_header', 'load_diadose_bar' );
function load_diadose_bar(){
    if(function_exists('diadose_bar')){
        diadose_bar();
    }
}

// Add to the admin_init action hook
//add_filter('current_screen', 'my_current_screen' );
// 
//function my_current_screen($screen) {
//    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return $screen;
//    print_r($screen);
//    return $screen;
//}

?>
