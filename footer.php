			<?php 
			global $ace_config;
						
			//reset wordpress query in case we modified it
			wp_reset_query();
			
			
			//checks which colors the footer and socket have and if they are the same to the body a border for separation is added
			$extraClass 	= "";
			$body_bg		= ace_get_option('boxed') == 'boxed' ? ace_get_option('bg_color_boxed') : ace_get_option('bg_color');
			$footer 		= ace_get_option('footer_bg'); 
			$socket 		= ace_get_option('socket_bg');
			
			if($body_bg == $footer || $footer == "") $extraClass .= 'footer_border ';
			if($socket == $footer  || $socket == "") $extraClass .= 'socket_border ';


			
			 /**
			 *  The footer default dummy widgets are defined in folder includes/register-widget-area.php
			 *  If you add a widget to the appropriate widget area in your wordpress backend the 
			 *  dummy widget will be removed and replaced by the real one previously defined
			 */
			?>

			
			<!-- ####### FOOTER CONTAINER ####### -->
			<div class='container_wrap <?php echo $extraClass; ?>' id='footer'>
				<div class='container'>
				
					<?php 
					//create the footer columns by iterating  
					$columns = ace_get_option('footer_columns');
					
					$firstCol = 'first';
			        switch($columns)
			        {
			        	case 1: $class = ''; break;
			        	case 2: $class = 'one_half'; break;
			        	case 3: $class = 'one_third'; break;
			        	case 4: $class = 'one_fourth'; break;
			        	case 5: $class = 'one_fifth'; break;
			        }
					
					//display the footer widget that was defined at appearenace->widgets in the wordpress backend
					//if no widget is defined display a dummy widget, located at the bottom of includes/register-widget-area.php
					for ($i = 1; $i <= $columns; $i++)
					{
						echo "<div class='$class $firstCol'>";
						if (function_exists('dynamic_sidebar') && dynamic_sidebar('Footer - column'.$i) ) : else : ace_dummy_widget($i); endif;
						echo "</div>";
						$firstCol = "";
					}
					
					?>

					
				</div>
				
			</div>
		<!-- ####### END FOOTER CONTAINER ####### -->
		
		<!-- ####### SOCKET CONTAINER ####### -->
			<div class='container_wrap <?php echo $extraClass; ?>' id='socket'>
				<div class='container'>
					<span class='copyright'>&copy; <?php _e('Copyright','ace_framework'); ?> - <a href='<?php echo home_url('/'); ?>'><?php echo get_bloginfo('name');?></a> - <a href='http://www.afzaalace.com'>Wordpress Theme by @fzaalace</a></span>
				
					<ul class="social_bookmarks">
							<?php 
							
							//contact icon
							$contact_page_id = ace_get_option('email_page');
			                if (function_exists('icl_object_id')) $contact_page_id = icl_object_id($contact_page_id, 'page', true);  //wpml prepared
							if($contact_page_id) echo "<li class='mail'><a href='".get_permalink($contact_page_id)."'>".__('Send us Mail', 'ace_framework')."</a></li>";
							
							 
							if($dribbble = ace_get_option('dribbble')) echo "<li class='dribbble'><a href='http://dribbble.com/".$dribbble."'>".__('Follow us on dribbble', 'ace_framework')."</a></li>";
							if($twitter = ace_get_option('twitter')) echo "<li class='twitter'><a href='http://twitter.com/".$twitter."'>".__('Follow us on Twitter', 'ace_framework')."</a></li>";
							if($facebook = ace_get_option('facebook')) echo "<li class='facebook'><a href='".$facebook."'>".__('Join our Facebook Group', 'ace_framework')."</a></li>";
							
							 ?>
							
							<li class='rss'><a href="<?php ace_option('feedburner',get_bloginfo('rss2_url')); ?>"><?php _e('Subscribe to our RSS Feed', 'ace_framework')?></a></li>

									
						</ul>
						<!-- end social_bookmarks-->
				
				</div>
			</div>
			<!-- ####### END SOCKET CONTAINER ####### -->
		
		</div><!-- end wrap_all -->
		
		
		
		<?php
			$bg_image 		= ace_get_option('bg_image') == "custom" ? ace_get_option('bg_image_custom') : ace_get_option('bg_image');
		
			if($bg_image && ace_get_option('bg_image_repeat') == 'fullscreen') 
			{ ?>
				<!--[if lte IE 8]>
				<style type="text/css">
				.bg_container {
				-ms-filter:"progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $bg_image; ?>', sizingMethod='scale')";
				filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $bg_image; ?>', sizingMethod='scale');
				}
				</style>
				<![endif]-->
			<?php
				echo "<div class='bg_container' style='background-image:url(".$bg_image.");'></div>"; 
			}
		?>
		

<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */
	 
	ace_option('analytics', false, true, true);
	wp_footer();
	
	
?>
</body>
</html>