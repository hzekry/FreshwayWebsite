<?php
/**
 *
 * TemplateToaster functions and definitions
 *
 * @package TemplateToaster
 */
$iscontactform = True;
$isplugincollection = False;
if($iscontactform || $isplugincollection)
{
require_once (dirname( __FILE__ ) . '/class-tgm-plugin-activation.php');
}
require_once(ABSPATH . 'wp-admin/includes/file.php');
ob_start();
global $TemplateToaster_classes_post, $TemplateToaster_cssprefix, $TemplateToaster_theme_widget_args;
$TemplateToaster_classes_post = array(
    'ttr_post'
);

/**
 * TemplateToaster functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, TemplateToaster_theme_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_TemplateToaster_theme_setup' );
 * function my_child_TemplateToaster_theme_setup() {
 *     ...
 * }
 * </code>
 *
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */

global $TemplateToaster_theme_widget_args;

if (!isset($content_width))
    $content_width = 900;

global $impt;

// add_action('after_switch_theme', 'TemplateToaster_import_option');
if (!function_exists('TemplateToaster_import_option')) :
    function TemplateToaster_import_option(){
	 add_option( 'is_import', '0' ); 
	}
	endif; 
	
// add_action('switch_theme', 'TemplateToaster_setup_import_options');

function TemplateToaster_setup_import_options () {
  delete_option('is_import'); 
}
/**
 * Tell WordPress to run TemplateToaster_theme_setup() when the 'after_setup_theme' hook is run.
 */

if (!function_exists('TemplateToaster_setup')) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function TemplateToaster_setup()
    {   
	
     /* 
            * Make theme available for translation.
            * Translations can be filed in the /languages/ directory.
            * If you're building a theme based on TemplateToaster, use a find and replace
            * to change 'TemplateToaster' to the name of your theme in all the template files
	 */
	 	
        load_theme_textdomain("theme--00316", get_template_directory() . '/languages');
        require_once(get_template_directory() . '/theme-options.php');
        global $TemplateToaster_options, $TemplateToaster_cssprefix, $TemplateToaster_classes_post,$impt;
       // $TemplateToaster_options = get_option('TemplateToaster_theme_options');       // Priya:- shift to inside the TemplateToaster_theme_option function.
		/*$seomode = TemplateToaster_theme_option('ttr_seo_enable');
	       if($seomode)
	       {
		   	add_filter('wp_title', 'TemplateToaster_wp_title', 10, 2);
		   }
		   else
		   {*/
		   		add_theme_support("title-tag");
		   // }
        require_once(get_template_directory() . '/widgetinit.php');
        require_once(get_template_directory() . '/custommenu.php');
        require_once(get_template_directory() . '/loginform.php');
        require_once(get_template_directory() . '/shortcodes.php');
        /* include 'seo.php';
        if (extension_loaded('zip')) {
		        include 'backup_recovery.php';
		    }*/
        $TemplateToaster_classes_post = array(
            $TemplateToaster_cssprefix . 'post'
        );

        $fileName = get_template_directory() . '/content/imports.php';
        if (file_exists($fileName)) {
	        $http = is_ssl()? "https:" : "http:";
            $current_url = $http . "//" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $theme_page_url = admin_url( 'themes.php?activated=true' );
            if( $current_url === $theme_page_url ) {
                add_action( 'admin_notices', 'my_admin_notice' );
            }	
        }

		// Push data in required plugins array for contact form 7 plugin if used in theme.
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
		$plugins = array();
		$iscontactform = True;
 		if($iscontactform)
 		{
 			array_push($plugins, array(
					'name'               => 'Contact Form 7',
					'slug'               => 'contact-form-7',
					'source'             => 'https://downloads.wordpress.org/plugin/contact-form-7.latest-stable.zip',
					'required'           => true,
					'force_deactivation' => true,
                ));
 		}
 		
		// push Recommended Plugins array.
		$recommended_plugins = array();
		if ( ! empty( $recommended_plugins ) ) {
			foreach ( $recommended_plugins as $single ) {
				array_push( $plugins, $single );
			}
		}
        
        // Add alert message for required plugins.
        if ( ! empty( $plugins ) )
        {
            add_action( 'tgmpa_register',
            function() use ( $plugins ) {
                TemplateToaster_require_plugins( $plugins ); });
        }
 		
        // This theme styles the visual editor with editor-style.css to match the theme style.
        add_editor_style();

        // Load up our theme options page and related code.

        // Add default posts and comments RSS feed links to <head>.
        add_theme_support('automatic-feed-links');    

        /*
        * Enable support for Post Thumbnails on posts and pages.
        *
        * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
        */
        add_theme_support('post-thumbnails');
        register_nav_menus(array(
            'primary' => __('Menu', "theme--00316"),
        ));
		
        // Add support for a variety of post formats

        add_theme_support('post-formats', array(
            'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video'
        ));

        add_filter('use_default_gallery_style', '__return_false');

        // Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
        register_default_headers(array(
            'wheel' => array(
                'url' => '%s/images/headers/wheel.jpg',
                'thumbnail_url' => '%s/images/headers/wheel-thumbnail.jpg',
                /* translators: header image description */
                'description' => __('Wheel', "theme--00316")
            ),
            'shore' => array(
                'url' => '%s/images/headers/shore.jpg',
                'thumbnail_url' => '%s/images/headers/shore-thumbnail.jpg',
                /* translators: header image description */
                'description' => __('Shore', "theme--00316")
            ),
            'trolley' => array(
                'url' => '%s/images/headers/trolley.jpg',
                'thumbnail_url' => '%s/images/headers/trolley-thumbnail.jpg',
                /* translators: header image description */
                'description' => __('Trolley', "theme--00316")
            ),
            'pine-cone' => array(
                'url' => '%s/images/headers/pine-cone.jpg',
                'thumbnail_url' => '%s/images/headers/pine-cone-thumbnail.jpg',
                /* translators: header image description */
                'description' => __('Pine Cone', "theme--00316")
            ),
            'chessboard' => array(
                'url' => '%s/images/headers/chessboard.jpg',
                'thumbnail_url' => '%s/images/headers/chessboard-thumbnail.jpg',
                /* translators: header image description */
                'description' => __('Chessboard', "theme--00316")
            ),
            'lanterns' => array(
                'url' => '%s/images/headers/lanterns.jpg',
                'thumbnail_url' => '%s/images/headers/lanterns-thumbnail.jpg',
                /* translators: header image description */
                'description' => __('Lanterns', "theme--00316")
            ),
            'willow' => array(
                'url' => '%s/images/headers/willow.jpg',
                'thumbnail_url' => '%s/images/headers/willow-thumbnail.jpg',
                /* translators: header image description */
                'description' => __('Willow', "theme--00316")
            ),
            'hanoi' => array(
                'url' => '%s/images/headers/hanoi.jpg',
                'thumbnail_url' => '%s/images/headers/hanoi-thumbnail.jpg',
                /* translators: header image description */
                'description' => __('Hanoi Plant', "theme--00316")
            )
        ));        
    }
endif; // TemplateToaster_setup
add_action('after_setup_theme', 'TemplateToaster_setup');

if (function_exists('is_woocommerce')) 
{
global $post, $product;
	add_action( 'after_setup_theme', 'setup_woocommerce_support' );
	add_theme_support( 'wc-product-gallery-zoom' ); 
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	function setup_woocommerce_support()
	{
	  add_theme_support('woocommerce');
	}
	
	add_filter( 'woocommerce_single_product_image_gallery_classes', 'tt_add_productimage_class');
	function tt_add_productimage_class($class)
	{
		$columns = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
		$class = array(	'pb-left-column', 
						'col-xl-6',
						'col-lg-6',
						'col-sm-12',
						'col-md-6',
						'col-xs-12',
						'col-12',
						'woocommerce-product-gallery',
						'woocommerce-product-gallery--' . ( has_post_thumbnail() ? 'with-images' : 'without-images' ),
						'woocommerce-product-gallery--columns-' . absint( $columns ),
						'images',);
				return $class;
	}
	
	function tt_woocommerce_sale_flash($label) { 
	$label = '<span class="label label-primary sale">' . __( 'Sale!', "theme--00316") .'</span>';
	return $label;    
	}; 
	add_filter( 'woocommerce_sale_flash', 'tt_woocommerce_sale_flash');
	add_filter('after_switch_theme', 'shopcolumn');
	
	
	add_filter('woocommerce_cart_item_name','productLink',10, 3);
    $cartid = wc_get_page_id( 'cart' );
    update_post_meta($cartid, '_wp_page_template', 'page-templates/shop_page.php');
    $chckout =  wc_get_page_id( 'checkout' );
    update_post_meta($chckout, '_wp_page_template', 'page-templates/shop_page.php');
    $myaccount =  get_option('woocommerce_myaccount_page_id');
    update_post_meta($myaccount, '_wp_page_template', 'page-templates/shop_page.php');
    
   function woocommerce_before_shop_loop_itemCallback(){ 
   global  $TemplateToaster_cssprefix; ?>
<ul class="<?php echo $TemplateToaster_cssprefix ?>post"><li class="<?php echo $TemplateToaster_cssprefix ?>post_content_inner"><ul class="<?php echo $TemplateToaster_cssprefix ?>article grid"><li ><?php } ?>
<?php function woocommerce_after_shop_loop_itemCallback() { ?>
</li></ul></li></ul><?php } ?>
<?php 
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
				add_action('woocommerce_after_shop_loop_item','TemplateToaster_woo_loop_add_to_cart');
				if(!function_exists( 'TemplateToaster_woo_loop_add_to_cart')) {
				function TemplateToaster_woo_loop_add_to_cart( $args = array() ) {
				global $product;
				echo'<div class="add-to-cart" >';
				if ( $product ) {
				$defaults = array(
				'quantity' => 1,
				'class'    => implode( ' ', array_filter( array(
				'~btn_type',
				'product_type_' . $product->get_type(),
				$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
				$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : ''
				) ) ), // add this to solve woocommerece product add to cart issue on page refresh
                'attributes' => array(
					'data-product_id'  => $product->get_id(),
				),
				);
				$args = apply_filters( 'woocommerce_loop_add_to_cart_args', wp_parse_args( $args, $defaults ), $product );
				wc_get_template( 'loop/add-to-cart.php', $args );
				} echo'</div>';
				} }
				remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
				add_action( 'woocommerce_shop_loop_item_title', 'templatetoster_woocommerce_product_title', 10 );
				if (  ! function_exists( 'templatetoster_woocommerce_product_title' ) ) {
            function templatetoster_woocommerce_product_title() {
            global  $TemplateToaster_cssprefix;
            echo'<div class="' . $TemplateToaster_cssprefix . 'post_inner_box">';
            echo'<div style="height:0px; width:0px; overflow:hidden; -webkit-margin-top-collapse: separate;"></div>';
             echo'<h2 class="' . $TemplateToaster_cssprefix . 'post_title">';
            echo '<a href="' . get_the_permalink() . '">' . get_the_title() . '</a>';
            echo'</h2>';
            echo'<div style="height:0px; width:0px; overflow:hidden; -webkit-margin-top-collapse: separate;"></div>';
            echo'</div>';
            } }
			add_action('woocommerce_before_shop_loop_item', 'woocommerce_before_shop_loop_itemCallback',10);
	 add_action('woocommerce_after_subcategory', 'woocommerce_after_shop_loop_itemCallback',50);
			 add_action('woocommerce_before_subcategory', 'woocommerce_before_shop_loop_itemCallback',10);
	 add_action('woocommerce_after_shop_loop_item', 'woocommerce_after_shop_loop_itemCallback',50);
	 remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );
     add_action( 'woocommerce_shop_loop_subcategory_title', 'TemplateToaster_woo_loop_category_title', 10 );
     if (!function_exists( 'TemplateToaster_woo_loop_category_title')){
     function TemplateToaster_woo_loop_category_title($category) {
     global  $TemplateToaster_cssprefix;
     echo'<div class="' . $TemplateToaster_cssprefix . 'post_inner_box">';
     echo'<h2 class="' . $TemplateToaster_cssprefix . 'post_title">';
     echo $category->name;
     if ( $category->count > 0 ) {
     echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . $category->count . ')</mark>', $category );
     } echo'</h2>';
     echo'</div>';
} }
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
    add_action('woocommerce_single_product_summary', 'TemplateToaster_woo_template_single_title', 5 );
    if (!function_exists( 'TemplateToaster_woo_template_single_title')) {
    function TemplateToaster_woo_template_single_title(){
    global  $TemplateToaster_cssprefix;
    echo'<h1 class="' . $TemplateToaster_cssprefix . 'Prodes_Title">';
    echo get_the_title();
    echo'</h1>';
} }
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    add_action( 'woocommerce_single_product_summary', 'TemplateToaster_woo_template_single_price', 5 );
    if ( ! function_exists( 'TemplateToaster_woo_template_single_price' ) ) {
    function TemplateToaster_woo_template_single_price() {
    global  $TemplateToaster_cssprefix;
    echo'<div class="' . $TemplateToaster_cssprefix . 'prodes_Price">';
    wc_get_template( 'single-product/price.php' );
    echo'</div>';
} }
    remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
	
	add_action( 'woocommerce_before_shop_loop_item_title', 'TemplateToaster_product_thumbnail_wrap_open', 8, 2);
	if (!function_exists('TemplateToaster_product_thumbnail_wrap_open')) {
	function TemplateToaster_product_thumbnail_wrap_open() {
	echo '<a class="product-image" href="' . get_the_permalink() . '">';
} }

	add_action( 'woocommerce_before_shop_loop_item_title', 'TemplateToaster_product_thumbnail_wrap_close', 10, 2);
	if (!function_exists('TemplateToaster_product_thumbnail_wrap_close')) {
	function TemplateToaster_product_thumbnail_wrap_close() {
	echo '</a> <!--/.wrap-->';
} }
	add_action( 'woocommerce_before_shop_loop_item_title', 'templatetoster_woocommerce_product_wrap_open', 11 );
	if (!function_exists('templatetoster_woocommerce_product_wrap_open')) {
	function templatetoster_woocommerce_product_wrap_open() {
	echo '<div class="product-shop">';
	echo '<div class="product-shop-margin postcontent">';
} }
	add_action('woocommerce_after_shop_loop_item', 'templatetoster_woocommerce_product_wrap_close');
	if (!function_exists('templatetoster_woocommerce_product_wrap_close')) {
	function templatetoster_woocommerce_product_wrap_close() {
	echo '</div>';
	echo '</div>';
} }
add_action( 'woocommerce_before_subcategory_title', 'templatetoster_woocommerce_product_wrap_open', 11 );//priyanka:- added gap between the product title and border on shop page
add_action('woocommerce_after_subcategory_title', 'templatetoster_woocommerce_product_wrap_close');
}
function productLink( $product_name, $cart_item, $cart_item_key )
    {
    global $TemplateToaster_cssprefix ;
     $pro_link = get_permalink($cart_item['product_id']);
         return sprintf( '<a class=' . $TemplateToaster_cssprefix . 'prochec_product_link href="'.$pro_link.'">%s </a>', $product_name );
         }

	
	function shopcolumn()
	{
	update_option( 'woocommerce_catalog_columns', "~shopcolumn" );
	}
	
function my_admin_notice()
{
	// add the theme options page url to string querys
	$theme_option_url = admin_url( 'admin.php?page=mytheme-options#import_content' );
	$msg = sprintf( "You may import content under <a href='%s'>Appearences -> Theme Options -> Import Content</a>",  $theme_option_url ) ;
    echo'<div class="updated notice notice-info is-dismissible"><p>'. $msg . '</p></div>';  
}

add_action( 'wp_ajax_parseXMLContent', 'parseXMLContent' );
function parseXMLContent(){
    $fileName = get_template_directory() . '/content/imports.php';
    if (file_exists($fileName)) {
        require_once(get_template_directory() . '/content/imports.php');
        $parsed_xml = TemplateToaster_parse_xml();
        print_r($parsed_xml);
    }    
      wp_die();
}

function templatetoaster_content_import() {
$fileName = get_template_directory() . '/content/imports.php';
$imported = false;
if (file_exists($fileName) && (isset($_POST['importt']) && isset($_POST['formdata']))) {
	if (($_POST['importt'] != "")) {
	 $filterdata = array();
            foreach( $_POST['formdata']['name'] as $key => $name ){
                    if( array_key_exists($name, $filterdata) ){
                        $i++;
                        $filterdata[$name][$i] = $_POST['formdata']['values'][$key];
                    } else {
                        $i = 0;
                        $filterdata[$name][$i] = $_POST['formdata']['values'][$key];
                    }
                }
        $sidebars = array();
        if(array_key_exists('sidebar1',$filterdata) || array_key_exists('sidebar2',$filterdata)){
            $sidebar1 = array_key_exists('sidebar1',$filterdata) ? $filterdata['sidebar1'] : array();
            $sidebar2 = array_key_exists('sidebar2',$filterdata) ? $filterdata['sidebar2'] : array();
            $sidebars = array_merge($sidebar1,$sidebar2);
        }
		$filteredContent = [ 
	        'pages_info' => array_key_exists('pages',$filterdata) ? $filterdata['pages'] : array(),
	        'menu_info' => array_key_exists('menus',$filterdata) ? $filterdata['menus'] : array(),
            'sidebars_info' => $sidebars,
	        'footers_info' => array_key_exists('footers',$filterdata) ? $filterdata['footers'] : array(),
	        'media_info' => array_key_exists('media',$filterdata) ? $filterdata['media'] : array() // adding media in filter content.
	    ];
	    require_once(get_template_directory() . "/content/imports.php");
	    $imported = TemplateToaster_import_start($filteredContent);
	}
	if( $imported ) {
		 $msg = __( "Content Imported successfully.", "theme--00316" ) ;
         echo $msg; 
        $imported = false; 
	}
	else {
		 $msg = __( "Content Not Imported Sucessfully, import it again", "theme--00316") ;
            echo $msg;
            $imported = false; 
	}
	wp_die();    
 }
}
  
add_action( 'wp_ajax_templatetoaster_content_import', 'templatetoaster_content_import' );

function TemplateToaster_excerpt_length($length)
{
    return 40;
}

add_filter('excerpt_length', 'TemplateToaster_excerpt_length');

/**
 * Returns a "Continue Reading" link for excerpts
 */
function TemplateToaster_continue_reading_link()
{
    if (TemplateToaster_theme_option('ttr_read_more_button')) {
        return '<br/><br/><a href="' . esc_url(get_permalink()) . '">' . '<span class="btn btn-default">' .__( TemplateToaster_theme_option('ttr_read_more'), '', "theme--00316") . '<span class="meta-nav">&rarr;</span></span>'. '</a>';
    } else {
        return '<br/><br/><a href="' . esc_url(get_permalink()) . '">' . __(TemplateToaster_theme_option('ttr_read_more'), '', "theme--00316") . '<span class="meta-nav">&rarr;</span></a>';

    }
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and TemplateToaster_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 */
function TemplateToaster_auto_excerpt_more($more)
{
    return ' &hellip;' . TemplateToaster_continue_reading_link();
}

add_filter('excerpt_more', 'TemplateToaster_auto_excerpt_more');

/**
 * Trim the content lenght without deleting tags
 */
function TemplateToaster_trim_words( $text, $more = null) {
		$length = TemplateToaster_theme_option('ttr_read_length');
		$tokens = array();
		$out = '';
		$w = 0;
		if (null === $more)
        $more = '&hellip;';

		// Divide the string into tokens; HTML tags, or words, followed by any whitespace
		// (<[^>]+>|[^<>\s]+\s*)
		$lang = get_locale();
		if ($lang == "zh_CN")
		{       
		       preg_match_all( '/(<[^>]+>|[^<>\s]+)\s*/u', $text, $temp_token );
		    		foreach ( $temp_token[0] as $tt )
		    		 { // Parse each token
		    		   if ( $w >= $length) {  
								 break; 
								  } // Limit reached
					     	if ( $tt[0] != '<' ) { 
					   	     preg_match_all('/./u', $tt, $tokens);
					   	     foreach ( $tokens[0] as $ttt ) { // Parse each token
								if ( $w >= $length) {  
								 break; 
								  } // Limit reached
								
								  $out .= $ttt;
			                       $w++;    // Count words
			                   
	   						}
			                  		 
			              }
		      				else
		      				 {
		       					 $out .= $tt; // Append what's left of the token
			                 }
						
	   	             }
	   	             
	   	             if ($w >= $length) {  $out .=$more;  }  
		}
		else{
			preg_match_all( '/(<[^>]+>|[^<>\s]+)\s*/u', $text, $tokens );
			foreach ( $tokens[0] as $t ) { // Parse each token
			if ( $w >= $length) { // Limit reached
								break;
								}
			if ( $t[0] != '<' ) { 
			                       $w++;    // Count words
			                    }
			// Append what's left of the token
			$out .= $t;
	   	}
	   	    $a=0;
	   	 	foreach ( $tokens[0] as $t ) {
	   		if ( $t[0] != '<' ){
				$a++;
			}   
    }
           if ($a > $length) {
    			$out .=$more;}
		}
		
		return force_balance_tags( $out );
	   
		
	}


/**
 * Read more link function on enabling the tag in theme options
 */

function TemplateToaster_content_filter($content)
{
    $morelink = ' &hellip;' . TemplateToaster_continue_reading_link();
    if (TemplateToaster_theme_option('ttr_post1_enable') && !is_single() && !is_page() && empty($post->post_excerpt) && !is_feed()) {
         return TemplateToaster_trim_words($content,$more = $morelink);
		}
 else if (!empty($post->post_excerpt) && !is_single() && !is_page() && TemplateToaster_theme_option('ttr_post1_enable') && !is_feed()) {
   		 return "<p>" . $post->post_excerpt .$more = $morelink. "</p>";
		}
		 else {
        return $content;
    }
}

add_filter('the_content', 'TemplateToaster_content_filter');

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 */
function TemplateToaster_custom_excerpt_more($output)
{
    if (has_excerpt() && !is_attachment()) {
        $output .= TemplateToaster_continue_reading_link();
    }
    return $output;
}

add_filter('get_the_excerpt', 'TemplateToaster_custom_excerpt_more');

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function TemplateToaster_page_menu_args($args)
{
    $args['show_home'] = true;
    return $args;
}

add_filter('wp_page_menu_args', 'TemplateToaster_page_menu_args');

/**
 * Display navigation to next/previous pages when applicable
 */
function TemplateToaster_content_nav($nav_id)
{
    global $wp_query;

    if ($wp_query->max_num_pages > 1) : ?>
<nav id="<?php echo esc_attr($nav_id); ?>">
<?php if ( ($nav_id == 'nav-above' && TemplateToaster_theme_option('ttr_post_navigation_above')) || ($nav_id == 'nav-below' && TemplateToaster_theme_option('ttr_post_navigation_below')) ): ?>
<h3 class="assistive-text">
<?php echo(__('Navigation', "theme--00316")); ?>
</h3>
<?php endif; ?>
<?php
            if ( ($nav_id == 'nav-above' && TemplateToaster_theme_option('ttr_pagination_link_posts_above')) || ($nav_id == 'nav-below' && TemplateToaster_theme_option('ttr_pagination_link_posts_below')) ){
                global $wp_query;

                $big = 999999999;
                $pge = paginate_links(array(
                    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'prev_next' => True,
                    'format' => '?paged=%#%',
                    'prev_text' => __('Previous', "theme--00316"),
                    'next_text' => __('Next', "theme--00316"),
                    'current' => max(1, get_query_var('paged')),
                    'type' => 'array',
                    'total' => $wp_query->max_num_pages
                ));
                if ($wp_query->max_num_pages > 1) :
                    ?>
<div class="woo_pagination">
                    <ul class="pagination">
<?php
                        foreach ($pge as $page) {
                            if (strpos($page, 'current') !== false) {
                                echo '<li class="active">' . $page . '</li>';
                            } else {
                                echo '<li>' . $page . '</li>';
                            }
                        }
                        ?>
</ul>
<?php endif; ?>
</div>
<?php
            }
			if ( ($nav_id == 'nav-above' && TemplateToaster_theme_option('ttr_older_newer_posts_above')) || ($nav_id == 'nav-below' && TemplateToaster_theme_option('ttr_older_newer_posts_below')) )
			{ ?>
<div class="nav-previous">
<?php next_posts_link(__('<span class="meta-nav">&larr;</span> Older posts', "theme--00316")); ?>
</div>
  <div
      class="nav-next">
<?php previous_posts_link(__('Newer posts <span class="meta-nav">&rarr;</span>', "theme--00316")); ?>
</div>
<?php } ?>
</nav>
<!-- #nav-above -->
<?php endif;
}

/**
 * Return the URL for the first link found in the post content.
 * @return string|bool URL or false when no link is present.
 */
function TemplateToaster_url_grabber()
{
    if (!preg_match('/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', get_the_content(), $matches))
        return false;

    return esc_url_raw($matches[1]);
}

/**
 * Count the number of footer sidebars to enable dynamic classes for the footer
 */
function TemplateToaster_footer_sidebar_class()
{
    $count = 0;

    if (is_active_sidebar('sidebar-3'))
        $count++;

    if (is_active_sidebar('sidebar-4'))
        $count++;

    if (is_active_sidebar('sidebar-5'))
        $count++;

    $class = '';

    switch ($count) {
        case '1':
            $class = 'one';
            break;
        case '2':
            $class = 'two';
            break;
        case '3':
            $class = 'three';
            break;
    }

    if ($class)
        echo 'class="' . $class . '"';
}

if (!function_exists('TemplateToaster_comment')) :
    /**
     * Template for comments and pingbacks.
     *
     * To override this walker in a child theme without modifying the comments template
     * simply create your own TemplateToaster_comment(), and that function will be used instead.
     */
function TemplateToaster_comment($comment, $args, $depth)
{
        if (is_singular() && comments_open() && get_option('thread_comments'))
            wp_enqueue_script('comment-reply');
$GLOBALS['comment'] = $comment;
        switch ($comment->comment_type) :
case 'pingback' :
case 'trackback' :
?>
<li class="post pingback">
  <p>
<?php _e('Pingback:', "theme--00316"); ?>
<?php comment_author_link(); ?>
<?php edit_comment_link(__('Edit', "theme--00316"), '<span class="edit-link">', '</span>'); ?>
</p>
<?php
    break;
    default :
    ?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
<!--<div id="comment-<?php comment_ID(); ?>" class="comment">-->
<div>

    <div class="comment-author vcard">
<?php
        $avatar_size = 68;
                            if ('0' != $comment->comment_parent)
            $avatar_size = 39;

                            echo get_avatar($comment, $avatar_size);

        /* translators: 1: comment author, 2: date and time */
                            printf(__('%1$s on %2$s <span class="says">said:</span>', "theme--00316"),
                                sprintf('<span class="fn">%s</span>', get_comment_author_link()),
                                sprintf('<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
                                    esc_url(get_comment_link($comment->comment_ID)),
                                    get_comment_time('c'),
                /* translators: 1: date, 2: time */
                                    sprintf(__('%1$s at %2$s', "theme--00316"), get_comment_date(), get_comment_time())
            )
        );
        ?>
<?php edit_comment_link(__('Edit', "theme--00316"), '<span class="edit-link">', '</span>'); ?>
</div>
    <!-- .comment-author .vcard -->
<?php if ($comment->comment_approved == '0') : ?>
<em class="comment-awaiting-moderation">
<?php _e('Your comment is awaiting moderation.', "theme--00316"); ?>
</em>
<br/>
<?php endif; ?>
<!--
</footer>-->

<div class="comment-content">
<?php comment_text(); ?>
</div>

    <div class="reply">
<?php comment_reply_link(array_merge($args, array('reply_text' => __('Reply <span>&darr;</span>', "theme--00316"), 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
    </div>
    <!-- .reply -->
  </div>
  <!-- #comment-## -->
<?php
break;
endswitch;
}
endif; // ends check for TemplateToaster_comment()

if (!function_exists('TemplateToaster_entry_meta')) :
    /**
     * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
     *
     * Create your own TemplateToaster_entry_meta() to override in a child theme.
     * @return void
     */
    function TemplateToaster_entry_meta()
    {
        if (is_sticky() && is_home() && !is_paged())
            echo '<span class="featured-post">' . __('Sticky', "theme--00316") . '</span>';

        if (!has_post_format('link') && 'post' == get_post_type())
            TemplateToaster_entry_date();

        // Translators: used between list items, there is a space after the comma.
        if (!has_post_format(array('chat', 'status'))):
            $categories_list = get_the_category_list(__(', ', "theme--00316"));
            if ($categories_list) {
                if (TemplateToaster_theme_option('ttr_remove_post_category'))
                    echo '<span class="categories-links"> ' . $categories_list . ' |</span>';
            }
        endif;

        // Translators: used between list items, there is a space after the comma.
        $tag_list = get_the_tag_list('', __(', ', "theme--00316"));
        if ($tag_list) {
            echo '<span class="tags-links"> |' . $tag_list . '</span>';
        }

        // Post author
        if (!has_post_format(array('chat', 'status', 'aside', 'quote'))):
            if ('post' == get_post_type()) {
                printf('<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author"> %3$s | </a></span>',
                    esc_url(get_author_posts_url(get_the_author_meta('ID'))),
                    esc_attr(sprintf(__('View all posts by %s', "theme--00316"), get_the_author())),
                    get_the_author()
                );
            }
        endif;
    }
endif;

if (!function_exists('TemplateToaster_entry_date')) :
    /**
     * Prints HTML with date information for current post.
     * Create your own TemplateToaster_entry_date() to override in a child theme.
     * @param boolean $echo Whether to echo the date. Default true.
     * @return string The HTML-formatted post date.
     */
    function TemplateToaster_entry_date($echo = true)
    {
        if (has_post_format(array('chat', 'status')))
            $format_prefix = _x('%1$s on %2$s ', '1: post format name. 2: date', "theme--00316");
        else
            $format_prefix = '%2$s ';

        if (TemplateToaster_theme_option('ttr_remove_date')):
            $date = sprintf('<span class="date"><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a></span>',
                esc_url(get_permalink()),
                esc_attr(sprintf(__('Permalink to %s', "theme--00316"), the_title_attribute('echo=0'))),
                esc_attr(get_the_date('c')),
                esc_html(sprintf($format_prefix, get_post_format_string(get_post_format()), get_the_date()))
            );


            if (has_post_format(array('chat'))):
                if ($echo)
                    echo $date;

                return $date;
            else:
                if ($echo)
                    echo $date . '|';

                return $date . '|';
            endif;

        endif;
    }
endif;

function TemplateToaster_get_link_url()
{
    $content = get_the_content();
    $has_url = get_url_in_content($content);

    return ($has_url) ? $has_url : apply_filters('the_permalink', get_permalink());
}

if (!function_exists('TemplateToaster_posted_on')) :
    /**
     * Prints HTML with meta information for the current post-date/time and author.
     * Create your own TemplateToaster_posted_on to override in a child theme
     */
    function TemplateToaster_posted_on($date, $author)
    {
        $post_status = '';
        $time_string = '';
        $var_date = TemplateToaster_theme_option('ttr_remove_date');
        $var_author = TemplateToaster_theme_option('ttr_remove_author_name');
        $post_url =  urldecode(esc_url( get_permalink() ) );
        
            echo '<div class="postedon">';
            if (is_sticky() && is_home() && !is_paged()) {
                echo '<span class="featured-post"></span>';
                echo '<span style="clear:both;">' . __('Sticky', "theme--00316") . '</span>';
            }
            if ( get_the_time() !== get_the_modified_time() ) 
			{
				if ($date && $author)
				{
					if ($var_date && $var_author)
					{
						$time_string = '<a href="' . $post_url . '" rel="bookmark"><img alt="' . __('date', "theme--00316") . '" src="' . get_template_directory_uri() . '/images/datebutton.png"/><time class="updated" datetime="%3$s">%4$s</time><time class="entry-date post_updated published hidden d-none" datetime="%1$s">%2$s</time></a><span class = "meta">  ' . __('by ', "theme--00316") . ' </span><span class="author vcard">  <img alt="' . __('author ', "theme--00316") . '" src="' . get_template_directory_uri() . '/images/authorbutton.png"/>  <a href="%5$s" title="%6$s" class="url fn n" rel="author">%7$s</a></span>';
					   $post_status= __('Updated on ', "theme--00316");
					}
					else if ($var_author)
					{
					  $time_string = '<span class = "meta">  ' . __('by ', "theme--00316") . ' </span><span class="author vcard">  <img alt="' . __('author ', "theme--00316") . '" src="' . get_template_directory_uri() . '/images/authorbutton.png"/>  <a href="%5$s" title="%6$s" class="url fn n" rel="author">%7$s</a></span>';
					}
					else if ($var_date)
					{
					   $time_string = '<img alt="' . __('date', "theme--00316") . '" src="' . get_template_directory_uri() . '/images/datebutton.png"/><time class="updated" datetime="%3$s">%4$s</time><time class="entry-date post_updated published hidden d-none" datetime="%1$s">%2$s</time>';
					  $post_status= __('Updated on ', "theme--00316");
					}
				}
				else if ($date && !$author) 
				{
					if ($var_date && $var_author)
					{
						$time_string = '<a href="' . $post_url . '" rel="bookmark"><img alt="' . __('date', "theme--00316") . '" src="' . get_template_directory_uri() . '/images/datebutton.png"/><time class="updated" datetime="%3$s">%4$s</time><time class="entry-date post_updated published hidden d-none" datetime="%1$s">%2$s</time></a><span class = "meta">  ' . __('by ', "theme--00316") . ' </span><span class="author vcard"> <a href="%5$s" title="%6$s" class="url fn n" rel="author">%7$s</a></span>';
					   $post_status= __('Updated on ', "theme--00316");
					}
					else if ($var_author)
					{
					  $time_string = '<span class = "meta">  ' . __('by ', "theme--00316") . ' </span><span class="author vcard"><a href="%5$s" title="%6$s" class="url fn n" rel="author">%7$s</a></span>';
					}
					else if ($var_date)
					{
					   $time_string = '<img alt="' . __('date', "theme--00316") . '" src="' . get_template_directory_uri() . '/images/datebutton.png"/><time class="updated" datetime="%3$s">%4$s</time><time class="entry-date post_updated published hidden d-none" datetime="%1$s">%2$s</time>';
					  $post_status= __('Updated on ', "theme--00316");
					}
				}
				else if (!$date && $author)
				{
					if ($var_date && $var_author)
					{
						$time_string = '<a href="' . $post_url . '" rel="bookmark"><time class="updated" datetime="%3$s">%4$s</time><time class="entry-date post_updated published hidden d-none" datetime="%1$s">%2$s</time></a><span class = "meta">  ' . __('by ', "theme--00316") . ' </span><span class="author vcard">  <img alt="' . __('author ', "theme--00316") . '" src="' . get_template_directory_uri() . '/images/authorbutton.png"/>  <a href="%5$s" title="%6$s" class="url fn n" rel="author">%7$s</a></span>';
					   $post_status= __('Updated on ', "theme--00316");
					}
					else if ($var_author)
					{
					  $time_string = '<span class = "meta">  ' . __('by ', "theme--00316") . ' </span><span class="author vcard">  <img alt="' . __('author ', "theme--00316") . '" src="' . get_template_directory_uri() . '/images/authorbutton.png"/>  <a href="%5$s" title="%6$s" class="url fn n" rel="author">%7$s</a></span>';
					}
					else if ($var_date)
					{
					   $time_string = '<time class="updated" datetime="%3$s">%4$s</time><time class="entry-date post_updated published hidden d-none" datetime="%1$s">%2$s</time>';
					  $post_status= __('Updated on ', "theme--00316");
					}
				}
				else
				{
					if ($var_date && $var_author)
					{
						$time_string = '<a href="' . $post_url . '" rel="bookmark"><time class="updated" datetime="%3$s">%4$s</time><time class="entry-date post_updated published hidden d-none" datetime="%1$s">%2$s</time></a><span class = "meta">  ' . __('by ', "theme--00316") . ' </span><span class="author vcard"><a href="%5$s" title="%6$s" class="url fn n" rel="author">%7$s</a></span>';
					   $post_status= __('Updated on ', "theme--00316");
					}
					else if ($var_author)
					{
					  $time_string = '<span class = "meta">  ' . __('by ', "theme--00316") . ' </span><span class="author vcard"><a href="%5$s" title="%6$s" class="url fn n" rel="author">%7$s</a></span>';
					}
					else if ($var_date)
					{
					   $time_string = '<time class="updated" datetime="%3$s">%4$s</time><time class="entry-date post_updated published hidden d-none" datetime="%1$s">%2$s</time>';
					  $post_status= __('Updated on ', "theme--00316");
					}	
				}
			}
			else
			{
				if ($date && $author)
				{
					if ($var_date && $var_author)
					{
						$time_string='<a href="' . $post_url . '" rel="bookmark"><img alt="' . __('date', "theme--00316") . '" src="' . get_template_directory_uri() . '/images/datebutton.png"/><time class="entry-date published" datetime="%1$s">%2$s</time><time class="post_published updated hidden d-none" datetime="%3$s">%4$s</time></a><span class = "meta">  ' . __('by ', "theme--00316") . ' </span> <span class="author vcard"><img alt="' . __('author ', "theme--00316") . '" src="' . get_template_directory_uri() . '/images/authorbutton.png"/>   <a href="%5$s" title="%6$s" class="url fn n" rel="author">%7$s</a></span>';
					$post_status=  __('Posted on ', "theme--00316");
					}
					else if ($var_author)
					{
					  $time_string = '<span class = "meta">  ' . __('by ', "theme--00316") . ' </span><span class="author vcard">  <img alt="' . __('author ', "theme--00316") . '" src="' . get_template_directory_uri() . '/images/authorbutton.png"/>  <a href="%5$s" title="%6$s" class="url fn n" rel="author">%7$s</a></span>';
					}
					else if ($var_date)
					{
					   $time_string='<img alt="' . __('date', "theme--00316") . '" src="' . get_template_directory_uri() . '/images/datebutton.png"/><time class="entry-date published" datetime="%1$s">%2$s</time><time class="post_published updated hidden d-none" datetime="%3$s">%4$s</time>';
					$post_status=  __('Posted on ', "theme--00316");
					}
				}
				else if ($date && !$author)
				{
					if ($var_date && $var_author)
					{
						$time_string='<a href="' . $post_url . '" rel="bookmark"><img alt="' . __('date', "theme--00316") . '" src="' . get_template_directory_uri() . '/images/datebutton.png"/><time class="entry-date published" datetime="%1$s">%2$s</time><time class="post_published updated hidden d-none" datetime="%3$s">%4$s</time></a><span class = "meta">  ' . __('by ', "theme--00316") . ' </span> <span class="author vcard"><a href="%5$s" title="%6$s" class="url fn n" rel="author">%7$s</a></span>';
					$post_status=  __('Posted on ', "theme--00316");
					}
					else if ($var_author)
					{
					  $time_string = '<span class = "meta">  ' . __('by ', "theme--00316") . ' </span><span class="author vcard"><a href="%5$s" title="%6$s" class="url fn n" rel="author">%7$s</a></span>';
					}
					else if ($var_date)
					{
					   $time_string='<img alt="' . __('date', "theme--00316") . '" src="' . get_template_directory_uri() . '/images/datebutton.png"/><time class="entry-date published" datetime="%1$s">%2$s</time><time class="post_published updated hidden d-none" datetime="%3$s">%4$s</time>';
					$post_status=  __('Posted on ', "theme--00316");
					}
				}
				elseif (!$date && $author)
				{
					if ($var_date && $var_author)
					{
						$time_string='<a href="' . $post_url . '" rel="bookmark"><time class="entry-date published" datetime="%1$s">%2$s</time><time class="post_published updated hidden d-none" datetime="%3$s">%4$s</time></a><span class = "meta">  ' . __('by ', "theme--00316") . ' </span> <span class="author vcard"><img alt="' . __('author ', "theme--00316") . '" src="' . get_template_directory_uri() . '/images/authorbutton.png"/>   <a href="%5$s" title="%6$s" class="url fn n" rel="author">%7$s</a></span>';
					$post_status=  __('Posted on ', "theme--00316");
					}
					else if ($var_author)
					{
					  $time_string = '<span class = "meta">  ' . __('by ', "theme--00316") . ' </span><span class="author vcard">  <img alt="' . __('author ', "theme--00316") . '" src="' . get_template_directory_uri() . '/images/authorbutton.png"/>  <a href="%5$s" title="%6$s" class="url fn n" rel="author">%7$s</a></span>';
					}
					else if ($var_date)
					{
					   $time_string='<time class="entry-date published" datetime="%1$s">%2$s</time><time class="post_published updated hidden d-none" datetime="%3$s">%4$s</time>';
					$post_status=  __('Posted on ', "theme--00316");
					}
				}
				else
				{
					if ($var_date && $var_author)
					{
						$time_string='<a href="' . $post_url . '" rel="bookmark"><time class="entry-date published" datetime="%1$s">%2$s</time><time class="post_published updated hidden d-none" datetime="%3$s">%4$s</time></a><span class = "meta">  ' . __('by ', "theme--00316") . ' </span> <span class="author vcard"><a href="%5$s" title="%6$s" class="url fn n" rel="author">%7$s</a></span>';
					$post_status=  __('Posted on ', "theme--00316");
					}
					else if ($var_author)
					{
					  $time_string = '<span class = "meta">  ' . __('by ', "theme--00316") . ' </span><span class="author vcard"><a href="%5$s" title="%6$s" class="url fn n" rel="author">%7$s</a></span>';
					}
					else if ($var_date)
					{
					   $time_string='<time class="entry-date published" datetime="%1$s">%2$s</time><time class="post_published updated hidden d-none" datetime="%3$s">%4$s</time>';
					$post_status=  __('Posted on ', "theme--00316");
					}
				}
			}
			
			$time_string = sprintf( $time_string,
					esc_attr(get_the_date( 'c' )),
					esc_html(get_the_date()),
					esc_attr(get_the_modified_date( 'c' )),
					esc_html(get_the_modified_date()),
					esc_url(get_author_posts_url(get_the_author_meta('ID'))),
					sprintf(esc_attr__('View all posts by %s', "theme--00316"), esc_html(get_the_author())),
					esc_html(get_the_author())
					);
					
					
			// Wrap the time string in a link, and preface it with 'Posted on' or 'Updated On'.
			printf(__( '<span class="meta">%s</span>%s', 'theme--00316' ),$post_status,$time_string );
           
            echo '</div>';
    }
endif;

/**
 * Adds two classes to the array of body classes.
 * The first is if the site has only had one author with published posts.
 * The second is if a singular post being displayed
 */

 function TemplateToaster_body_classes($classes)
{

    if (!is_multi_author()) {
        $classes[] = 'single-author';
    }

    if (is_singular() && !is_home() && !is_page_template('showcase.php') && !is_page_template('sidebar-page.php'))
        $classes[] = 'singular';

    return $classes;
}

add_filter('body_class', 'TemplateToaster_body_classes');

function TemplateToaster_theme_curPageURL()
{
    $pageURL = 'http';
    if (!empty($_SERVER['HTTPS'])) {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "81" && $_SERVER["SERVER_PORT"] != "443") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

function TemplateToaster_theme_getsubmenu($menu_items, $parent)
{
    $submenu = array(); // all menu items under $menuID

    foreach ($menu_items as $key => $item) {
        if ($item->menu_item_parent == $parent->ID) {

            $submenu[] = $item;

        }
    }
    return $submenu;
}

// No longer need this function
/* function TemplateToaster_generate_menu( $meenu, $TemplateToaster_magmenu, $TemplateToaster_menuh, $TemplateToaster_vmenuh, $TemplateToaster_ocmenu, $TemplateToaster_cssprefix = "ttr_")
{
	global $TemplateToaster_justify;
    $output = '';
    if (is_front_page()) {
        $output .= '<li class="' . $TemplateToaster_cssprefix . $meenu . '_items_parent dropdown active"><a href="' . get_home_url(null, '/') . '" class="' . $TemplateToaster_cssprefix . $meenu . '_items_parent_link_active"><span class="menuchildicon"></span>' . __('Home', "theme--00316");
       
	  //  $output .= ('</a><hr class="horiz_separator" />'); 
		
        if ($TemplateToaster_justify) {
              $output .= ('<hr class="horiz_separator" /> </a>');

        } else {
              $output .= ('</a><hr class="horiz_separator" />');
             }
						
        $output .= '</li>';
    } else {
        $output .= '<li class="' . $TemplateToaster_cssprefix . $meenu . '_items_parent dropdown"><a href="' . get_home_url(null, '/') . '" class="' . $TemplateToaster_cssprefix . $meenu . '_items_parent_link"><span class="menuchildicon"></span>' . __('Home', "theme--00316");
       
	   // $output .= ('</a><hr class="horiz_separator" />'); 
		
        if ($TemplateToaster_justify) {
              $output .= ('<hr class="horiz_separator" /> </a>');

        } else {
              $output .= ('</a><hr class="horiz_separator" />');
             }
		
        $output .= '</li>';
    }

    $pages = get_pages(array('child_of' => 0, 'hierarchical' => 0, 'parent' => 0, 'sort_column' => 'menu_order,post_title'));
  
   $count = count($pages);

    $count2 = 0;
    foreach ($pages as $key => $pagg) {
        if ($pagg->post_parent == 0)
                continue;
				$count2++;		
	    }
    $count1 = 0;

    foreach ($pages as $key => $pagg) {
        $childs = get_pages(array('child_of' => $pagg->ID, 'hierarchical' => 0, 'parent' => $pagg->ID, 'sort_column' => 'menu_order,post_title'));

         $count1++;
		 
        if (empty($childs)) {
            if (home_url() != untrailingslashit(get_permalink($pagg->ID))) {

                if (get_permalink() === get_permalink($pagg->ID)) {
                    $output .= '<li class="' . $TemplateToaster_cssprefix . $meenu . '_items_parent dropdown active"><a href="' . get_permalink($pagg->ID) . '" class="' . $TemplateToaster_cssprefix . $meenu . '_items_parent_link_active"><span class="menuchildicon"></span>' . $pagg->post_title;
				
				// 
              //   if ($key != ($count - 1))
                    //    $output .= ('<hr class="horiz_separator" />'); 
				     
						
                    if ($count1 != $count2) {
                        if ($TemplateToaster_justify) {
                            $output .= ('<hr class="horiz_separator" /> </a>');

                        } else {
                            $output .= ('</a><hr class="horiz_separator" />');
                        }

                    } else {
					 $output .= ('</a>');
					}        

					$output .= '</li>';
                } else if (function_exists('TemplateToaster_woocommerce_get_page_id') && (int)TemplateToaster_woocommerce_get_page_id('shop') === $pagg->ID && is_shop()) {
                    $shop_page = (int)TemplateToaster_woocommerce_get_page_id('shop');
                    if ($shop_page === $pagg->ID) {
                        $output .= '<li class="' . $TemplateToaster_cssprefix . $meenu . '_items_parent dropdown active"><a href="' . get_permalink($pagg->ID) . '" class="' . $TemplateToaster_cssprefix . $meenu . '_items_parent_link_active"><span class="menuchildicon"></span>' . $pagg->post_title;
                     
				 //  if ($key != ($count - 1))
                   //      $output .= ('<hr class="horiz_separator" />'); 
				      
							
                        if ($count1 != $count2) {
                            if ($TemplateToaster_justify) {
                            $output .= ('<hr class="horiz_separator" /> </a>');

                            } else {
                            $output .= ('</a><hr class="horiz_separator" />');
                        }

                        } else {
					 $output .= ('</a>');
					}
						
                        $output .= '</li>';
                   }

                } else {
                    $output .= '<li class="' . $TemplateToaster_cssprefix . $meenu . '_items_parent dropdown"><a href="' . get_permalink($pagg->ID) . '" class="' . $TemplateToaster_cssprefix . $meenu . '_items_parent_link"><span class="menuchildicon"></span>' . $pagg->post_title;
                   
				   // if ($key != ($count - 1))
                      //  $output .= ('<hr class="horiz_separator" />'); 
					    
					
                    if ($count1 != $count2) {
                        if ($TemplateToaster_justify) {
                            $output .= ('<hr class="horiz_separator" /> </a>');

                        } else {
                            $output .= ('</a><hr class="horiz_separator" />');
                        }

                    } else {
					 $output .= ('</a>');
					}
					
                    $output .= '</li>';
                }
            }
        } else {
            if (home_url() != untrailingslashit(get_permalink($pagg->ID))) {
                if (get_permalink() === get_permalink($pagg->ID)) {
                    $output .= '<li class="' . $TemplateToaster_cssprefix . $meenu . '_items_parent dropdown active"><a href="' . get_permalink($pagg->ID) . '" class="' . $TemplateToaster_cssprefix . $meenu . '_items_parent_link_active_arrow dropdown-toggle" data-toggle="dropdown" ><span class="menuchildicon"></span>' . $pagg->post_title;
                } else if (function_exists('TemplateToaster_woocommerce_get_page_id') && (int)TemplateToaster_woocommerce_get_page_id('shop') === $pagg->ID && is_shop()) {
                    $shop_page = (int)TemplateToaster_woocommerce_get_page_id('shop');
                
                    if ($shop_page === $pagg->ID) {
                        $output .= '<li class="' . $TemplateToaster_cssprefix . $meenu . '_items_parent dropdown active"><a href="' . get_permalink($pagg->ID) . '" class="' . $TemplateToaster_cssprefix . $meenu . '_items_parent_link_active_arrow dropdown-toggle" data-toggle="dropdown" ><span class="menuchildicon"></span>' . $pagg->post_title;
                    }

                } else {
                    $output .= '<li class="' . $TemplateToaster_cssprefix . $meenu . '_items_parent dropdown"><a href="' . get_permalink($pagg->ID) . '" class="' . $TemplateToaster_cssprefix . $meenu . '_items_parent_link_arrow dropdown-toggle" data-toggle="dropdown" ><span class="menuchildicon"></span>' . $pagg->post_title;
                }
                }
           
		   // if ($key != ($count - 1))
               // $output .= ('<hr class="horiz_separator" />'); 
				
				
            if ($count1 != $count2) {
                if ($TemplateToaster_justify) {
                            $output .= ('<hr class="horiz_separator" /></a>');

                } else {
                            $output .= ('</a><hr class="horiz_separator" />');
                        }

            } else {
					 $output .= ('</a>');
					}		
					
            $output .= TemplateToaster_generate_level1_children($childs, $meenu, $TemplateToaster_magmenu, $TemplateToaster_menuh, $TemplateToaster_vmenuh);
            $output .= '</li>';
        }
    }

    return $output;
} */

/*
Edit the widgets which are not exported from TemplateToaster
*/

function filter_dynamic_sidebar_params( $sidebar_params ) {
  if ( is_admin() ) {
        return $sidebar_params;
    }
$widget_id = $sidebar_params[0]['widget_id'];
 $sidebar_params[0]['after_widget'] = str_replace('~tt', '', $sidebar_params[0]['after_widget']);
return $sidebar_params;
}
add_filter( 'dynamic_sidebar_params', 'filter_dynamic_sidebar_params' );   
                   
/**
 * Prints HTML with meta information for the current post-date/time and author.
 * Create your own TemplateToaster_posted_on to override in a child theme
 */

function TemplateToaster_theme_dynamic_sidebar($index)
{
 global $wp_registered_sidebars, $wp_registered_widgets, $TemplateToaster_cssprefix, $params, $menuclass;
    $heading_tag = TemplateToaster_theme_option('ttr_heading_tag_block');
    if(empty($heading_tag) || $heading_tag == Null){
 	$heading_tag = "h3";
 	}
    /*if ($heading_tag == 'choice1')
        $heading_tag = 'h1';
    elseif ($heading_tag == 'choice2')
        $heading_tag = 'h2';
    elseif ($heading_tag == 'choice3')
        $heading_tag = 'h3';
    elseif ($heading_tag == 'choice4')
        $heading_tag = 'h4';
    elseif ($heading_tag == 'choice5')
        $heading_tag = 'h5';
    elseif ($heading_tag == 'choice6')
        $heading_tag = 'h6';*/
  
    if (is_int($index)) {
        $index = "sidebar-$index";
       $i = 0;
    } else {
       $i = 0;
        $index = sanitize_title($index);
        foreach ((array)$wp_registered_sidebars as $key => $value) {
            if (sanitize_title($value['name']) == $index) {
                $index = $key;
                break;
            }
        }
    }

	$TemplateToaster_sidebars_widgets = wp_get_sidebars_widgets();
    if (empty($TemplateToaster_sidebars_widgets))
	        return false;

    if (empty($wp_registered_sidebars[$index]) || !array_key_exists($index, $TemplateToaster_sidebars_widgets) || !is_array($TemplateToaster_sidebars_widgets[$index]) || empty($TemplateToaster_sidebars_widgets[$index]))
        return false;

    $sidebar = $wp_registered_sidebars[$index];

    ob_start();
    if (!dynamic_sidebar($index)) {
        return FALSE;
    }
    $sidebarcontent = ob_get_clean();

    $data = explode("~tt", $sidebarcontent);

    foreach ((array)$TemplateToaster_sidebars_widgets[$index] as $id) {
      if(empty($wp_registered_widgets[$id]['name']) || is_null($wp_registered_widgets[$id]['name']))
        	{
        		continue;
        	}    
        $params = array_merge(
            array(array_merge((array)$sidebar, array('widget_id' => $id, 'widget_name' => $wp_registered_widgets[$id]['name']))),
            (array)$wp_registered_widgets[$id]['params']);
        if (!isset($data[$i])) {
            continue;
        }

        $classname_ = '';
        foreach ((array)$wp_registered_widgets[$id]['classname'] as $cn) {
            if (is_string($cn))
                $classname_ .= '_' . $cn;
            elseif (is_object($cn))
                $classname_ .= '_' . get_class($cn);
        }
        $classname_ = ltrim($classname_, '_');
        $params[0]['before_widget'] = sprintf($params[0]['before_widget'], $id, $classname_);
        $params = apply_filters('dynamic_sidebar_params', $params);

        $widget =  $data[$i];
		$i++;
       
        if (!is_string($widget) || strlen(str_replace(array('&nbsp;', ' ', "\n", "\r", "\t"), '', $widget)) == 0) continue;
        if (strlen(str_replace(array('&nbsp;', ' ', "\n", "\r", "\t"), '', $params[0]['before_title'])) == 0) {
            $widget = preg_replace('#(\'\').*?(' . $params[0]['after_title'] . ')#', '$1$2', $widget);
        }

        $pos = strpos($widget, $params[0]['after_title']);

        $widget_id = $params[0]['widget_id'];

        $widget_obj = $wp_registered_widgets[$widget_id];

        $widget_opt = get_option($widget_obj['callback'][0]->option_name);

        $widget_num = $widget_obj['params'][0]['number'];

        if (isset($widget_opt[$widget_num]['style'])) {
            $style = $widget_opt[$widget_num]['style'];
        } else
            $style = '';

        if ($style == "block") {
            if ($pos === FALSE) {

                $widget = str_replace($params[0]['before_widget'], '<div class = "' . $TemplateToaster_cssprefix . 'block"> <div class="remove_collapsing_margins"></div>
			<div class = "' . $TemplateToaster_cssprefix . 'block_without_header"> </div> <div id="' . $widget_id . '" class="' . $TemplateToaster_cssprefix . 'block_content">', $widget);
            } else {
                $widget = str_replace($params[0]['before_widget'], '<div class="' . $TemplateToaster_cssprefix . 'block"><div class="remove_collapsing_margins"></div> <div class="' . $TemplateToaster_cssprefix . 'block_header">', $widget);
            }
           $params[0]['after_widget'] = str_replace('~tt', '', $params[0]['after_widget']);
            $widget = str_replace($params[0]['after_widget'], '</div></div>', $widget);
            $widget = str_replace($params[0]['after_title'], '</' . $heading_tag . '></div> <div id="' . $widget_id . '" class="' . $TemplateToaster_cssprefix . 'block_content">', $widget);
            $widget = str_replace($params[0]['before_title'], '<' . $heading_tag . ' style="color:' . TemplateToaster_theme_option('ttr_blockheading') . '; font-size:' . TemplateToaster_theme_option('ttr_font_size_block') . 'px;" class="' . $TemplateToaster_cssprefix . 'block_heading">', $widget);
        } else if ($style == "none") {
            $classname_ = '';
            foreach ((array)$wp_registered_widgets[$id]['classname'] as $cn) {
                if (is_string($cn))
                    $classname_ .= '_' . $cn;
                elseif (is_object($cn))
                    $classname_ .= '_' . get_class($cn);
            }
            $classname_ = ltrim($classname_, '_');
            $widget = str_replace($params[0]['before_widget'], sprintf('<aside id="%1$s" class="widget %2$s">', $id, $classname_), $widget);
            $params[0]['after_widget'] = str_replace('~tt', '', $params[0]['after_widget']);
            $widget = str_replace($params[0]['after_widget'], '</aside>', $widget);
            $widget = str_replace($params[0]['after_title'], '</h3>', $widget);
            $widget = str_replace($params[0]['before_title'], '<h3 class="widget-title">', $widget);
        } else {
            if ($index == 'sidebar-1' || $index == 'sidebar-2') {

                if ($pos === FALSE) {

                    $widget = str_replace($params[0]['before_widget'], '<div class = "' . $TemplateToaster_cssprefix . 'block"> <div class="remove_collapsing_margins"></div>
			<div class = "' . $TemplateToaster_cssprefix . 'block_without_header"> </div> <div id="' . $widget_id . '" class="' . $TemplateToaster_cssprefix . 'block_content">', $widget);
                }
            }
        }

        echo $widget;

    }

    return true;
}

function TemplateToaster_theme_comment_form($args = array(), $post_id = null)
{
    global $user_identity, $id;
    global $TemplateToaster_cssprefix;
    
    if (null === $post_id)
        $post_id = $id;
    else
        $id = $post_id;

    $commenter = wp_get_current_commenter();

    $req = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');
    $fields = array(
        'author' => '<p class="comment-form-author">' . '<label for="author">' . __('Name', "theme--00316") . '</label> ' . ($req ? '<span class="required">*</span>' : '') . '<br/>' .
            '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' /></p>',
        'email' => '<p class="comment-form-email"><label for="email">' . __('Email', "theme--00316") . '</label> ' . ($req ? '<span class="required">*</span>' : '') . '<br/>' .
            '<input id="email" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . ' /></p>',
        'url' => '<p class="comment-form-url"><label for="url">' . __('Website', "theme--00316") . '</label>' . '<br/>' .
            '<input id="url" name="url" type="text" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" /></p>',
    );

    $required_text = sprintf(' ' . __('Required fields are marked %s', "theme--00316"), '<span class="required">*</span>');
    $defaults = array(
        'fields' => apply_filters('comment_form_default_fields', $fields),
        'comment_field' => '<p class="comment-form-comment"><label for="comment">' . _x('Comment', 'noun', "theme--00316") . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>' . '<br/>',
        'must_log_in' => '<p class="must-log-in">' . sprintf(__('You must be <a href="%s">logged in</a> to post a comment.', "theme--00316"), wp_login_url(apply_filters('the_permalink', get_permalink($post_id)))) . '</p>',
        'logged_in_as' => '<p class="logged-in-as">' . sprintf(__('Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', "theme--00316"), admin_url('profile.php'), $user_identity, wp_logout_url(apply_filters('the_permalink', get_permalink($post_id)))) . '</p>',
        'comment_notes_before' => '<p class="comment-notes">' . __('Your email address will not be published.', "theme--00316") . ($req ? $required_text : '') . '</p>',
        'comment_notes_after' => '<p class="form-allowed-tags">' . sprintf(__('You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s', "theme--00316"), ' <code>' . allowed_tags() . '</code>') . '</p>',
        'id_form' => 'commentform',
        'id_submit' => 'submit',
        'title_reply' => __('Leave a Reply', "theme--00316"),
        'title_reply_to' => __('Leave a Reply to %s', "theme--00316"),
        'cancel_reply_link' => __('Cancel reply', "theme--00316"),
        'label_submit' => __('Post Comment', "theme--00316"),
    );

    $args = wp_parse_args($args, apply_filters('comment_form_defaults', $defaults));

    ?>
<?php if (comments_open()) : ?>
<?php do_action('comment_form_before'); ?>
<!--<div id="respond">-->
<?php if (TemplateToaster_theme_option('ttr_comments_form')): ?>
<div class="<?php echo esc_attr($TemplateToaster_cssprefix); ?>comment comment-respond" id="respond">
    <div class="<?php echo esc_attr($TemplateToaster_cssprefix); ?>comment_header">
      <div class="<?php echo esc_attr($TemplateToaster_cssprefix); ?>comment_header_left_border_image">
        <div class="<?php echo esc_attr($TemplateToaster_cssprefix); ?>comment_header_right_border_image">
                        </div>
                    </div>
                </div>
    <div class="<?php echo esc_attr($TemplateToaster_cssprefix); ?>comment_content">
      <div class="<?php echo esc_attr($TemplateToaster_cssprefix); ?>comment_content_left_border_image">
        <div class="<?php echo esc_attr($TemplateToaster_cssprefix); ?>comment_content_right_border_image">

          <div class="<?php echo esc_attr($TemplateToaster_cssprefix); ?>comment_content_inner">

            <h3 id="reply-title">
<?php comment_form_title($args['title_reply'], $args['title_reply_to']); ?>
<small>
<?php cancel_comment_reply_link($args['cancel_reply_link']); ?>
</small>
            </h3>
<?php if (get_option('comment_registration') && !is_user_logged_in()) : ?>
<?php echo $args['must_log_in']; ?>
<?php do_action('comment_form_must_log_in_after'); ?>
<?php else : ?>
<form action="<?php echo esc_url(site_url('/wp-comments-post.php')); ?>" method="post"
              id="<?php echo esc_attr($args['id_form']); ?>">
<?php do_action('comment_form_top'); ?>
<?php if (is_user_logged_in()) : ?>
<?php echo apply_filters('comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity); ?>
<?php do_action('comment_form_logged_in_after', $commenter, $user_identity); ?>
<?php else : ?>
<?php echo $args['comment_notes_before']; ?>
<?php
                do_action('comment_form_before_fields');
                foreach ((array)$args['fields'] as $name => $field) {
                    echo apply_filters("comment_form_field_{$name}", $field) . "\n";
                                            }
                do_action('comment_form_after_fields');
                                            ?>
<?php endif; ?>
<?php echo apply_filters('comment_form_field_comment', $args['comment_field']); ?>
<?php echo $args['comment_notes_after']; ?>
<div class="form-submit">
                <span class="<?php echo esc_attr($TemplateToaster_cssprefix); ?>button"
                  onmouseover="this.className='<?php echo esc_attr($TemplateToaster_cssprefix); ?>button_hover1';"
                  onmouseout="this.className='<?php echo esc_attr($TemplateToaster_cssprefix); ?>button';">

                  <input name="ttr_comment_submit" class="btn btn-default" type="submit"
                         id="<?php echo esc_attr($args['id_submit']); ?>"
                  value="<?php echo esc_attr($args['label_submit']); ?>"/>
							</span>

                <div style="clear: both;"></div>
<?php comment_id_fields($post_id); ?>
</div>
<?php do_action('comment_form', $post_id); ?>
</form>
<?php endif; ?>
</div>

                        </div>
                    </div>
                </div>
    <div class="<?php echo esc_attr($TemplateToaster_cssprefix); ?>comment_footer">
      <div class="<?php echo esc_attr($TemplateToaster_cssprefix); ?>comment_footer_left_border_image">
        <div class="<?php echo esc_attr($TemplateToaster_cssprefix); ?>comment_footer_right_border_image">
                        </div>
                    </div>
                </div>

                <!--	</div>--><!-- #respond -->
            </div>
<?php endif; ?>
<?php do_action('comment_form_after'); ?>
<?php else : ?>
<?php do_action('comment_form_comments_closed'); ?>
<?php endif; ?>
<?php
}

function TemplateToaster_count_sidebar_widgets($sidebar_id)
{
    $the_sidebars = wp_get_sidebars_widgets();
    if (!isset($the_sidebars[$sidebar_id]))
        return FALSE;
    else
        return count($the_sidebars[$sidebar_id]);

}

function TemplateToaster_add_init()
{
	global $post,$tt_post_type;
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_media();						// enqueue the media js, to be used at upload.js file
    wp_enqueue_style('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/smoothness/jquery-ui.css', false, '1.8.9', false);
    wp_enqueue_style('admin-style', get_template_directory_uri() . '/css/admin-style.css', array(), '1.0.0');
    wp_enqueue_script('import-script', get_template_directory_uri() . '/js/import-script.js', array(), '1.0.0', true);
    wp_localize_script( 'import-script','adminajax',array("ajaxurl"=>admin_url('admin-ajax.php')) );
    $screen = get_current_screen();
    if ($screen->id == 'appearance_page_mytheme-options') {
        wp_enqueue_style('thickbox');
        wp_register_script('upload', get_template_directory_uri() . '/js/upload.js', array('jquery', 'media-upload'));
        
        $tt_upload_data = array('title' => __('Choose or Upload an Image', "theme--00316"), 'text' => __('Use this image', "theme--00316"));
        wp_localize_script('upload','tt_upload_data',$tt_upload_data);
        wp_enqueue_script('upload');
       
       wp_register_script('addtextbox', get_template_directory_uri() . '/js/addtextbox.js', array(), 1.0, false);
        wp_enqueue_script('addtextbox', get_template_directory_uri() . '/js/addtextbox.js', array(), 1.0, false);

    }
    
   // TemplateToaster custom ediotr cases managed for Wordpress5 TT imported pages.
    $fileName = get_template_directory() . '/content/imports.php';
    if (file_exists($fileName))
    {
    if(get_bloginfo('version') <  '5.0')
    {
    $id = get_the_ID();
    $page = get_post($id, OBJECT); 
    $page_meta = get_post_meta( $id, 'tt_pageID', true );
    $tt_post_type = get_post_type($id);
    
    if (($tt_post_type == 'page') || ($tt_post_type == 'post')) 
    {
    wp_register_script('ttr_post_button', get_template_directory_uri() . '/js/post_button.js', array('jquery'), '1.0.0', false);
    wp_enqueue_script('ttr_post_button');
    }
    }
		else
		{
		$id = get_the_ID();
		$page = get_post($id, OBJECT); 
		$page_meta = get_post_meta( $id, 'tt_pageID', true );
		$tt_post_type = get_post_type($id);
		$screen = get_current_screen();
		if (($tt_post_type == 'page' && !empty($page_meta)) || ($screen->id == 'appearance_page_mytheme-options'))  {
		add_action('admin_head', 'templatetoaster_customAdmin');  // Custom script files embedded for wordpress5 for TT imported pages only
		// removed post_button.js as not required in Wordpress5.
		}
	}
    }
}
add_action('admin_enqueue_scripts', 'TemplateToaster_add_init');


function TemplateToaster_options_setup()
{
    global $pagenow;

    if ('media-upload.php' == $pagenow || 'async-upload.php' == $pagenow) {
        // Now we'll replace the 'Insert into Post Button' inside Thickbox
        add_filter('gettext', 'TemplateToaster_replace_thickbox_text', 1, 3);
    }
}

add_action('admin_init', 'TemplateToaster_options_setup');

function TemplateToaster_replace_thickbox_text($translated_text, $text, $domain)
{
    if ('Insert into Post' == $text) {
        $referer = strpos(wp_get_referer(), 'functions.php');
        if ($referer != '') {
            return __('Select this image!', "theme--00316");
        }
    }
    return $translated_text;
}

function TemplateToaster_customAdmin()
{
    global $post, $TemplateToaster_cssprefix;
    $screen = get_current_screen();
	  $postid = ( isset( $post->ID ) ? get_the_ID() : NULL );
    wp_register_script('togglebutton', get_template_directory_uri() . '/js/jquery.toggle.buttons.js', array('jquery'), '2.8.2', false);
    wp_enqueue_script('togglebutton');
    //wp_register_script('expand', get_template_directory_uri() . '/js/expand.js', array('jquery'), '1.0.0', false);
    //wp_enqueue_script('expand');
    wp_register_script('widgetform', get_template_directory_uri() . '/js/widgetform.js', array('jquery'), '1.0.0', false);
    wp_enqueue_script('widgetform');
    wp_enqueue_script('toggleButtons', get_template_directory_uri() . '/js/toggleButtons.js', array('jquery'), '1.0.0', false);
    $fileName = get_template_directory() . '/content/imports.php';
    if (file_exists($fileName)) {
    $pageClass = get_post_meta($postid, 'tt_pageClass', true);
    $passed_data = array('on' => __('ON', "theme--00316"), 'off' => __('OFF', "theme--00316"), 'pageClass' => $pageClass);
        }
        else
	  $passed_data = array('on' => __('ON', "theme--00316"), 'off' => __('OFF', "theme--00316"));
    wp_localize_script('toggleButtons', 'passed_data', $passed_data);
    wp_register_style('bootstrap', get_template_directory_uri() . '/css/bootstrap-admin.css');
    wp_enqueue_style('bootstrap');
    wp_register_style('bootstrap-toggle-buttons', get_template_directory_uri() . '/css/bootstrap-toggle-buttons.css');
    wp_enqueue_style('bootstrap-toggle-buttons');
    
   // wp_register_script('jquery_tinymce_script', get_template_directory_uri() . '/js/jquery.tinymce.min.js', array('jquery', 'jquery-ui-core'), '1.2', false);
   // wp_enqueue_script('jquery_tinymce_script');
    wp_register_style('grideditor', get_template_directory_uri() . '/css/grideditor.css');
    wp_enqueue_style('grideditor');

	// Content row and cell css for backend editor.
	$iscontent = True;
	if ($iscontent) {
	    wp_register_style('contenteditor', get_template_directory_uri() . '/css/contenteditor.css');
	    wp_enqueue_style('contenteditor');
    }
	// ul list style image css
	$islisticon = True;
	echo '<style>';
	echo '.wp-admin .editor-styles-wrapper:not(.' . $TemplateToaster_cssprefix . 'ecommerce) #' . $TemplateToaster_cssprefix . 'content ul, .wp-admin .editor-styles-wrapper ul, .wp-admin .editor-styles-wrapper:not(.' . $TemplateToaster_cssprefix . 'ecommerce) ul';
	if ($islisticon) {
		echo '{list-style-image:url('. get_stylesheet_directory_uri() . '/images/1634660648listimg.png);}';
	} else {
		echo '{list-style-image:none;}';
	}
	echo '</style>';
    }

// Custom script files check managed for Wordpress older version as for w5 embedded above based on page type condition
// enqueueing custom script for w5 for styling of post page theme options. 
add_action('admin_head', 'TemplateToaster_customAdmin');

function TemplateToaster_wordpress_breadcrumbs()
            {

    $name = __('Home', "theme--00316"); //text for the 'Home' link
    $currentBefore = '<li><span class="current">';
    $currentAfter = '</span></li>';

    if (!is_home() && !is_front_page() || is_paged()) {

        echo '<ol class="breadcrumb">';

        global $post;
        $home = home_url();
        echo get_option("ttr_breadcrumb_text");
        echo '<li><a href="' . $home . '">' . $name . '</a></li>';

        if (is_category()) {
            global $wp_query;
            $cat_obj = $wp_query->get_queried_object();
            $thisCat = $cat_obj->term_id;
            $thisCat = get_category($thisCat);
            $parentCat = get_category($thisCat->parent);
            if ($thisCat->parent != 0) echo '<li>' . (get_category_parents($parentCat, TRUE, '')) . '</li>';
            echo $currentBefore . __('Archive by category &#39;', "theme--00316");
            single_cat_title();
            echo '&#39;' . $currentAfter;

        } elseif (is_day()) {
            echo '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></li>';
            echo '<li><a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a></li>';
            echo $currentBefore . get_the_time('d') . $currentAfter;

        } elseif (is_month()) {
            echo '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></li>';
            echo $currentBefore . get_the_time('F') . $currentAfter;

        } elseif (is_year()) {
            echo $currentBefore . get_the_time('Y') . $currentAfter;

        } elseif (is_single()) {
            $cat = get_the_category();
            if (isset($cat) && !empty($cat)) {
                $cat = $cat[0];
                echo '<li>' . get_category_parents($cat, TRUE, '') . '</li>';
                echo $currentBefore;
                the_title();
                echo $currentAfter;
                }

        } elseif (is_page() && !$post->post_parent) {
            echo $currentBefore;
            the_title();
            echo $currentAfter;

        } elseif (is_page() && $post->post_parent) {
            $parent_id = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = '<li><a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
                $parent_id = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            foreach ($breadcrumbs as $crumb) echo $crumb;
            echo $currentBefore;
            the_title();
            echo $currentAfter;

        } elseif (is_search()) {
            echo $currentBefore . __('Search results for &#39;', "theme--00316") . get_search_query() . '&#39;' . $currentAfter;

        } elseif (is_tag()) {
            echo $currentBefore . __('Posts tagged &#39;', "theme--00316");
            single_tag_title();
            echo '&#39;' . $currentAfter;

        } elseif (is_author()) {
            global $author;
            $userdata = get_userdata($author);
            echo $currentBefore . __('Articles posted by', "theme--00316") . $userdata->display_name . $currentAfter;

        } elseif (is_404()) {
            echo $currentBefore . __('Error 404', "theme--00316") . $currentAfter;
		}

        if (get_query_var('paged')) {
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) echo ' (';
            echo __('Page', "theme--00316") . ' ' . get_query_var('paged');
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) echo ')';
        }

        echo '</ol>';

    }
}

add_filter('sidebars_widgets', 'TemplateToaster_sidebars_widgets');
//Add input fields(priority 5, 3 parameters)
add_action('in_widget_form', 'TemplateToaster_in_widget_form', 5, 3);
//Callback function for options update (priority 5, 3 parameters)
add_filter('widget_update_callback', 'TemplateToaster_in_widget_form_update', 5, 3);
function TemplateToaster_sidebars_widgets($sidebars)
{
    if (is_admin()) {
        return $sidebars;
    }

    global $wp_registered_widgets;

    foreach ($sidebars as $s => $sidebar) {
        if ($s == 'wp_inactive_widgets' || strpos($s, 'orphaned_widgets') === 0 || empty($sidebar)) {
            continue;
        }

        foreach ($sidebar as $w => $widget) {
            // $widget is the id of the widget
            if (!isset($wp_registered_widgets[$widget])) {
                continue;
            }

            $opts = $wp_registered_widgets[$widget];
            $id_base = is_array($opts['callback']) ? $opts['callback'][0]->id_base : $opts['callback'];

            if (!$id_base) {
                continue;
            }

            $instance = get_option('widget_' . $id_base);

            if (!$instance || !is_array($instance)) {
                continue;
            }

            if (isset($instance['_multiwidget']) && $instance['_multiwidget']) {
                $number = $opts['params'][0]['number'];
                if (!isset($instance[$number])) {
                    continue;
                }

                $instance = $instance[$number];
                unset($number);
            }

            unset($opts);

            $show = TemplateToaster_show_widget($instance);

            if (!$show) {
                unset($sidebars[$s][$w]);
            }

            unset($widget);
        }
        unset($sidebar);
    }

    return $sidebars;
}

function TemplateToaster_show_widget($instance)
{
    global $wp_query;
    $post_id = $wp_query->get_queried_object_id();

    if (is_home()) {
        $show = isset($instance['page-home']) ? ($instance['page-home']) : false;
    } else if (is_front_page()) {
        $show = isset($instance['page-front']) ? ($instance['page-front']) : false;
    } else if (is_archive()) {
        $show = isset($instance['page-archive']) ? ($instance['page-archive']) : false;
    } else if (is_single()) {
        if (function_exists('get_post_type')) {
            $type = get_post_type();
            if ($type != 'page' and $type != 'post')
                $show = isset($instance['page-' . $type]) ? ($instance['page-' . $type]) : false;
        }

        if (!isset($show))
            $show = isset($instance['page-single']) ? ($instance['page-single']) : false;
    } else if (is_404()) {
        $show = isset($instance['page-404']) ? ($instance['page-404']) : false;
    } else if ($post_id) {
        $show = isset($instance['page-' . $post_id]) ? ($instance['page-' . $post_id]) : false;
    }

    if (!isset($show))
        $show = false;

    if ($show)
        return false;

    return $instance;
}

function TemplateToaster_in_widget_form($t, $return, $instance)
{
    $instance = wp_parse_args((array)$instance, array('style' => 'default'));
    $pages = get_posts(array(
        'post_type' => 'page', 'post_status' => 'publish',
        'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'
    ));
    $wp_page_types = array(
        'front' => __('Front', "theme--00316"),
        'home' => __('Blog', "theme--00316"),
        'archive' => __('Archives', "theme--00316"),
        'single' => __('Single Post', "theme--00316"),
        '404' => __('404', "theme--00316")
    );

    ?>
<br/>
<label>
<?php echo(__('Hide widget on:', "theme--00316")); ?>
</label>
  <div class="menupagecontainer">
    <div class="<?php echo $t->get_field_id(''); ?>">
      <button onclick="select_widget(this);" id="select_button" type="button" class="check-all">
        Select All
      </button>
      <button onclick="unselect_widget(this);" id="select_button" type="button" class="uncheck-all">
        UnSelect All
      </button>
<?php foreach ($pages as $page) {
	if ( $page->ID !== (int) get_option( 'page_on_front' ) && $page->ID !== (int)get_option( 'page_for_posts' ) ){
            $instance['page-' . $page->ID] = isset($instance['page-' . $page->ID]) ? $instance['page-' . $page->ID] : false;
        ?>
<div class="menupageelement">
        <input class="<?php echo esc_attr(sanitize_html_class($t->get_field_id(''))); ?> widgetcheckbox"
        type="checkbox" <?php checked($instance['page-' . $page->ID], true) ?>
        id="<?php echo esc_attr($t->get_field_id('page-' . $page->ID)); ?>"
        name="<?php echo esc_attr($t->get_field_name('page-' . $page->ID)); ?>"/>
        <label class="widgetlabel"
               for="<?php echo esc_attr($t->get_field_id('page-' . $page->ID)); ?>"><?php echo $page->post_title ?>
</label>
        </div>
<?php } } ?>
<?php foreach ($wp_page_types as $key => $label){
        $instance['page-'.$key] = isset($instance['page-'.$key]) ? $instance['page-'.$key] : false;
        ?>
<div class="menupageelement">
        <input class="<?php echo esc_attr(sanitize_html_class($t->get_field_id(''))); ?> widgetcheckbox"
        type="checkbox" <?php checked($instance['page-' . $key], true) ?>
        id="<?php echo esc_attr($t->get_field_id('page-' . $key)); ?>"
        name="<?php echo esc_attr($t->get_field_name('page-' . $key)); ?>"/>
        <label class="widgetlabel"
               for="<?php echo esc_attr($t->get_field_id('page-' . $key)); ?>
          "><?php echo $label . ' ' . __('Page', "theme--00316") ?>
</label>
        </div>
<?php } ?>
</div>

  </div>
<?php if (!isset($instance['style']))
        $instance['style'] = null;
	?>
<label for="<?php echo $t->get_field_id('style'); ?>"><?php echo(__('Block Style:', "theme--00316")); ?>
</label>
  <select id="<?php echo $t->get_field_id('style'); ?>" name="<?php echo $t->get_field_name('style'); ?>">
    <option
      <?php selected($instance['style'], 'default'); ?>value="default"><?php echo(__('Default', "theme--00316")); ?>
</option>
    <option
      <?php selected($instance['style'], 'none'); ?>
      value="none"><?php echo(__('None', "theme--00316")); ?>
</option>
    <option
      <?php selected($instance['style'], 'block'); ?>value="block"><?php echo(__('Block', "theme--00316")); ?>
</option>
        </select>
<?php
    $retrun = null;
    return array($t, $return, $instance);
}

function TemplateToaster_in_widget_form_update($instance, $new_instance, $old_instance)
{
    $pages = get_posts(array(
        'post_type' => 'page', 'post_status' => 'publish',
        'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'
    ));
    if ($pages) {

        foreach ($pages as $page) {

            if (isset($new_instance['page-' . $page->ID])) {
                $instance['page-' . $page->ID] = 1;

            } else if (isset($instance['page-' . $page->ID]))
                unset($instance['page-' . $page->ID]);
            unset($page);
        }
    }

    foreach (array('front', 'home', 'archive', 'single', '404') as $page) {
        if (isset($new_instance['page-' . $page])) {
            $instance['page-' . $page] = 1;

        } else if (isset($instance['page-' . $page]))
            unset($instance['page-' . $page]);
    }
    $instance['style'] = $new_instance['style'];
    return $instance;
}

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */

function TemplateToaster_post_options_array()
{
    $postoptions = array(
        array("type" => "open"),
        array("name" => __("Display Post Title", "theme--00316"),
            "desc" => "Check this box if you would like to DISABLE the Post Title",
            "id" => "ttr_post_title_checkbox",
            "type" => "checkbox",
            "std" => "true"),
        array("name" => __("Enable Post link", "theme--00316"),
            "desc" => "Check this box if you would like to ENABLE the 'Post link'.",
            "id" => "ttr_post_link_enable_checkbox",
            "type" => "checkbox",
            "std" => "true"),
        array("type" => "close")
    );
    return $postoptions;
}

function TemplateToaster_page_options_array()
{
    $pageoptions = array(
        array("type" => "open"),
        array("name" => __("Display Page Title", "theme--00316"),
            "desc" => "Check this box if you would like to DISABLE the Page Title",
            "id" => "ttr_page_title_checkbox",
            "type" => "checkbox",
            "std" => "true"),
        array("name" => __("Display Footer", "theme--00316"),
            "desc" => "Check this box if you would like to DISABLE the Page Footer",
            "id" => "ttr_page_foot_checkbox",
            "type" => "checkbox",
            "std" => "true"),
        array("name" => __("Header Background Style", "theme--00316"),
            "desc" => "Select Box for Header Image size",
            "id" => "ttr_header_size_select",
            "type" => "select",
            "std" => "none",
            "options" => array(__("None", "theme--00316"), __("Fill", "theme--00316"), __("Horizontal Fill", "theme--00316"), __("Vertical Fill", "theme--00316"))),
        array("name" => __("Disable header Image Repeat", "theme--00316"),
            "desc" => "Check this box if you dont want to repeat image",
            "id" => "ttr_background_repeat_enable_checkbox",
            "type" => "checkbox",
            "std" => "true"),
        array("name" => __("Header  Background Image", "theme--00316"),
            "desc" => "Choose Header Image",
            "id" => "ttr_change_header_image_text",
            "type" => "media",
            "std" => ""),
        array("name" => __("Body Background Style", "theme--00316"),
            "desc" => "Select Box for background Image size",
            "id" => "ttr_background_size_select",
            "type" => "select",
            "std" => "none",
            "options" => array(__("None", "theme--00316"), __("Fill", "theme--00316"), __("Horizontal Fill", "theme--00316"), __("Vertical Fill", "theme--00316"))),
        array("name" => __("Disable background Image Repeat", "theme--00316"),
            "desc" => "Check this box if you dont want to repeat image",
            "id" => "ttr_header_repeat_enable_checkbox",
            "type" => "checkbox",
            "std" => "true"),
        array("name" => __("Body Background Image", "theme--00316"),
            "desc" => "Text Box for Body Background",
            "id" => "ttr_custom_style_text",
            "type" => "media",
            "std" => ""),
        array("type" => "close")
    );
    return $pageoptions;
}


function TemplateToaster_add_custom_box() {

    $screens = array( 'post', 'page' );

    foreach($screens as $screen)
    {
        add_meta_box(
            'post_page_options',
            __( 'Theme Options',"theme--00316" ),
            'TemplateToaster_custombox_in_publish',
            $screen,
            'side',
            'high',
            array(
        '__back_compat_meta_box' => false, // the meta box works in the block editor.
        )
        );
   }

}
add_action( 'add_meta_boxes', 'TemplateToaster_add_custom_box' );


add_action( 'save_post', 'TemplateToaster_save_postdata' );

function TemplateToaster_custombox_in_publish() 
	{
    global $post;
	$postid = ( isset( $post->ID ) ? get_the_ID() : NULL );
    if (  function_exists( 'TemplateToaster_page_options_array' ) )
        $pageoptions = TemplateToaster_page_options_array();
    if (  function_exists( 'TemplateToaster_post_options_array' ) )
        $postoptions = TemplateToaster_post_options_array();
    if ('page' != get_post_type($post) && 'post' != get_post_type($post)) return;

    if ('page' == get_post_type($post)):
        foreach ($pageoptions as $value) {
            switch ($value['type']) {

                case "open":
                    ?>
<table class="table table-hover table-bordered">
<?php 	break;

                case "close":
                    ?>
</table>
<?php   break;

                case "select":
                    ?><table class="table table-hover table-bordered">
                    <tr>
                        <td><h6><?php echo $value['name']; ?></h6></td>
                        <td ><select style="width:100px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"><?php foreach ($value['options'] as $option) { ?><option<?php if ( get_post_meta($postid, $value['id'], true) == $option) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?></select></td>
                    </tr>
<?php   break;

                case "checkbox":
                    ?>
<tr>
                        <td ><h6><?php echo $value['name']; ?></h6></td>

                        <td>
<?php
                            $var = get_post_meta($postid, $value['id'],true);?>
<?php if ((isset($var) && $var == 'true') || $var == '')
                            {
                                $checked = 'checked="yes"';
                            }
                            else
                            {
                                $checked = '';
                            }?>
<div class="normal-toggle-button">
                                <input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" <?php echo $checked; ?> />
                            </div>
                        </td>
                    </tr>
<?php 	break;

                case 'media': ?>
<tr>
                        <td><h6><?php echo $value['name']; ?></h6></td>
                        <td>
                            <div class="uploader">

                                <input type="text" class="upload" style="width:100px;" id="<?php echo $value['id']; ?>" name="<?php  echo $value['id']; ?>" value="<?php if ( get_post_meta($postid, $value['id'], true) != "") { echo get_post_meta($postid, $value['id'], true); }?>" />

                                <input type= "button" style="margin-top:5px;" class="button" name="<?php echo $value['id']; ?>_button" id="<?php echo $value['id']; ?>_button" value="Upload" />
                            </div>
                            <script type="text/javascript">
                                jQuery(document).ready(function()
                                {
                                    var _custom_media = true,
                                        _orig_send_attachment = wp.media.editor.send.attachment;

                                    // ADJUST THIS to match the correct button
                                    jQuery('.uploader .button').click(function(e)
                                    {
                                        var send_attachment_bkp = wp.media.editor.send.attachment;
                                        var button = jQuery(this);
                                        var id = button.attr('id').replace('_button', '');
                                        _custom_media = true;
                                        wp.media.editor.send.attachment = function(props, attachment)
                                        {
                                            if ( _custom_media )
                                            {
                                                jQuery("#"+id).val(attachment.url);
                                            } else {
                                                return _orig_send_attachment.apply( this, [props, attachment] );
                                            };
                                        }

                                        wp.media.editor.open(button);
                                        return false;
                                    });

                                    jQuery('.add_media').on('click', function()
                                    {
                                        _custom_media = false;
                                    });
                                });
                            </script>
                        </td>
                    </tr>
                    </table>
                    <?php break;
            }
        }
        ?>
<?php

    endif;
    if ('post' == get_post_type($post)):
        foreach ($postoptions as $value) {
            switch ($value['type']) {

                case "open":
                    ?>
<table class="table table-hover table-bordered">
<?php 	break;

                case "close":
                    ?>
</table>
<?php   break;

                case "select":
                    ?>
<tr>
                        <td><h6><?php echo $value['name']; ?></h6></td>
                        <td ><select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"><?php foreach ($value['options'] as $option) { ?><option<?php if ( get_post_meta($postid, $value['id'], true) == $option) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?></select></td>
                    </tr>

                    <?php   break;

                case "checkbox":
                    ?>
<tr>
                        <td ><h6><?php echo $value['name']; ?></h6></td>

                        <td>
<?php
                            $var = get_post_meta($postid, $value['id'],true);?>
<?php if ((isset($var) && $var == 'true') || $var == '')
                            {
                                $checked = 'checked="yes"';
                            }
                            else
                            {
                                $checked = '';
                            }?>
<div class="normal-toggle-button">
                                <input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" <?php echo $checked; ?> />
                            </div>
                        </td>
                    </tr>
<?php 	break;
            }
        }
        ?>
<?php
    endif;
}

function TemplateToaster_save_postdata( $post_id ) {

    if (  function_exists( 'TemplateToaster_page_options_array' ) )
        $pageoptions = TemplateToaster_page_options_array();
    if (  function_exists( 'TemplateToaster_post_options_array' ) )
        $postoptions = TemplateToaster_post_options_array();
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
        return;

    if ( isset($_POST['post_type']) &&'page' != $_POST['post_type'] && 'post' != $_POST['post_type'] )
        return;

    if (isset($_POST['post_type']) && 'post' == $_POST['post_type']):
        foreach ($postoptions as $value) {
         if (isset($value['id']))
            {
            $mydata = $_POST[$value['id']];

            if (strpos($value['id'], "checkbox") !== false)
            {
                if (isset($mydata))
                {
                    $mydata = 'true';
                }
                else
                {
                    $mydata = 'false';
                }

                update_post_meta($post_id, $value['id'], $mydata);
            }
            elseif(strpos($value['id'], "text") !== false)
            {
                update_post_meta($post_id, $value['id'], $mydata);
            }
          }
        }
    endif;

    if (isset($_POST['post_type']) && 'page' == $_POST['post_type']):
        foreach ($pageoptions as $value) {
         if (isset($value['id']))
            {
            $mydata = $_POST[$value['id']];
            if (strpos($value['id'], "checkbox") !== false)
            {
                if (isset($mydata))
                {
                    $mydata = 'true';
                }
                else
                {
                    $mydata = 'false';
                }

                update_post_meta($post_id, $value['id'], $mydata);
            }
            elseif(strpos($value['id'], "text") !== false)
            {
                update_post_meta($post_id, $value['id'], $mydata);
            }
            elseif(strpos($value['id'], "select") !== false)
            {
                update_post_meta($post_id, $value['id'], $mydata);

            }
          }
        }
    endif;
}


add_action('wp_head', 'TemplateToaster_slide_show');

function TemplateToaster_slide_show()
{
     global $TemplateToaster_no_slides, $TemplateToaster_slide_show_visible, $TemplateToaster_cssprefix, $post;
    /*if (function_exists('TemplateToaster_slideshow_options_array'))
       $slideshowoptions = TemplateToaster_slideshow_options_array();
    else
        $slideshowoptions = "";*/
   $tt_options = new TemplateToaster_Theme_Options();
	 $slideshowoptions = $tt_options->settings;
    if ($TemplateToaster_slide_show_visible):
        if (is_array($slideshowoptions)) {
            for ($i = 0; $i < $TemplateToaster_no_slides; $i++) {
        		if(array_key_exists('ttr_slide_show_image' . $i, $slideshowoptions) && $slideshowoptions['ttr_slide_show_image' . $i]['type']== 'media'){
					  $slideimage = TemplateToaster_theme_option('ttr_slide_show_image' . $i);
                            if (!empty($slideimage)) {
                            	echo "<style>";
                                if (TemplateToaster_theme_option('ttr_slide_show_image' . $i) && TemplateToaster_theme_option('ttr_horizontal_align' . $i) && TemplateToaster_theme_option('ttr_vertical_align' . $i) && TemplateToaster_theme_option('ttr_stretch' . $i)) {
                                    $stretch_option = TemplateToaster_theme_option('ttr_stretch' . $i);

                                    if (strtolower($stretch_option) == strtolower("Uniform")) {
                                    $stretch = "/ contain";
                                    } 
                                    else if (strtolower($stretch_option) == strtolower("Uniform to Fill")) {
                                    $stretch = "/ cover";
                                    } 
                                    else if (strtolower($stretch_option) == strtolower("Fill")) {
                                    $stretch = "/ 100% 100%";
                                    }
                                    else{
									 $stretch = " ";
								    }
                                    echo '#Slide' . $i . '{background:url(' . TemplateToaster_theme_option('ttr_slide_show_image' . $i) . ') no-repeat ' . TemplateToaster_theme_option('ttr_horizontal_align' . $i) . ' ' . TemplateToaster_theme_option('ttr_vertical_align' . $i) . '' . $stretch . ' !important;}';

                                } else if (TemplateToaster_theme_option('ttr_slide_show_image' . $i)) {
                                    echo '#Slide' . $i . '{background:url(' . TemplateToaster_theme_option('ttr_slide_show_image' . $i) . ') no-repeat scroll center center / 100% 100% !important;}';
                                } else {
                                    echo '#Slide' . $i . '{background:url(' . TemplateToaster_theme_option('ttr_slide_show_image' . $i) . ') no-repeat scroll center center / 100% 100% ;}';
                            }
                            echo "</style>";
							}
						}
        		}
        }
    endif;

    if (get_option('ttr_post_title_color'))
     {
         echo '<style>.'.$TemplateToaster_cssprefix.'post_title,.'.$TemplateToaster_cssprefix.'post_title a,.'.$TemplateToaster_cssprefix.'post_title a:visited{color:'. get_option('ttr_post_title_color').' !important}</style>';
     }

     if (get_option('ttr_post_title_hover_color'))
     {
         echo '<style>.'.$TemplateToaster_cssprefix.'post_title a:hover{color:'. get_option('ttr_post_title_hover_color').' !important}</style>';
     }

	$postid = ( isset( $post->ID ) ? get_the_ID() : NULL );
    if(get_post_meta($postid,"ttr_background_size_select",true)):
        $a=get_post_meta($postid,"ttr_background_size_select",true);
        switch($a)
        {
            case "Fill":

                if(get_post_meta($postid,"ttr_custom_style_text",true)):
                    echo '<style>';
                    echo 'body {';
                    echo 'background:url('.get_post_meta($postid, 'ttr_custom_style_text', true).')';
                    if(get_post_meta($postid, 'ttr_header_repeat_enable_checkbox', true)=="true")
                        echo 'no-repeat';
                    else
                        echo "repeat";
                    echo ' !important;';
                    echo 'background-size:100% 100% !important;';

                    echo ' }</style>';
                endif;
                break;

            case "Horizontal Fill":
                if(get_post_meta($postid,"ttr_custom_style_text",true)):
                    echo '<style>';
                    echo 'body {';
                    echo 'background:url('.get_post_meta($postid, 'ttr_custom_style_text', true).')!important;';
                    echo 'background-size:auto 100% !important;';
                    if(get_post_meta($postid, 'ttr_header_repeat_enable_checkbox', true)=="true")
                        echo 'background-repeat:no-repeat !important;';
                    echo '}</style>';
                endif;
                break;

            case "Vertical Fill":
                if(get_post_meta($postid,"ttr_custom_style_text",true)):
                    echo '<style>';
                    echo 'body {';
                    echo 'background:url('.get_post_meta($postid, 'ttr_custom_style_text', true).')!important;';
                    echo 'background-size:100% auto !important;';
                    if(get_post_meta($postid, 'ttr_header_repeat_enable_checkbox', true)=="true")
                        echo 'background-repeat:no-repeat !important;';
                    echo '}</style>';
                endif;
                break;

            default:
                if(get_post_meta($postid,"ttr_custom_style_text",true)):
                    echo '<style>';
                    echo 'body {';
                    echo 'background:url('.get_post_meta($postid, 'ttr_custom_style_text', true).')!important;';
                    if(get_post_meta($postid, 'ttr_header_repeat_enable_checkbox', true)=="true")
                        echo 'background-repeat:no-repeat !important;';
                    echo '}</style>';
                endif;
        }
    endif;
    if(get_post_meta($postid,"ttr_header_size_select",true)):
        $a=get_post_meta($postid,"ttr_header_size_select",true);
        switch($a)
        {
            case "Fill":
                if(get_post_meta($postid,"ttr_change_header_image_text",true)):
                    echo '<style>';
                    echo 'header #' . $TemplateToaster_cssprefix . 'header_inner{';  // To manage the priority of theme options styling.
                    echo 'background:url('.get_post_meta($postid, 'ttr_change_header_image_text', true).')!important;';
                    echo 'background-size:100% 100% !important;';
                    if(get_post_meta($postid, 'ttr_background_repeat_enable_checkbox', true)=="true")
                        echo 'background-repeat:no-repeat !important;';
                    echo '}</style>';
                endif;
                break;

            case "Horizontal Fill":
                if(get_post_meta($postid,"ttr_change_header_image_text",true)):
                    echo '<style>';
                    echo 'header #' . $TemplateToaster_cssprefix . 'header_inner{';  // To manage the priority of theme options styling.
                    echo 'background:url('.get_post_meta($postid, 'ttr_change_header_image_text', true).')!important;';
                    echo 'background-size:auto 100% !important;';
                    if(get_post_meta($postid, 'ttr_background_repeat_enable_checkbox', true)=="true")
                        echo 'background-repeat:no-repeat !important;';
                    echo '}</style>';
                endif;
                break;

            case "Vertical Fill":
                if(get_post_meta($postid,"ttr_change_header_image_text",true)):
                    echo '<style>';
                    echo 'header #' . $TemplateToaster_cssprefix . 'header_inner{';  // To manage the priority of theme options styling.
                    echo 'background:url('.get_post_meta($postid, 'ttr_change_header_image_text', true).')!important;';
                    echo 'background-size:100% auto !important;';
                    if(get_post_meta($postid, 'ttr_background_repeat_enable_checkbox', true)=="true")
                        echo 'background-repeat:no-repeat !important;';
                    echo '}</style>';
                endif;
                break;

            default:
                if(get_post_meta($postid,"ttr_change_header_image_text",true)):
                    echo '<style>';
                    echo 'header #' . $TemplateToaster_cssprefix . 'header_inner{';  // To manage the priority of theme options styling.
                    echo 'background:url('.get_post_meta($postid, 'ttr_change_header_image_text', true).')!important;';
                    if(get_post_meta($postid, 'ttr_background_repeat_enable_checkbox', true)=="true")
                        echo 'background-repeat:no-repeat !important;';
                    echo '}</style>';
                endif;
                break;
        }
    endif;

    if (TemplateToaster_theme_option('ttr_post_title_color')) {
        echo '<style>.' . $TemplateToaster_cssprefix . 'post_title,.' . $TemplateToaster_cssprefix . 'post_title a,.' . $TemplateToaster_cssprefix . 'post_title a:visited{color:' . TemplateToaster_theme_option('ttr_post_title_color') . ' !important}</style>';
    }

    if (TemplateToaster_theme_option('ttr_post_title_hover_color')) {
        echo '<style>.' . $TemplateToaster_cssprefix . 'post_title a:hover{color:' . TemplateToaster_theme_option('ttr_post_title_hover_color') . ' !important}</style>';
    }

    if (TemplateToaster_theme_option('ttr_logo_image_width') || TemplateToaster_theme_option('ttr_logo_image_height')) {
        echo '<style> @media only screen and (min-width:1024px) { .' .  $TemplateToaster_cssprefix . 'header_logo img{';

        if (TemplateToaster_theme_option('ttr_logo_image_width')) {
            echo 'width:' . TemplateToaster_theme_option('ttr_logo_image_width') . 'px !important;';
        }
        if (TemplateToaster_theme_option('ttr_logo_image_height')) {
            echo 'height:' . TemplateToaster_theme_option('ttr_logo_image_height') . 'px !important;';
        }
        echo '}}</style>';
    }

    $delimiter = TemplateToaster_theme_option('ttr_breadcrumb_text_separator');
    if (!empty($delimiter)) {
        echo '<style>.breadcrumb > li + li:before
    {
	content: "' . $delimiter . '" !important;
}</style>';
    }
    }

add_action('wp_head', 'TemplateToaster_add_custom_css');

function TemplateToaster_add_custom_css()
    {
    global $TemplateToaster_cssprefix;
    $post_title_color = TemplateToaster_theme_option("ttr_post_title_color");
    $post_title_hover_color = TemplateToaster_theme_option("ttr_post_title_hover_color");
    if (($post_title_color != '#') && !empty($post_title_color)) {
        echo '<style>.' . $TemplateToaster_cssprefix . 'post_title, .' . $TemplateToaster_cssprefix . 'post_title a,.' . $TemplateToaster_cssprefix . 'post_title a:visited{color:' . $post_title_color . ' !important}</style>';
}

    if (($post_title_hover_color != '#') && !empty($post_title_hover_color)) {
        echo '<style>.' . $TemplateToaster_cssprefix . 'post_title a:hover{color:' . $post_title_hover_color . ' !important}</style>';
    }
    $tt_custom_css = TemplateToaster_theme_option('ttr_custom_style');
    if (isset($tt_custom_css) && !empty($tt_custom_css))
        echo '<style type="text/css">' . $tt_custom_css . '</style>';

	}
add_action('wp_footer', 'TemplateToaster_add_googleanalytics');

function TemplateToaster_add_googleanalytics() {
global $TemplateToaster_cssprefix;
if(TemplateToaster_theme_option('ttr_google_analytics_enable')):
    $ga= TemplateToaster_theme_option('ttr_google_analytics');
	echo $ga;
	endif;
		
	$title = TemplateToaster_theme_option("ttr_font_size_title");
  	$title_color = TemplateToaster_theme_option("ttr_title");
  	$slogan = TemplateToaster_theme_option("ttr_font_size_slogan");
  	$slogan_color = TemplateToaster_theme_option("ttr_slogan");
   	$block_title = TemplateToaster_theme_option("ttr_font_size_block");
  	$block_title_color = TemplateToaster_theme_option("ttr_blockheading");
   	$sidebarmenu_title = TemplateToaster_theme_option("ttr_font_size_sidebarmenu");
  	$sidebarmenu_title_color = TemplateToaster_theme_option("ttr_sidebarmenuheading");
  	$copyright = TemplateToaster_theme_option("ttr_font_size_copyright");
  	$copyright_color = TemplateToaster_theme_option("ttr_copyright");
   	$designedby = TemplateToaster_theme_option("ttr_font_size_designedby");
 	$designedby_color = TemplateToaster_theme_option("ttr_designedby");
  	$designedbylink = TemplateToaster_theme_option("ttr_font_size_designedbylink");
  	$designedbylink_color = TemplateToaster_theme_option("ttr_designedbylink");
  		
if(!empty($title) || !empty($title_color) || !empty($slogan) || !empty($slogan_color) || !empty($block_title) || !empty($block_title_color) || !empty($sidebarmenu_title) || !empty($sidebarmenu_title_color) || !empty($copyright) || !empty($copyright_color) || !empty($designedby) || !empty($designedby_color) || !empty($designedbylink) || !empty($designedbylink_color)){

echo '<style type="text/css">
@media only screen
and (min-width:992px) 
{ ';

if(!empty($title) || !empty($title_color)){
echo 'header .'.$TemplateToaster_cssprefix.'title_style a, header .'.$TemplateToaster_cssprefix.'title_style a:link, header .'.$TemplateToaster_cssprefix.'title_style a:visited, header .'.$TemplateToaster_cssprefix.'_title_style a:hover{';
if(!empty($title)){
        echo   'font-size:'.$title.'px';
    }
    if(!empty($title_color)){
        echo 'color:'. $title_color;
    }
echo '}';
}

if(!empty($slogan) || !empty($slogan_color)){
echo '.'.$TemplateToaster_cssprefix.'slogan_style{';
    if(!empty($slogan)){
        echo 'font-size:'. $slogan.'px';
    }
    if(!empty($slogan_color)){   
        echo 'color:'. $slogan_color;
    }
echo '}';
}

if(!empty($block_title) || !empty($block_title_color)){
echo 'h1.'.$TemplateToaster_cssprefix.'block_heading, h2.'.$TemplateToaster_cssprefix.'block_heading, h3.'.$TemplateToaster_cssprefix.'block_heading, h4.'.$TemplateToaster_cssprefix.'block_heading, h5.'.$TemplateToaster_cssprefix.'block_heading, h6.'.$TemplateToaster_cssprefix.'block_heading, p.'.$TemplateToaster_cssprefix.'block_heading{
font-size:'. $block_title .'px;
color:'. $block_title_color.';
}';
}

if(!empty($sidebarmenu_title) || !empty($sidebarmenu_title_color)){
echo 'h1.'.$TemplateToaster_cssprefix.'verticalmenu_heading, h2.'.$TemplateToaster_cssprefix.'verticalmenu_heading, h3.'.$TemplateToaster_cssprefix.'verticalmenu_heading, h4.'.$TemplateToaster_cssprefix.'verticalmenu_heading, h5.'.$TemplateToaster_cssprefix.'verticalmenu_heading, h6.'.$TemplateToaster_cssprefix.'verticalmenu_heading, p.'.$TemplateToaster_cssprefix.'verticalmenu_heading{
font-size:'. $sidebarmenu_title .'px;
color:'. $sidebarmenu_title_color.';
}';
}

if(!empty($copyright) || !empty($copyright_color)){
echo 'footer#'.$TemplateToaster_cssprefix.'footer #'.$TemplateToaster_cssprefix.'copyright a:not(.btn) {
font-size:'. $copyright .'px;
color:'. $copyright_color.';
}';
}

if(!empty($designedby) || !empty($designedby_color)){
echo 'footer#'.$TemplateToaster_cssprefix.'footer span#'.$TemplateToaster_cssprefix.'footer_designed_by{
font-size:'. $designedby .'px;
color:'. $designedby_color.';
}';
}

if(!empty($designedbylink) || !empty($designedbylink_color)){
echo 'footer#'.$TemplateToaster_cssprefix.'footer #'.$TemplateToaster_cssprefix.'footer_designed_by_links a:not(.btn), #'.$TemplateToaster_cssprefix.'footer_designed_by_links a:link:not(.btn), #'.$TemplateToaster_cssprefix.'footer_designed_by_links a:visited:not(.btn), #'.$TemplateToaster_cssprefix.'footer_designed_by_links a:hover:not(.btn){
font-size:'. $designedbylink .'px;
color:'. $designedbylink_color.';
}';
}

echo '}
</style>';
}
}

add_action('template_redirect', 'TemplateToaster_m_mode');

function TemplateToaster_m_mode()
        {
    $mm_mode = TemplateToaster_theme_option('ttr_mm_enable');
    if (!is_admin() && $mm_mode) {
        if (!is_user_logged_in()) {
            $file = get_template_directory() . '/maintenance-mode.php';
            include($file);
            exit();

        }
    }
}

function TemplateToaster_theme_option($option)
{
	global $TemplateToaster_options, $theme_options;
	
	$TemplateToaster_options = get_option('TemplateToaster_theme_options');
	
	if (!isset($TemplateToaster_options) || !is_array($TemplateToaster_options))
	{
		   if(isset($theme_options->settings[$option]))
			{
				return $theme_options->settings[$option]['std'];
			}
			else
			{
			 return null;	
			}
	}
	else
	{
		if(isset($TemplateToaster_options[$option]))
		{
		//sanitize text field values in array
		$value = sanitize_text_field($TemplateToaster_options[$option]);
	        $value_escaped = wp_specialchars_decode($value, 'both');
			return $value_escaped;
		}
	}
}

function TemplateToaster_unset_options(){
    delete_option('TemplateToaster_theme_options');
    delete_option( 'contact_form');
}

add_action("switch_theme", "TemplateToaster_unset_options");	

function TemplateToaster_wp_title($title, $sep)
{
    if (is_feed()) {
		return $title;
    }

    global $page, $paged;

    // Add the blog name
    $title .= get_bloginfo('name', 'display');

    // Add the blog description for the home/front page.
    $site_description = get_bloginfo('description', 'display');
    if ($site_description && (is_home() || is_front_page())) {
        $title .= " $sep $site_description";
    }

    // Add a page number if necessary:
    if (($paged >= 2 || $page >= 2) && !is_404()) {
        $title .= " $sep " . sprintf(__('Page %s', "theme--00316"), max($paged, $page));
    }

	return $title;
}

/*add_action('wp_head', 'TemplateToaster_render_title');

function TemplateToaster_render_title()
{
    $seomode = TemplateToaster_theme_option('ttr_seo_enable');
    if ($seomode) {
        echo '<title>';
        $seo_enable = TemplateToaster_theme_option('ttr_seo_rewrite_titles');
        if ($seo_enable)
            TemplateToaster_rewrite_titles();
        else
            TemplateToaster_original_titles();
        echo '</title>';


        $google_webmaster = TemplateToaster_theme_option('ttr_seo_google_webmaster');
        $bing_webmaster = TemplateToaster_theme_option('ttr_seo_bing_webmaster');
        $pinterst_webmaster = TemplateToaster_theme_option('ttr_seo_pinterst_webmaster');
        if (!empty($google_webmaster))
            echo sprintf("<meta name=\"google-site-verification\" content=\"%s\"/>\n", $google_webmaster);
        if (!empty($bing_webmaster))
            echo sprintf("<meta name=\"msvalidate.01\" content=\"%s\"/>\n", $bing_webmaster);
        if (!empty($pinterst_webmaster))
            echo sprintf("<meta name=\"p:domain_verify\" content=\"%s\"/>\n", $pinterst_webmaster);
        if ((is_page() || is_single())) {
            $profile = TemplateToaster_theme_option('ttr_seo_google_plus');
            if (!empty($profile)) {
                echo '<link href="' . $profile . '" rel="author"/>';
            } */
            /* else {
                $profile = TemplateToaster_theme_option('googleplus');
                echo '<link href="' . $profile . '" rel="author" />';
            } */
       /* }

        $blog_title = get_option('blogname');
        $blog_desciprtion = get_option('blogdescription');
        $theme_path = get_template_directory_uri();
        $theme_path_content = get_template_directory_uri().'\content';
        if (is_single()) {
            if (TemplateToaster_theme_option('ttr_seo_nonindex_post')) {
                $noindex = "noindex";
            } else {
                $noindex = "index";
            }
            if (TemplateToaster_theme_option('ttr_seo_nofollow_post')) {
                $nofollow = "nofollow";
            } else {
                $nofollow = "follow";
            }
            echo sprintf("<!--Add by easy-noindex-nofollow--><meta name=\"robots\" content=\"%s, %s\"/>\n", $noindex, $nofollow);
        } else if (is_attachment()) {
            if (TemplateToaster_theme_option('ttr_seo_nonindex_media')) {
                $noindex = "noindex";
            } else {
                $noindex = "index";
            }
            if (TemplateToaster_theme_option('ttr_seo_nofollow_media')) {
                $nofollow = "nofollow";
            } else {
                $nofollow = "follow";
            }
            echo sprintf("<!--Add by easy-noindex-nofollow--><meta name=\"robots\" content=\"%s, %s\"/>\n", $noindex, $nofollow);
        } else if (is_home() || is_page() || is_paged()) {
            if (TemplateToaster_theme_option('ttr_seo_nonindex_page')) {
                $noindex = "noindex";
            } else {
                $noindex = "index";
            }
            if (TemplateToaster_theme_option('ttr_seo_nofollow_page')) {
                $nofollow = "nofollow";
            } else {
                $nofollow = "follow";
            }
            echo sprintf("<!--Add by easy-noindex-nofollow--><meta name=\"robots\" content=\"%s, %s\"/>\n", $noindex, $nofollow);
        } else if (is_date()) {
            if (TemplateToaster_theme_option('ttr_seo_noindex_date_archive')) {
                $noindex = "noindex";
            } else {
                $noindex = "index";
            }
            echo sprintf("<!--Add by easy-noindex--><meta name=\"robots\" content=\"%s\"/>\n", $noindex);
        } else if (is_author()) {
            if (TemplateToaster_theme_option('ttr_seo_noindex_author_archive')) {
                $noindex = "noindex";
            } else {
                $noindex = "index";
            }
            echo sprintf("<!--Add by easy-noindex--><meta name=\"robots\" content=\"s\"/>\n", $noindex);
        } else if (is_tag()) {
            if (TemplateToaster_theme_option('ttr_seo_noindex_tag_archive')) {
                $noindex = "noindex";
            } else {
                $noindex = "index";
            }
            echo sprintf("<!--Add by easy-noindex--><meta name=\"robots\" content=\"%s\"/>\n", $noindex);
        }
        if (is_search()) {
            if (TemplateToaster_theme_option('ttr_seo_noindex_search')) {
                $noindex = "noindex";
            } else {
                $noindex = "index";
            }
            if (TemplateToaster_theme_option('ttr_seo_nofollow_search')) {
                $nofollow = "nofollow";
            } else {
                $nofollow = "follow";
            }
            echo sprintf("<!--Add by easy-noindex-nofollow--><meta name=\"robots\" content=\"%s, %s\"/>\n", $noindex, $nofollow);
        }
        if (is_category()) {
            if (TemplateToaster_theme_option('ttr_seo_noindex_categories')) {
                $noindex = "noindex";
            } else {
                $noindex = "index";
            }
            if (TemplateToaster_theme_option('ttr_seo_nofollow_categories')) {
                $nofollow = "nofollow";
            } else {
                $nofollow = "follow";
            }
            echo sprintf("<!--Add by easy-noindex-nofollow--><meta name=\"robots\" content=\"%s, %s\"/>\n", $noindex, $nofollow);
        }
        $home_header = TemplateToaster_theme_option('ttr_seo_additional_fpage_header');
        $page_header = TemplateToaster_theme_option('ttr_seo_additional_post_header');
        $post_header = TemplateToaster_theme_option('ttr_seo_additional_page_header');
        if (is_home() && !empty($home_header)) {
            echo '<center><h1>' . $home_header . '</h1></center>';
        } else if (is_single() && !empty($page_header)) {
            echo '<center><h1>' . $page_header . '</h1></center>';
        } else if (is_page() && !empty($post_header)) {
            echo '<center><h1>' . $post_header . '</h1></center>';
        }
    } 
}*/

/*
add_filter('script_loader_tag', function ($tag, $handle) {

    if (is_admin()) {
        return $tag;
    }
    return str_replace(' src', ' defer="defer" src', $tag);

}, 10, 2);*/

function TemplateToaster_comment_call($comment, $args, $depth) {
  global $TemplateToaster_cssprefix;
    $GLOBALS['comment'] = $comment; ?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
    <div id="comment-<?php comment_ID(); ?>">
        <div class="<?php echo $TemplateToaster_cssprefix; ?>comments">
            <div class="<?php echo $TemplateToaster_cssprefix; ?>comment_author" style="width:<?php echo TemplateToaster_theme_option('ttr_avatar_size')?>px;">
<?php echo get_avatar( $comment,TemplateToaster_theme_option('ttr_avatar_size') ); ?>
</div>
            
            <div class="<?php echo $TemplateToaster_cssprefix; ?>comment_text" style="float:none;width:auto;">
<span class="url"><a class="<?php echo $TemplateToaster_cssprefix; ?>author_name" href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ) ?>"></a><?php
    echo get_comment_author_link( $comment->comment_ID ); ?>
</span>
                <time datetime="<?php echo get_comment_date("c")?>" class="comment-date">
                    <a class="<?php echo $TemplateToaster_cssprefix; ?>comment_date" href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s', "theme--00316"), get_comment_date(),  get_comment_time()) ?></a> </time>
                <hr style="color:#fff;"/>
<?php comment_text() ?>
<div style="clear:both"></div>
                <hr style="color:#fff;">
                <div style="clear:both"></div>
                <div class="<?php echo $TemplateToaster_cssprefix; ?>comment_reply_edit">
                    <span class="<?php echo $TemplateToaster_cssprefix; ?>reply_edit"><?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?></span>
                    <span class="<?php echo $TemplateToaster_cssprefix; ?>reply_edit"><?php edit_comment_link(__('(Edit)', "theme--00316")) ?></span>
                </div>
<?php if ($comment->comment_approved == '0') : ?>
<br /><em><?php _e('Your comment is awaiting approval.', "theme--00316") ?></em>
<?php endif; ?>
</div>
            <div class="<?php echo $TemplateToaster_cssprefix; ?>comment_author_right">
<?php  echo get_avatar( $comment,TemplateToaster_theme_option('ttr_avatar_size') ); ?>
</div>
            <div style="clear:both"></div>
        </div>
    </div>
<?php }
require get_template_directory() . '/templatetoaster-walker-nav-menu.php';

function add_last_child($items) {
   $item = end($items);
   $item->classes[] = 'last';
   return $items;
}

add_filter('wp_nav_menu_objects', 'add_last_child');

function custom_contact_form_class( $class ) {
  $class .= ' form-horizontal';
  return $class;
}

add_filter( 'wpcf7_form_class_attr', 'custom_contact_form_class' );

function TemplateToaster_require_plugins( $plugins )
{
$config = array( 'id' => 'TemplateToaster-tgmpa',
'default_path' => get_stylesheet_directory() . '/lib/plugins/',
'menu' => 'TemplateToaster-install-required-plugins', // menu slug
'has_notices' => true,
'dismissable' => false,
'is_automatic' => true,
'message' => '<!--Hey there.-->',
'strings' => array()
);
tgmpa( $plugins, $config );
} 

//adding logo to theme          
   function TemplateToaster_custom_logo_setup() {
    $defaults = array(
        'height'      => TemplateToaster_theme_option('ttr_logo_image_height'),
        'width'       => TemplateToaster_theme_option('ttr_logo_image_width'),
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array( 'site-title', 'site-description' ),
        
    );
    add_theme_support( 'custom-logo', $defaults );
}

// Add custom color control to theme
function theme_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'header_textcolor' )->transport  = 'refresh';
		/**
	 * Custom colors.
	 */
	$wp_customize->add_setting( 'colorscheme', array(
		'default'           => 'light',
		'transport'         => 'refresh',
		'sanitize_callback' => 'tt_sanitize_colorscheme',
	) );

	$wp_customize->add_control( 'colorscheme', array(
		'type'    => 'radio',
		'label'    => __( 'Color Scheme', 'theme--00316' ),
		'choices'  => array(
			'light'  => __( 'Light', 'theme--00316' ),
			'custom' => __( 'Custom', 'theme--00316' ),
		),
		'section'  => 'colors',
		'priority' => 5,
	) );
	
	
	$wp_customize->add_setting( 'colorscheme_hue', array(
		'default'           =>  250,
		'transport'         => 'refresh',
		'sanitize_callback' => 'absint', // The hue is stored as a positive integer.
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'colorscheme_hue', array(
		'mode' => 'hue',
		'section'  => 'colors',
		'priority' => 6,
	) ) );

    
  }

  add_action( 'customize_register', 'theme_customize_register' );

          
 function theme_get_customizer_css() {
    ob_start();
    
    if ( 'custom' !== get_theme_mod( 'colorscheme' ) ) {
		return;
	}   

    $hue = absint( get_theme_mod( 'colorscheme_hue', 250 ) );

	/**
	 * Filter default saturation level.
	 *
	 *
	 * @param int $saturation Color saturation level.
	 */
	$saturation = absint( apply_filters( 'tt_custom_colors_saturation', 80 ) );
	$reduced_saturation = ( .8 * $saturation ) . '%';
	$saturation = $saturation . '%';
    
    $text_color  = $hue;
    if ( ! empty( $text_color ) ) {
      ?>
.nav.ttr_menu_items li a.ttr_menu_items_parent_link_active,.ttr_slogan_style, .html_content p, .html_content span, .html_content h2, .html_content h1, .html_content h3, .html_content h4, .html_content h5, .html_content h6, footer#ttr_footer #ttr_footer_designed_by_links a:not(.btn), #ttr_footer_designed_by_links a:link:not(.btn), #ttr_footer_designed_by_links a:visited:not(.btn), #ttr_footer_designed_by_links a:hover:not(.btn) {
        color: <?php echo ' hsl( ' . $text_color . ', ' . $saturation . ', 20% ) /* base: #000; */'?>;
      }
<?php
    }
    
    $link_color  = $hue;
    if ( ! empty( $link_color ) ) {
      ?>
a {
        color: <?php echo ' hsl( ' . $link_color . ', ' . $saturation . ', 20% ) /* base: #000; */'?>;
        border-bottom-color: <?php echo ' hsl( ' . $link_color . ', ' . $saturation . ', 20% ) /* base: #000; */'?>;
      }
<?php
    }

    $border_color = $hue;
    if ( ! empty( $border_color ) ) {
      ?>
footer ,.ttr_block_header,<?php echo'#'?>ttr_sidebar_right <?php echo'#'?>newsletter_block_left .block_header,<?php echo'#'?>ttr_sidebar_left <?php echo'#'?>newsletter_block_left .block_header,a:not(.btn), input[type="text"], input[type="search"], input[type="password"], input[type="email"], input[type="url"], input[type="tel"], select, input[type="text"], input[type="search"], input[type="password"], input[type="email"], input[type="url"], input[type="tel"], select, input[type="number"], .input-text.qty, body #ttr_content .cart .ttr_post input.input-text, .input-group input#search, .form-search #searchbox #search_query_top {
        border-color:<?php echo ' hsl( ' . $border_color . ', ' . $saturation . ', 20% ) /* base: #000; */' ?>;
      }
<?php
    }
    
    $accent_color  = $hue;
    if ( ! empty( $accent_color ) ) {
      ?>
a:hover {
         color: <?php echo ' hsl( ' . $accent_color . ', ' . $saturation . ', 20% ) /* base: #000; */'?>;
        border-bottom-color: <?php echo ' hsl( ' . $accent_color . ', ' . $saturation . ', 20% ) /* base: #000; */'?>;
      }

      button,
      input[type="submit"] {
        background-color: <?php echo ' hsl( ' . $accent_color . ', ' . $saturation . ', 20% ) /* base: #000; */'?>;
      }
<?php
    }
    
    $sidebar_background  = $hue;
    if ( ! empty( $sidebar_background ) ) {
      ?>
.sidebar {
        background-color: <?php echo ' hsl( ' . $sidebar_background . ', ' . $saturation . ', 20% ) /* base: #000; */'?>;
      }
<?php
    }

    $css = ob_get_clean();
    return $css;
  }

// Modify our styles registration like so:

function theme_enqueue_styles() {
  $custom_css = theme_get_customizer_css();
  wp_add_inline_style( 'style', $custom_css ); // style :-  handle of existing registered stylesheet
}
    
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles',100 );

/**
 * Load dynamic logic for the customizer controls area.
 */
function tt_panels_js() {
	wp_register_script('tt-customize-controls', get_template_directory_uri().'/js/tt_customize.js', array('jquery'), '1.0.0', false);
    wp_enqueue_script('tt-customize-controls');
}
add_action( 'customize_controls_enqueue_scripts', 'tt_panels_js' );

function tt_sanitize_colorscheme( $input ) {
	$valid = array( 'light', 'dark', 'custom' );

	if ( in_array( $input, $valid, true ) ) {
		return $input;
	}
	return 'light';
}


/**
 * Set up the WordPress core custom header feature.
 *
 * @uses TemplateToaster_header_style()
 */
function TemplateToaster_custom_header_setup() {

	/**
	 * Filter custom-header support arguments.
	 *
	 *
	 * @param array $args {
	 *     An array of custom-header support arguments.
	 *
	 *     @type string $default-image     		Default image of the header.
	 *     @type string $default_text_color     Default color of the header text.
	 *     @type int    $width                  Width in pixels of the custom header image. Default 954.
	 *     @type int    $height                 Height in pixels of the custom header image. Default 1300.
	 *     @type string $wp-head-callback       Callback function used to styles the header image and text
	 *                                          displayed on the blog.
	 *     @type string $flex-height     		Flex support for height of header.
	 * }
	 */
		 
add_theme_support( 'custom-header', apply_filters( 'TemplateToaster_custom_header_args', array(
	'default-image'          => '',
	'flex-height'            => false,
	'flex-width'             => false,
	'uploads'                => true,
	'random-default'         => false,
	'header-text'            => true,
	'default-text-color'     => '',
	'wp-head-callback'       => '',
	'admin-head-callback'    => '',
	'admin-preview-callback' => ''
	) ) );
}

// add custom header image support
$header = True;
$head_logo = False;
if($header)
{
if($head_logo)
{
add_action( 'after_setup_theme', 'TemplateToaster_custom_logo_setup' );
}
add_action( 'after_setup_theme', 'TemplateToaster_custom_header_setup' );
}

function custom_video_active_callback() {
  if( !is_user_logged_in() && !is_home() ) {
    return true;
  }
  
  return false;
}
add_filter( 'is_header_video_active', 'custom_video_header_pages' );

function custom_video_header_pages( $active ) {
  if( is_home() || is_page() ) {
    return true;
  }

  return false;
}

// add custom header image support
$header_video = False;
function ttr_video_support(){
	add_theme_support( 'custom-header', array(
 	'video' => true,
  	'video-active-callback' => 'custom_video_active_callback'
	));
  }

if($header_video)
{
add_action( 'after_setup_theme', 'ttr_video_support' );
}

// updating customizer logo and css url to ttropt
function TemplateToaster_customizersetting_to_themeoption(){
global $TemplateToaster_options, $theme_options;	
$custom_logo_id = get_theme_mod( 'custom_logo' );
$logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
$TemplateToaster_options = get_option('TemplateToaster_theme_options');
$content = wp_get_custom_css();
if (!isset($TemplateToaster_options) || !is_array($TemplateToaster_options))
{
	$default_settings = array();
        foreach ($theme_options->settings as $id => $setting) {
            if ($setting['type'] != 'heading')
                $default_settings[$id] = $setting['std'];
        }

		   if(isset($theme_options->settings['ttr_logo']))
			{	 
				 if(has_custom_logo()){
				 	$default_settings['ttr_logo'] = esc_url( $logo[0]);
                    $default_settings['ttr_header_logo_enable'] = "1";
                 }
                 else{
                    $default_settings['ttr_header_logo_enable'] = "0";
                 }				 
			}
			if(isset($theme_options->settings['ttr_favicon_image']))
			{
				 if(has_site_icon()){
				 $default_settings['ttr_favicon_image'] = get_site_icon_url();
				 }
				 else{
				 $default_settings['ttr_favicon_image'] = '';
				 }
			}
			if(isset($theme_options->settings['ttr_site_title_slogan_enable']) && display_header_text())
			{
				$default_settings['ttr_site_title_slogan_enable'] = 1 ;
			}else{
				$default_settings['ttr_site_title_slogan_enable'] = 0 ;
			}
		   if(isset($theme_options->settings['ttr_custom_style']) && $content)
			{
				 $default_settings['ttr_custom_style'] .= $content;	
			}
			
			update_option( 'TemplateToaster_theme_options', $default_settings);		
	}
	else
	{
		if(array_key_exists('ttr_logo', $TemplateToaster_options))
		{		 
			if(has_custom_logo()){
				$TemplateToaster_options['ttr_logo'] = esc_url( $logo[0]);
                $TemplateToaster_options['ttr_header_logo_enable'] = "1";}
            else{
                $TemplateToaster_options['ttr_header_logo_enable'] = "0";}                        		
		}
		if(array_key_exists('ttr_favicon_image', $TemplateToaster_options))
		{
		if(has_site_icon()){
			$TemplateToaster_options['ttr_favicon_image'] = get_site_icon_url();
			 }
			 else{
			 $TemplateToaster_options['ttr_favicon_image'] = '';
			 }		
		}
		if(array_key_exists('ttr_custom_style', $TemplateToaster_options))
		{
			$TemplateToaster_options['ttr_custom_style'] = $content;	
		}
		if(display_header_text())
		{
			$TemplateToaster_options['ttr_site_title_slogan_enable'] = "1";
			$TemplateToaster_options['ttr_site_title_enable'] = "1";
			$TemplateToaster_options['ttr_site_slogan_enable'] = "1";
		}else{
			$TemplateToaster_options['ttr_site_title_slogan_enable'] = "0";
		}
		update_option( 'TemplateToaster_theme_options', $TemplateToaster_options);
	}
}
add_action( 'customize_save_after', 'TemplateToaster_customizersetting_to_themeoption' );

function templatetoaster_export_xml()
{
	$theme_options = get_option('TemplateToaster_theme_options');
	if($theme_options)
	{	
		$doc = new DOMDocument();			
		$xml = $doc->createElement('options');		
		$xml = $doc->appendChild($xml);	
			
		foreach ( $theme_options as $theme_option => $value)   
		{
			$option = $doc->createElement('option');			
			$option->appendChild($doc->createElement('name',$theme_option));
			$option->appendChild($doc->createElement('value',$value));
			$xml->appendChild($option);
		}
			
		$report = $doc->saveXML();
	}
	else
	{
        $report = "false"; 
    }
    
    echo $report;	
	die();
}
add_action('wp_ajax_export_xml','templatetoaster_export_xml');	
add_action('wp_ajax_nopriv_export_xml','templatetoaster_export_xml');	

function templatetoaster_import_xml()
{
	global $templatetoaster_options, $theme_options;	
	$tt_options = get_option('TemplateToaster_theme_options');
	
	if ($_POST['data'] != NULL)
	{
		$options = array();		
		$LoadXML = $_POST['data']['file'];
		$LoadXML1 = stripslashes($LoadXML);
		$LoadXML1 = str_replace('&rsquo;', '&#8217;', $LoadXML1);		
		$load_theme_options=simplexml_load_string($LoadXML1) or die(__('Error: Cannot create object','theme--00316'));
		
		if ($load_theme_options->option)
		{
			if ($tt_options != NULL) 
			{
				$options = $tt_options;
			}
			else
			{
				foreach($theme_options->settings as $setting => $settingvalue)
				{
					$options[$setting] = $settingvalue['std'];				
				}	
			}		
		
			foreach($load_theme_options->option as $val)
			{
				$k = (string)$val->name;	
				$v = (string)$val->value;		
						
				if(array_key_exists($k, $options))
				{
				   $options[$k] = $v;
				}
			}	
					
			$result = update_option('TemplateToaster_theme_options',$options, null);
					
			if($result)
			{
				die(__('Theme options imported successfully','theme--00316'));				
			}
			else
			{
				die(__('Problem in importing theme options.','theme--00316'));	
			}
		}
		else
		{
			die(__('Incorrect XML file.','theme--00316'));
		}	
	}
	else
	{
		die(__('Please, upload an XML file only','theme--00316'));
	}
}
add_action('wp_ajax_import_xml','templatetoaster_import_xml');	
add_action('wp_ajax_nopriv_import_xml','templatetoaster_import_xml');

// Remove shop page breadcrumb according to theme option to  on/off breadcrumb on page.
add_action( 'init', 'remove_shop_breadcrumbs' );
function remove_shop_breadcrumbs() {
	$var = templatetoaster_theme_option('ttr_page_breadcrumb');
	if(!$var)
	{
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
	}
	
}
function tt_style_load() {
	if ( ! function_exists( 'register_block_type' ) ) {
		// Gutenberg is not active.
		return;
    }
    else{
    // pass array to enqueue style for Gutenberg editor and classic editor
		add_editor_style( array(get_template_directory_uri() . '/css/bootstrap.css ', get_template_directory_uri() .'/style.css', get_template_directory_uri() . '/css/admin-style.css') );
	    add_theme_support( 'editor-styles' );
    }
}
add_action( 'after_setup_theme', 'tt_style_load' );



// uploading attachments 
 function action_add_attachment() { 
 		global $wp_filesystem;
        WP_Filesystem();
        $attachment_arr = array();
        $file_list = ttr_upload_list();
        foreach($file_list as $key => $value){
		$filename = basename($value);
		$parent_post_id = 0;
		$upload_file = wp_upload_bits($filename, null, $wp_filesystem->get_contents($value));
	if (!$upload_file['error']) {
	$wp_filetype = wp_check_filetype($filename, null );
	$test = wp_upload_dir();
	$attachment = array(
		'guid'           => $upload_file['url'],
		'post_mime_type' => $wp_filetype['type'],
		'post_parent' => $parent_post_id,
		'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
		'post_content' => '',
		'post_status' => 'inherit'
	);
	$attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $parent_post_id );
	if (!is_wp_error($attachment_id)) {
		// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		if($key != "h_video"){
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );		
			wp_update_attachment_metadata( $attachment_id,  $attachment_data );
		}		
		set_post_thumbnail( $parent_post_id, $attachment_id );
		}	
	  } 
	  $attachment_arr[$key] = $attachment_id ;
	}
	ttr_set_theme_prop($attachment_arr);
} 
 	add_action( 'after_switch_theme', 'action_add_attachment' );
  
// getting theme properties path  
   function ttr_upload_list(){
 		$path_arr = array();
 		$path = wp_upload_dir();
 		$tt_image_path = '';
 		$tt_image_dir = get_template_directory() . '/images/';
 		$tt_content_images = opendir($tt_image_dir);
        while ($tt_read_image = readdir($tt_content_images)) {
        	$file = glob($tt_image_dir."*header.*"); 
			if(!empty($file)) {    	  
            if ($tt_read_image != '.' && $tt_read_image != '..' && $tt_read_image == basename($file[0])) {
                if (!file_exists($tt_read_image)) {
                  $tt_image_path = $tt_image_dir . $tt_read_image;
                  break;   
                }
		 	  }
           }
        }     
       	    
        $tt_video_dir = get_template_directory() . '/video/HeaderVideo/';
        if(file_exists($tt_video_dir)){
			$tt_content_video = opendir($tt_video_dir);
        while ($tt_read_video = readdir($tt_content_video)) {
            if ($tt_read_video != '.' && $tt_read_video != '..') {
                if (!file_exists($tt_read_video)) {
                  $tt_video_path = $tt_video_dir . $tt_read_video;
                   break;   
                }
            }
          }
		}
        
		$tt_logo_path = get_template_directory(). '/logo.png';
		$tt_favicon_path = get_template_directory(). '/favicon.ico'; //// changed extension to small letters to solve favicon issue

		if($tt_image_path){
			$path_arr['h_image'] = $tt_image_path;
		}
		if(file_exists ($tt_logo_path)){
			$path_arr['h_logo'] = $tt_logo_path;
		}	
		if(file_exists ($tt_favicon_path)){
			$path_arr['site_icon'] = $tt_favicon_path;
		}
		if(file_exists ($tt_video_dir)){
			$path_arr['h_video'] = $tt_video_path;
		}	
		
		return $path_arr;	
	}
   
   
 // set theme properties  
 function ttr_set_theme_prop($all_attch_ids){
    						
    foreach($all_attch_ids as $key => $attach_id) {

    if($key == "h_image"){
		$header_image_data = (object) array(
				'attachment_id' => $attach_id,
				'url'           => get_permalink($attach_id),
			);
		
		set_theme_mod( 'header_image_data', $header_image_data );
       
	}  	

    if($key == "h_video"){
        set_theme_mod( 'header_video', $attach_id );
        }
    
     if($key == "h_logo"){
        set_theme_mod( 'custom_logo', $attach_id );
        }
        
    if($key == "site_icon"){
        update_option( 'site_icon', $attach_id);
       }
    }   
	return;		
	} 
	
	// storing old theme positions & widgets.
	add_action('switch_theme', 'old_theme_positions');
    function old_theme_positions () {
        update_option('tt_previous_theme',get_option( 'sidebars_widgets' ));
    }
    
    // inactive and restore widgets according to theme positions.
    add_action('after_switch_theme', 'new_theme_positions');
    function new_theme_positions () {
        // execute only if tt_previous_theme option available in db.
        if(get_option( 'tt_previous_theme' )) {
        	// get sidebars_widgets of previous theme.
	        $old_sidebars_widgets = get_option( 'tt_previous_theme' );
	        
	        // get sidebars_widgets of new theme.
	        $new_sidebars_widgets = get_option( 'sidebars_widgets' );
            
	        if(is_array($old_sidebars_widgets) && is_array($new_sidebars_widgets)){
	        $previous_widgets = array();
            foreach($old_sidebars_widgets as $old_widget_pos => $old_widgets)
            {
                foreach (is_array($old_widgets) ? $old_widgets : array() as $old_widget_id => $oldwidget)
                {
                    $oldwidget = explode("-",$oldwidget); // split widget name in which 0 for type and 1 for id.
                if (count($oldwidget) > 2)
                {
                    $oldarray = array($oldwidget[0], $oldwidget[1]);
                    $oldwidget[0] = implode("-",$oldarray);
                }
                array_push($previous_widgets, $oldwidget[0]);
                }
            }

                foreach($new_sidebars_widgets as $new_widget_pos => $new_widgets)
                {
                    foreach (is_array($new_widgets) ? $new_widgets : array() as $new_widget_id => $newwidget)
                    {
                        $newwidget = explode("-",$newwidget); // split widget name in which 0 for type and 1 for id.
                        if (count($newwidget) > 2)
                        {
                            $newarray = array($newwidget[0], $newwidget[1]);
                            $newwidget[0] = implode("-",$newarray);
                            $newwidget[1] = $newwidget[2];
                            array_pop($newwidget);
                        }
                        if ( in_array( $newwidget[0], $previous_widgets ) )
                        {
                            unset($new_sidebars_widgets[$new_widget_pos][$new_widget_id]);
                        }                        
                }
                }
                
	            // get diff of positions.
	            $diff_sidebar_positions = array_diff_key($old_sidebars_widgets,$new_sidebars_widgets);
	                    
	            // go through all diff position and move widget to wp_inactive_widgets.
	            foreach ($diff_sidebar_positions as $diff_sidebar_position => $diff_widgets) {
	                foreach (is_array($diff_widgets) ? $diff_widgets : array() as $diff_widget_id => $diff_widget) {
	                    foreach ($new_sidebars_widgets as $widgets_position => $widgets) {
	                        if (in_array($diff_widget, is_array($widgets) ? $widgets : array())){
	                            foreach ($widgets as $widget_id => $widget) {
	                                if ($diff_widget == $widget) {
	                                    unset($new_sidebars_widgets[$widgets_position][$widget_id]);
	                                }
	                            }
	                        }
	                    }
	                    if (!in_array($diff_widget, $new_sidebars_widgets['wp_inactive_widgets'])) {
	                        array_push($new_sidebars_widgets['wp_inactive_widgets'],$diff_widget);
	                    }
	                    unset($diff_sidebar_positions[$diff_sidebar_position][$diff_widget_id]);
	                }
	            }
			}
		}
		 else
        {
	        // get sidebars_widgets of new theme.
            $new_sidebars_widgets = get_option( 'sidebars_widgets' );  
            if(is_array($new_sidebars_widgets))
            {
                foreach($new_sidebars_widgets as $new_widget_pos => $new_widgets)
                {
                    foreach (is_array($new_widgets) ? $new_widgets : array() as $new_widget_id => $newwidget)
                    {
                        unset($new_sidebars_widgets[$new_widget_pos][$new_widget_id]);
                    }
                }
            }
        }
        // update sidebar_widgets to db.
	    update_option('sidebars_widgets', $new_sidebars_widgets );
	    update_option('tt_previous_theme', "" );
    }
?>