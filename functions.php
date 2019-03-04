<?php
/*
 *  Add support for WP 3.0 features, thumbnails etc
 */
add_theme_support( 'post-thumbnails' );
add_theme_support('nav-menus');
add_theme_support('custom-background');
/*
 *  Required files for theme
 */
require_once('includes/theme-options.php');
require_once('includes/update.php');
require_once('includes/form.php'); 
/*
*   Define Javascript Files
*/
function ac_scripts()
{   
    //Get Options
    $ac_options = get_option('ac_option_name');
    $font = $ac_options['ac_font_family'];
    // Add jquery library
    wp_enqueue_script('jquery');    
    // Include script file
    wp_enqueue_script( 'ac-script', get_template_directory_uri() . '/assets/js/ac-script.js', array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'ac-mordenizr', get_template_directory_uri() . '/assets/js/modernizr-2.6.2.min.js', array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'ac_forms_js', get_template_directory_uri() . '/assets/js/form-script.js', array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'Bootstrap-Script-CDN', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.2/js/bootstrap.min.js', array('jquery'), '1.0.0', true );
    // Localize script file
    wp_localize_script( 'ac_forms_js', 'acajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce('ac_form_nonce') ) );
    // Include normalize styles and bottstrap cdn
    wp_enqueue_style( 'ac-normalize', get_template_directory_uri() . '/assets/css/normalize.css', false, '1.0.0' );
    wp_enqueue_style( 'bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css', array(), '3.3.2', 'all' );
    if( $font === 'Droid Sans'){
        wp_enqueue_style( 'droid-sans', 'http://fonts.googleapis.com/css?family=Droid+Sans:400,700' );
    }elseif( $font === 'Open Sans'){
        wp_enqueue_style( 'open-sans', 'http://fonts.googleapis.com/css?family=Open+Sans:400,700' );
    }elseif( $font === 'Lato'){
        wp_enqueue_style( 'lato', 'http://fonts.googleapis.com/css?family=Lato:400,700' );
    }elseif( $font === 'Bitter'){
        wp_enqueue_style( 'Bitter', 'http://fonts.googleapis.com/css?family=Bitter:400,700' );
    }
    // , array(), '4.3.0', 'all'
    wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );
    wp_enqueue_style( 'custom-style', get_template_directory_uri() . '/assets/css/custom.css');
    //Custom Styles Dynamic
    $main_color = $ac_options['ac_main_color_picker']; 
    $second_color = $ac_options['ac_second_color_picker']; 
    $main_cta = $ac_options['ac_main_image']; 
    $form_bg = $ac_options['ac_form_image'];
    $body_color = $ac_options['ac_font_color'];
    $footer_bg = $ac_options['ac_footer_image'];
    $caption_bg =$ac_options['ac_main_image_caption'];
    $custom_css = "
        body{
            color: {$body_color};
            font-family: '{$font}', sans-serif;
        }
        a{
            color: {$main_color};
        }
        .main-color{
            color: {$main_color};
        }
        .ac-content h1{
            color: {$main_color};
        }
        .ac-chat .chat-button{
            background: {$main_color};
        }
        #footer{
            background: url({$footer_bg});
        }
        #footer h3{
            color: {$body_color};
        }
        .img-cta{
            background: {$main_color};
        }
        .ac-caption, .footer-icon, .features h3, #menu-toggle{
            background: {$main_color} url({$caption_bg});            
        }
        .phone-number, .main-color{
            color: {$main_color};
        }
        #main-navigation ul li:hover{
            color: {$main_color};
        }
        #menu-toggle:focus{
            background-color: {$body_color};
        }
        #ac-form form{
            background: {$second_color} url({$form_bg});
        }
        #ac-form form h2{
            background: {$main_color};
        }
        #ac-form form #ac-submit{
            background: {$main_color};
        }
        .brand-style {
            border-top: 3px solid {$main_color};
        }
        #main-navigation-mobile ul > li:hover, #main-navigation ul li.click-call, #main-navigation ul li.current-menu-item {
            color: #FFF !important;
            background-color: {$main_color};
        }
        .main-title{
            color: {$main_color};
        }
        a{
            color: {$main_color};
        }
        #footer h2{
            border-bottom: 1px solid {$main_color};
        }
        .insurance-image h2{
            color: {$main_color};
        }
        @media (max-width: 1200px){
            #main-navigation-mobile ul li.click-call{
                background-color: {$main_color};
            }
        }
        ";
    wp_add_inline_style( 'custom-style', $custom_css );
}
add_action('wp_enqueue_scripts' , 'ac_scripts');
/*
*   Define Javascript Files for admin panel or Dashboard
*/
function ac_admin_assets() 
{
    //Only work if the user is admin
    if( is_admin() ) {                
        // Include Styles for admin options
        wp_enqueue_style( 'ac_admin_css', get_template_directory_uri() . '/assets/css/admin-styles.css', false, '1.0.0' );
    }
}
add_action( 'admin_enqueue_scripts', 'ac_admin_assets' );
/*
 *  Register Sidebar. If Sidebar is not registered use default in sidebar.php
 */
function ac_widgets_init() 
{
    register_sidebar( array(
        'name'          => __( 'Main Sidebar', 'acframework' ),
        'id'            => 'sidebar',
        'description'   => __( 'Widgets in this area will be shown on all posts and pages.', 'acframework' ),
        'before_widget' => '<div id="%1$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2>',
        'after_title'   => '</h2>',
    ) );
    register_sidebar(array(
        'name'          => __('Footer Left', 'acframework'),
        'id'            => 'ac-footer-left',
        'description'   => __('Left footer widget position.', 'acframework'),
        'before_widget' => '<div id="%1$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="main-color">',
        'after_title'   => '</h2>'
    ));
    register_sidebar(array(
        'name'          => __('Footer Center', 'acframework'),
        'id'            => 'ac-footer-center',
        'description'   => __('Center footer widget position.', 'acframework'),
        'before_widget' => '<div id="%1$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="main-color">',
        'after_title'   => '</h2>'
    ));
    register_sidebar( array(
        'name'          => __('Footer Right', 'acframework'),
        'id'            => 'ac-footer-right',
        'description'   => __('Right footer widget position.', 'acframework'),
        'before_widget' => '<div id="%1$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="main-color">',
        'after_title'   => '</h2>'
    ) );
    register_sidebar( array(
        'name'          => __('Upper Footer', 'acframework'),
        'id'            => 'ac-upper-footer',
        'description'   => __('Upper footer widget position.', 'acframework'),
        'before_widget' => '<div id="%1$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2>',
        'after_title'   => '</h2>'
    ) );
}
add_action( 'widgets_init', 'ac_widgets_init' );
/*
 *  Register navigation menus
 */
function register_ac_menu() 
{
  register_nav_menu( 'primary', 'Primary Menu' );
}
add_action( 'after_setup_theme', 'register_ac_menu' );
/*
 *  Remove Comments
 */
function ac_remove_comment_fields($fields) 
{
    unset($fields['url']);
    return $fields;
}
add_filter('comment_form_default_fields','ac_remove_comment_fields');

function add_search_box_to_menu( $items, $args ) {
    $ac_options = get_option('ac_option_name');

    if( $args->theme_location == 'primary' )
        return $items.'<li class="menu-header-cta click-call pull-right"><a href="tel:'. $ac_options['ac_number'] .'">Click to Call</a></li>';

    return $items;
}
add_filter('wp_nav_menu_items','add_search_box_to_menu', 10, 2);