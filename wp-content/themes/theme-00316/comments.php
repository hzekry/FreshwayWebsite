<?php
/**
* The Template for displaying comments.
*
* The area of the page that contains both current comments and the comment form.
*
* @package TemplateToaster
*/
?>
<div id="comments">
<?php if ( post_password_required() ) : ?>
<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'theme--00316' ); ?></p>
</div>
<?php
return;
endif;
?>
<?php
$comment_form_show_on = TemplateToaster_theme_option('ttr_comment_list_form');
if (TemplateToaster_theme_option('ttr_comment_list_form') == 'choice2') {
TemplateToaster_theme_comment_form($TemplateToaster_cssprefix = 'ttr_');
} 
?>
<?php if(TemplateToaster_theme_option('ttr_comments_list')): ?>
<?php if ( have_comments() ) : ?>
<h2 id="comments-title">
<?php
printf( _n( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'theme--00316' ),
number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
?>
</h2>
<?php endif; ?>
<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :?>
<nav id="comment-nav-above">
<h1 class="assistive-text"><?php _e( 'Comment navigation', 'theme--00316' ); ?></h1>
<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'theme--00316' ) ); ?></div>
<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'theme--00316' ) ); ?></div>
</nav>
<?php endif;?>
<ol class="commentlist">
<?php
wp_list_comments( array(
'style'       => 'ol',
'short_ping'  => true,
'avatar_size' => TemplateToaster_theme_option('ttr_avatar_size'),
'callback' => 'TemplateToaster_comment_call',
) );
?>
</ol>
<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
?>
<nav id="comment-nav-below">
<h1 class="assistive-text"><?php _e( 'Comment navigation', 'theme--00316' ); ?></h1>
<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'theme--00316' ) ); ?></div>
<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'theme--00316' ) ); ?></div>
</nav>
<?php endif;?>
<?php
elseif ( ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) :
?>
<?php if(TemplateToaster_theme_option('ttr_comments_closed_text')){?>
<p class="nocomments"><?php _e( 'Comments are closed.', 'theme--00316' ); ?></p>
<?php } ?>
<?php endif; ?>
<?php if (TemplateToaster_theme_option('ttr_comment_list_form') == 'choice1') {
TemplateToaster_theme_comment_form($TemplateToaster_cssprefix = 'ttr_');
} ?>
</div>