<?php

error_reporting(E_ALL); //Turn on Error reporting

/* Starkers Items*/
require_once( 'external/starkers-utilities.php' );
add_filter( 'body_class', 'add_slug_to_body_class' );

/* --------------------------------------------------
Location Rewriting based on the URL, including templating!
-----------------------------------------------------*/

// REWRITE RULES
add_filter('rewrite_rules_array','wp_insertMyRewriteRules');
add_filter('query_vars','wp_insertMyRewriteQueryVars');
add_filter('init','flushRules');

// Remember to flush_rules() when adding rules
function flushRules(){
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
}

// Adding a new rule
function wp_insertMyRewriteRules($rules){
  $newrules = array();
  //If the URL matches any of the locations, do an internal re-write.
  //$locations = require("locations.php");
  //$newrules['(.?.+?)(/[0-9]+)?/('.$locations.')'] = 'index.php?pagename=$matches[1]&page=$matches[2]&placename=$matches[3]';
  return $newrules + $rules;
}

// Adding the id var so that WP recognizes it
function wp_insertMyRewriteQueryVars($vars){
  array_push($vars, 'placename');
  return $vars;
}

//Modify buffer here, and then return the updated code
function location_callback($buffer) {
  //{##around #location# and surrounding areas##|## or this ##}
  $location = get_query_var("placename");

  //Replace any instances of #location# with the actual location (based on the URL)
  $location = str_replace("-"," ",$location);
  $location = ucwords($location);

  //We have a location! lets remove the stuff we don't want (from the | onwards)
  $buffer = str_replace('$location',$location,$buffer);
  if (strlen($location)){
    $buffer = preg_replace("/\|(.*?)}%/","%",$buffer);
  } else {
    $buffer = preg_replace("/%{(.*?)}\|/","%",$buffer);
  }
    $buffer = str_replace(array("%{","}%"),"",$buffer);
  return $buffer;
}

function buffer_start() { ob_start("location_callback"); }
function buffer_end() { ob_end_flush(); }

add_action('wp', 'buffer_start');
add_action('wp_footer', 'buffer_end');

/* -------------------------------------------------------
  Post Thumbnails
------------------------------------------------------- */
add_theme_support( 'post-thumbnails' );

if (class_exists('MultiPostThumbnails')) {
    new MultiPostThumbnails(
        array(
            'label' => 'Secondary Image',
            'id' => 'secondary-image',
            'post_type' => 'jdtl_video'

        )
    );
}

/* --------------------------------------------------
 * Register Custom Menus
  -------------------------------------------------- */
register_nav_menus(
  array(
    'page_navigation' => "Page Navigation Menu",
  )
);

/* -------------------------------------------------------
    Register Custom Post Type
------------------------------------------------------- */
add_action('init', 'register_casestudy');
function register_casestudy() {
  register_post_type( 'casestudy',
    array('labels' => array(
      'name' => __('Case Studies', 'post type general name'), /* The Title of the Group */
      'singular_name' => __('Case Study', 'post type singular name'), /* The individual type */
      'add_new' => __('Add New Case Study', 'custom post type item'), /* The add new menu item */
      'add_new_item' => __('Add New Case Study'), /* Add New Display Title */
      'edit' => __( 'Edit' ), /* Edit Dialog */
      'edit_item' => __('Edit Case Study'), /* Edit Display Title */
      'new_item' => __('New Case Study'), /* New Display Title */
      'view_item' => __('View Case Studies'), /* View Display Title */
      'search_items' => __('Search Case Studies'), /* Search Custom Type Title */
      'not_found' =>  __('Nothing found in the Database.'), /* This displays if there are no entries yet */
      'not_found_in_trash' => __('Nothing found in Trash'), /* This displays if there is nothing in the trash */
      'parent_item_colon' => ''
      ), /* end of arrays */
      'description' => __( 'This is the Case Study custom post type.' ), /* Custom Type Description */
      'public' => true,
      'publicly_queryable' => true,
      'exclude_from_search' => false,
      'show_ui' => true,
      'query_var' => true,
      'menu_position' => 6, /* this is what order you want it to appear in on the left hand side menu */
      'rewrite' => true,
      'capability_type' => 'post',
      'hierarchical' => false,
      /* the next one is important, it tells what's enabled in the post editor */
      'supports' => array( 'title', 'editor', 'thumbnail'),
      'taxonomies' => array('category')
    ) /* end of options */
  ); /* end of register post type */
}

/* -------------------------------------------------------
    Register Custom Taxonomies
------------------------------------------------------- */
// Industry
add_action( 'init', 'create_industry_nonhierarchical_taxonomy', 0 );
function create_industry_nonhierarchical_taxonomy() {
    $labels = array(
        'name' => _x( 'Industry', 'taxonomy general name' ),
        'singular_name' => _x( 'Industries', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Industries' ),
        'popular_items' => __( 'Popular Industries' ),
        'all_items' => __( 'All Industries' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Industry' ),
        'update_item' => __( 'Update Industry' ),
        'add_new_item' => __( 'Add New Industry' ),
        'new_item_name' => __( 'New Industry Name' ),
        'separate_items_with_commas' => __( 'Separate Industries with commas' ),
        'add_or_remove_items' => __( 'Add or remove Industries' ),
        'choose_from_most_used' => __( 'Choose from the most used topics' ),
        'menu_name' => __( 'Industries' ),
    );
    register_taxonomy('industry','casestudy',array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array( 'slug' => 'industry' ),
    ));
}


// Technology
add_action( 'init', 'create_technology_nonhierarchical_taxonomy', 0 );
function create_technology_nonhierarchical_taxonomy() {
    $labels = array(
        'name' => _x( 'Technology', 'taxonomy general name' ),
        'singular_name' => _x( 'Technologies', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Technologies' ),
        'popular_items' => __( 'Popular Technologies' ),
        'all_items' => __( 'All Technologies' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Technology' ),
        'update_item' => __( 'Update Technology' ),
        'add_new_item' => __( 'Add New Technology' ),
        'new_item_name' => __( 'New Technology Name' ),
        'separate_items_with_commas' => __( 'Separate Technologies with commas' ),
        'add_or_remove_items' => __( 'Add or remove Technologies' ),
        'choose_from_most_used' => __( 'Choose from the most used Technologies' ),
        'menu_name' => __( 'Technologies' ),
    );
    register_taxonomy('technology','casestudy',array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array( 'slug' => 'technology' ),
    ));
}
// Programme
add_action( 'init', 'create_programme_nonhierarchical_taxonomy', 0 );
function create_programme_nonhierarchical_taxonomy() {
    $labels = array(
        'name' => _x( 'Programme', 'taxonomy general name' ),
        'singular_name' => _x( 'Programmes', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Programmes' ),
        'popular_items' => __( 'Popular Programmes' ),
        'all_items' => __( 'All Programmes' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Programme' ),
        'update_item' => __( 'Update Programme' ),
        'add_new_item' => __( 'Add New Programme' ),
        'new_item_name' => __( 'New Programme Name' ),
        'separate_items_with_commas' => __( 'Separate Programmes with commas' ),
        'add_or_remove_items' => __( 'Add or remove Programmes' ),
        'choose_from_most_used' => __( 'Choose from the most used Programmes' ),
        'menu_name' => __( 'Programmes' ),
    );
    register_taxonomy('programme','casestudy',array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array( 'slug' => 'programme' ),
    ));
}
// Partner
add_action( 'init', 'create_partner_nonhierarchical_taxonomy', 0 );
function create_partner_nonhierarchical_taxonomy() {
    $labels = array(
        'name' => _x( 'Partner', 'taxonomy general name' ),
        'singular_name' => _x( 'Partners', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Partners' ),
        'popular_items' => __( 'Popular Partners' ),
        'all_items' => __( 'All Partners' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Partner' ),
        'update_item' => __( 'Update Partner' ),
        'add_new_item' => __( 'Add New Partner' ),
        'new_item_name' => __( 'New Partner Name' ),
        'separate_items_with_commas' => __( 'Separate Partners with commas' ),
        'add_or_remove_items' => __( 'Add or remove Partners' ),
        'choose_from_most_used' => __( 'Choose from the most used topics' ),
        'menu_name' => __( 'Partners' ),
    );
    register_taxonomy('partner','casestudy',array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array( 'slug' => 'partner' ),
    ));
}

// JD : Show 'No Posts' message.
function jd_show_noposts_message( $casestudy_noposts_text ) {
    echo '<p class="font1 text-center">' . $casestudy_noposts_text . '</p>';
}


function print_bs_customtaxonomies( $taxonomy ) {
    //$terms = get_terms($taxonomy);
    $terms = get_terms( array(
    'taxonomy' => $taxonomy,
    'hide_empty' => false,
    ) );
    if ( $terms ) {
        foreach ( $terms as $term ) {
            printf( '<li><a href="#">%s</a></li>', esc_attr( $term->name ), esc_html( $term->name ) );
        }
    }
}
//fjarret
function jd_get_tags() {
    $terms = get_terms( array(
        'taxonomy' => 'post_tag',
        'hide_empty' => false,
    ) );
	if ( $terms ) {
		foreach ( $terms as $term ) {
			printf( '<option value="%s">%s</option>', esc_attr( $term->slug ), esc_html( $term->name ) );
		}
	}
}
function jd_get_industry_tags() {
    $terms = get_terms( array(
        'taxonomy' => 'industry',
        'hide_empty' => false,
    ) );
	if ( $terms ) {
		foreach ( $terms as $term ) {
			printf( '<option value="%s">%s</option>', esc_attr( $term->slug ), esc_html( $term->name ) );
		}
	}
}
function jd_get_technology_tags() {
    $terms = get_terms( array(
        'taxonomy' => 'technology',
        'hide_empty' => false,
    ) );
	if ( $terms ) {
		foreach ( $terms as $term ) {
			printf( '<option value="%s">%s</option>', esc_attr( $term->slug ), esc_html( $term->name ) );
		}
	}
}
function jd_get_programme_tags() {
    $terms = get_terms( array(
        'taxonomy' => 'programme',
        'hide_empty' => false,
    ) );
	if ( $terms ) {
		foreach ( $terms as $term ) {
			printf( '<option value="%s">%s</option>', esc_attr( $term->slug ), esc_html( $term->name ) );
		}
	}
}
function jd_get_partner_tags() {
    $terms = get_terms( array(
        'taxonomy' => 'partner',
        'hide_empty' => false,
    ) );
	if ( $terms ) {
		foreach ( $terms as $term ) {
			printf( '<option value="%s">%s</option>', esc_attr( $term->slug ), esc_html( $term->name ) );
		}
	}
}

function fjarrett_custom_taxonomy_dropdown_two( $taxonomy, $orderby = 'date', $order = 'DESC', $limit = '-1', $name, $show_option_all = null, $show_option_none = null ) {
	$args = array(
		'orderby' => $orderby,
		'order' => $order,
		'number' => $limit,
	);
	$terms = get_terms( $taxonomy, $args );
	$name = ( $name ) ? $name : $taxonomy;
	if ( $terms ) {
		printf( '<select name="%s" class="postform">', esc_attr( $name ) );
		if ( $show_option_all ) {
			printf( '<option value="0">%s</option>', esc_html( $show_option_all ) );
		}
		if ( $show_option_none ) {
			printf( '<option value="-1">%s</option>', esc_html( $show_option_none ) );
		}
		foreach ( $terms as $term ) {
			printf( '<option value="%s">%s</option>', esc_attr( $term->slug ), esc_html( $term->name ) );
		}
		print( '</select>' );
	}
}

/* -------------------------------------------------------
    Make the site private
------------------------------------------------------- */
function is_local(){
  $whitelist = array('127.0.0.1');
  return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
}

function is_login_page() {
  return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

if (isset($_GET["importer"])) {
  require "importer.php";
}
