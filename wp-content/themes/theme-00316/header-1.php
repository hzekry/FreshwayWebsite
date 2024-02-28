<?php
/**
* The header for our theme.
*
* @package TemplateToaster
*/
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<!--[if IE 7]>
<style type="text/css" media="screen">
#ttr_vmenu_items  li.ttr_vmenu_items_parent {display:inline;}
</style>
<![endif]-->
<style>
<?php $var = templatetoaster_theme_option('ttr_avatar_size');
if(!empty($var)){ 
?>
.ttr_comment_author{width : <?php echo TemplateToaster_theme_option('ttr_avatar_size')."px";?>;}
<?php 
$avtar = $var + 10;
} else {
$avtar = 10;
} ?>
.ttr_comment_text{width :calc(100% - <?php echo $avtar."px"?>);}
</style>
<?php wp_head(); ?>
</head>
<?php
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
global $TemplateToaster_cssprefix;
$theme_path_content = get_template_directory_uri().'/content';
$pageClass = strtolower(preg_replace('/_page.php$/', '', get_page_template_slug()));
if(empty($pageClass) && is_home() || is_single() || is_category() || is_archive() || is_search()){$pageClass = "blog-wp";
}
if (strrchr($pageClass, '/')) {
$pageClass = substr(strrchr($pageClass, '/'), 1);
}
?>
<body <?php body_class($pageClass); ?>> 
<?php if(function_exists('wp_body_open')) {
wp_body_open();
}?> 
<?php if (TemplateToaster_theme_option('ttr_back_to_top')): ?>
<?php $gotopng = TemplateToaster_theme_option('ttr_icon_back_to_top');?>
<div class="totopshow">
<?php if(!empty($gotopng)): ?>
<a href="#" class="back-to-top">
<img alt="<?php esc_attr_e('Back to Top', 'theme--00316'); ?>" src="<?php echo esc_url($gotopng); ?>">
</a>
<?php else : ?>
<a href="#" class="back-to-top">
<img alt="<?php esc_attr_e('Back to Top', 'theme--00316'); ?>" src="<?php echo esc_url(get_template_directory_uri()).'/images/gototop0.png';?>">
</a>
<?php endif; ?>
</div>
<div class="margin_collapsetop"></div>
<?php endif; ?>
<div class="ttr_banner_menu">
</div>
<div class="remove_collapsing_margins"></div>
<?php if ( has_nav_menu( 'primary' ) ) : ?>
<div class="navigation-top">
<div class="wrap">
<?php get_template_part( 'template-parts/navigation/navigation', 'top' ); ?>
</div><!-- .wrap -->
</div><!-- .navigation-top -->
<?php endif; ?>
<div class="ttr_banner_menu">
</div><div class="ttr_banner_header">
</div>
<div class="remove_collapsing_margins"></div>
<?php $postid = ( isset( $post->ID ) ? get_the_ID() : NULL );
$var = get_post_meta ( $postid, 'ttr_page_head_checkbox', true );
if ($var == "true" || $var == ""):?>
<header id="ttr_header"<?php if ( get_header_image() ) : ?> style="background-image: url(<?php header_image(); ?>);background-position: center;"<?php endif; ?> >
<div class="margin_collapsetop"></div>
<div id="ttr_header_inner"<?php if ( get_header_image() ) : ?> style="background-image: url(<?php echo header_image(); ?>);background-position:  center;"<?php endif; ?> >
<div class="ttr_header_element_alignment container">
<div class="ttr_images_container">
</div>
</div>
<div class="ttr_images_container">
<div class="ttr_headershape01">
<div class="html_content"><p style="text-align:Center; display:block; line-height:5.35em;"><span style="font-family:'Viga','Arial';font-size:3.714em;color:rgba(255,255,255,1);">Check out our Best Weekly Prices</span></p><p style="text-align:Center; display:block; "><span style="font-family:'Open Sans','Arial';font-weight:600;color:rgba(255,255,255,1);">Vestibulum ornare purus sapien, eu tempus turpis pellentesque vitae. In hac habitasse platea dictumst. Nunc sapien lectus, aliquam a semper ege</span></p><p style="margin:30px 0px 5px 0px;text-align:Center; display:block; "><span><a href="https://templatetoaster.com" target="_self" class="btn btn-md btn-default">Shop All Product</a></span></p></div>
</div>
<div class="ttr_headershape02">
<div class="html_content"><p style="text-align:Center; display:block; line-height:3.72em;"><span style="font-family:'Poppins','Arial';font-weight:700;font-size:2.571em;color:rgba(255,255,255,1);">ABOUT US</span></p></div>
</div>
</div>
</div>
</header>
<?php endif; ?>
<div class="ttr_banner_header">
</div><div class="margin_collapsetop"></div>
<div id="ttr_page" class="container">
