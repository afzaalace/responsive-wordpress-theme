

<p><strong><?php _e('Nothing Found', 'ace_framework'); ?></strong><br/>

<?php _e('Sorry, the post you are looking for is not available. Maybe you want to perform a search?', 'ace_framework'); ?>
</p>
<?php get_search_form(); ?>



<div class='hr_invisible'></div>  

<?php _e('For best search results, mind the following suggestions:', 'ace_framework'); ?></p>
<ul class='borderlist'>
	<li><?php _e('Always double check your spelling.', 'ace_framework'); ?></li>
	<li><?php _e('Try similar keywords, for example: tablet instead of laptop.', 'ace_framework'); ?></li>
	<li><?php _e('Try using more than one keyword.', 'ace_framework'); ?></li>
</ul>

<div class='hr_invisible'></div>

<h3 class=''><?php _e('Feel like browsing some posts instead?', 'ace_framework'); ?></h3>

<?php
the_widget('ace_combo_widget', 'error404widget', array('widget_id'=>'arbitrary-instance-'.$id,
        'before_widget' => '<div class="widget ace_combo_widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ));
?>