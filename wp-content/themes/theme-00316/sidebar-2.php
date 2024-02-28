<div id="ttr_sidebar_right_margin"> 
<div class="remove_collapsing_margins"></div>
<div class="ttr_sidebar_right_padding"> 
<div class="remove_collapsing_margins"></div>
<?php if(!TemplateToaster_theme_dynamic_sidebar(2)){
global $TemplateToaster_theme_widget_args;
extract($TemplateToaster_theme_widget_args);
echo ($before_widget.$before_title.__('Category','theme--00316').$after_title); ?>
<ul>
<?php wp_list_categories(); ?>
</ul>
<?php echo substr($after_widget,0,-3); ?>
<?php echo ($before_widget.$before_title.__('Archive','theme--00316').$after_title); ?>
<ul>
<?php wp_get_archives(); ?>
</ul>
<?php echo substr($after_widget,0,-3);
}
?>
<div class="remove_collapsing_margins"></div>
</div>
</div>
