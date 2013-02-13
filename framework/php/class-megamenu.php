<?php  if ( ! defined('ACE_FW')) exit('No direct script access allowed');
/**
 * This file holds various classes and methods necessary to hijack the wordpress menu and improve it with mega menu capabilities
 *
 *
 * @author		Afzaal Ameer
 * @copyright	Copyright (c) Afzaal Ameer
 * @link		http://www.afzaalace.com
 * @since		Version 1.0
 * @package 	AceFramework
 */

/**
 *
 */


if( !class_exists( 'ace_megamenu' ) )
{	

	/**
	 * The ace megamenu class contains various methods necessary to create mega menus out of the admin backend
	 * @package 	AceFramework
	 */
	class ace_megamenu
	{
		
		/**
		 * ace_megamenu constructor
		 * The constructor uses wordpress hooks and filters provided and 
		 * replaces the default menu with custom functions and classes within this file
		 * @package 	AceFramework
		 */
		function ace_megamenu()
		{
			//adds stylesheet and javascript to the menu page
			add_action('admin_menu', array(&$this,'ace_menu_header'));
		
			//exchange arguments and tell menu to use the ace walker for front end rendering
			add_filter('wp_nav_menu_args', array(&$this,'modify_arguments'), 100);
			
			//exchange argument for backend menu walker
			add_filter( 'wp_edit_nav_menu_walker', array(&$this,'modify_backend_walker') , 100);
			
			//save ace options:
			add_action( 'wp_update_nav_menu_item', array(&$this,'update_menu'), 100, 3);
  	
		}
	
		/**
		 * If we are on the nav menu page add javascript and css for the page
		 */
		function ace_menu_header()
		{
			if(basename( $_SERVER['PHP_SELF']) == "nav-menus.php" )
			{	
				wp_enqueue_style(  'ace_admin', ACE_CSS_URL . 'ace_admin.css'); 
				wp_enqueue_script( 'ace_mega_menu' , ACE_JS_URL . 'ace_mega_menu.js',array('jquery', 'jquery-ui-sortable'), false, true ); 
			}
		}
	

		/**
		 * Replaces the default arguments for the front end menu creation with new ones
		 */
		function modify_arguments($arguments){
							
			$arguments['walker'] 				= new ace_walker();
			$arguments['container_class'] 		= $arguments['container_class'] .= ' megaWrapper';
			$arguments['menu_class']			= 'ace_mega';

			return $arguments;
		}
		
		
		/**
		 * Tells wordpress to use our backend walker instead of the default one
		 */
		function modify_backend_walker($name)
		{
			return 'ace_backend_walker';
		}
		
		
		
		/*
		 * Save and Update the Custom Navigation Menu Item Properties by checking all $_POST vars with the name of $check
		 * @param int $menu_id
		 * @param int $menu_item_db
		 */
		function update_menu($menu_id, $menu_item_db)
		{	
			$check = array('megamenu','division','textarea' );
			
			foreach ( $check as $key )
			{
				if(!isset($_POST['menu-item-ace-'.$key][$menu_item_db]))
				{
					$_POST['menu-item-ace-'.$key][$menu_item_db] = "";
				}
				
				$value = $_POST['menu-item-ace-'.$key][$menu_item_db];
				update_post_meta( $menu_item_db, '_menu-item-ace-'.$key, $value );
			}
		}
	}
}



if( !class_exists( 'ace_walker' ) )
{

	/**
	 * The ace walker is the frontend walker and necessary to display the menu, this is a advanced version of the wordpress menu walker
	 * @package WordPress
	 * @since 1.0.0
	 * @uses Walker
	 */
	class ace_walker extends Walker {
		/**
		 * @see Walker::$tree_type
		 * @var string
		 */
		var $tree_type = array( 'post_type', 'taxonomy', 'custom' );
	
		/**
		 * @see Walker::$db_fields
		 * @todo Decouple this.
		 * @var array
		 */
		var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );
	
		/**
		 * @var int $columns 
		 */
		var $columns = 0;
		
		/**
		 * @var int $max_columns maximum number of columns within one mega menu 
		 */
		var $max_columns = 0;
		
		/**
		 * @var int $rows holds the number of rows within the mega menu 
		 */
		var $rows = 1;
		
		/**
		 * @var array $rowsCounter holds the number of columns for each row within a multidimensional array
		 */
		var $rowsCounter = array();
		
		/**
		 * @var string $mega_active hold information whetever we are currently rendering a mega menu or not
		 */
		var $mega_active = 0;
	
	
	
		/**
		 * @see Walker::start_lvl()
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int $depth Depth of page. Used for padding.
		 */
		function start_lvl(&$output, $depth) {
			$indent = str_repeat("\t", $depth);
			if($depth === 0) $output .= "\n{replace_one}\n";
			$output .= "\n$indent<ul class=\"sub-menu\">\n";
		}
	
		/**
		 * @see Walker::end_lvl()
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int $depth Depth of page. Used for padding.
		 */
		function end_lvl(&$output, $depth) {
			$indent = str_repeat("\t", $depth);
			$output .= "$indent</ul>\n";
			
			if($depth === 0) 
			{
				if($this->mega_active)
				{

					$output .= "\n</div>\n";
					$output = str_replace("{replace_one}", "<div class='ace_mega_div ace_mega".$this->max_columns."'>", $output);
					
					foreach($this->rowsCounter as $row => $columns)
					{
						$output = str_replace("{current_row_".$row."}", "ace_mega_menu_columns_".$columns, $output);
					}
					
					$this->columns = 0;
					$this->max_columns = 0;
					$this->rowsCounter = array();
					
				}
				else
				{
					$output = str_replace("{replace_one}", "", $output);
				}
			}
		}
	
		/**
		 * @see Walker::start_el()
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item Menu item data object.
		 * @param int $depth Depth of menu item. Used for padding.
		 * @param int $current_page Menu item ID.
		 * @param object $args
		 */
		function start_el(&$output, $item, $depth, $args) {
			global $wp_query;
			
			//set maxcolumns
			if(!isset($args->max_columns)) $args->max_columns = 5;

			
			$item_output = $li_text_block_class = $column_class = "";
			
			if($depth === 0)
			{	
				$this->mega_active = get_post_meta( $item->ID, '_menu-item-ace-megamenu', true);
			}
			
			
			if($depth === 1 && $this->mega_active)
			{
				$this->columns ++;
				
				//check if we have more than $args['max_columns'] columns or if the user wants to start a new row
				if($this->columns > $args->max_columns || (get_post_meta( $item->ID, '_menu-item-ace-division', true) && $this->columns != 1))
				{
					$this->columns = 1;
					$this->rows ++;
					$output .= "\n<li class='ace_mega_hr'></li>\n";
				}
				
				$this->rowsCounter[$this->rows] = $this->columns;
				
				if($this->max_columns < $this->columns) $this->max_columns = $this->columns;
				
				
				$title = apply_filters( 'the_title', $item->title, $item->ID );
				
				if($title != "-" && $title != '"-"') //fallback for people who copy the description o_O
				{
					$item_output .= "<h4>".$title."</h4>";
				}
				
				$column_class  = ' {current_row_'.$this->rows.'}';
				
				if($this->columns == 1)
				{
					$column_class  .= " ace_mega_menu_columns_fist";
				}
			}
			else if($depth >= 2 && $this->mega_active && get_post_meta( $item->ID, '_menu-item-ace-textarea', true) )
			{
				$li_text_block_class = 'ace_mega_text_block ';
			
				$item_output.= do_shortcode($item->post_content);
				
			
			}
			else
			{
				$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
				$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
				$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
				$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';			
			
				$item_output .= $args->before;
				$item_output .= '<a'. $attributes .'>';
				$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
				$item_output .= '</a>';
				$item_output .= $args->after;
			}
			
			
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
			$class_names = $value = '';
	
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
	
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
			$class_names = ' class="'.$li_text_block_class. esc_attr( $class_names ) . $column_class.'"';
	
			$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
			
			
			
			
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	
		/**
		 * @see Walker::end_el()
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item Page data object. Not used.
		 * @param int $depth Depth of page. Not Used.
		 */
		function end_el(&$output, $item, $depth) {
			$output .= "</li>\n";
		}
	}
}





if( !class_exists( 'ace_backend_walker' ) )
{
/**
 * Create HTML list of nav menu input items. 
 * This walker is a clone of the wordpress edit menu walker with some options appended, so the user can choose to create mega menus
 *
 * @package AceFramework
 * @since 1.0
 * @uses Walker_Nav_Menu
 */
	class ace_backend_walker extends Walker_Nav_Menu  
	{
		/**
		 * @see Walker_Nav_Menu::start_lvl()
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference.
		 * @param int $depth Depth of page.
		 */
		function start_lvl(&$output) {}
	
		/**
		 * @see Walker_Nav_Menu::end_lvl()
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference.
		 * @param int $depth Depth of page.
		 */
		function end_lvl(&$output) {
		}
	
		/**
		 * @see Walker::start_el()
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item Menu item data object.
		 * @param int $depth Depth of menu item. Used for padding.
		 * @param int $current_page Menu item ID.
		 * @param object $args
		 */
		function start_el(&$output, $item, $depth, $args) {
			global $_wp_nav_menu_max_depth;
			$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;
	
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
	
			ob_start();
			$item_id = esc_attr( $item->ID );
			$removed_args = array(
				'action',
				'customlink-tab',
				'edit-menu-item',
				'menu-item',
				'page-tab',
				'_wpnonce',
			);
	
			$original_title = '';
			if ( 'taxonomy' == $item->type ) {
				$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
			} elseif ( 'post_type' == $item->type ) {
				$original_object = get_post( $item->object_id );
				$original_title = $original_object->post_title;
			}
	
			$classes = array(
				'menu-item menu-item-depth-' . $depth,
				'menu-item-' . esc_attr( $item->object ),
				'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
			);
	
			$title = $item->title;
	
			if ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
				$classes[] = 'pending';
				/* translators: %s: title of menu item in draft status */
				$title = sprintf( __('%s (Pending)'), $item->title );
			}
	
			$title = empty( $item->label ) ? $title : $item->label;
			
			$itemValue = "";
			if($depth == 0)
			{
				$itemValue = get_post_meta( $item->ID, '_menu-item-ace-megamenu', true);
				if($itemValue != "") $itemValue = 'ace_mega_active ';
			}
			
			?>
			
			<li id="menu-item-<?php echo $item_id; ?>" class="<?php echo $itemValue; echo implode(' ', $classes ); ?>">
				<dl class="menu-item-bar">
					<dt class="menu-item-handle">
						<span class="item-title"><?php echo esc_html( $title ); ?></span>
						<span class="item-controls">
						
						
							<span class="item-type item-type-default"><?php echo esc_html( $item->type_label ); ?></span>
							<span class="item-type item-type-ace"><?php _e('Column'); ?></span>
							<span class="item-type item-type-megafied"><?php _e('(Mega Menu)'); ?></span>
							<span class="item-order">
								<a href="<?php
									echo wp_nonce_url(
										add_query_arg(
											array(
												'action' => 'move-up-menu-item',
												'menu-item' => $item_id,
											),
											remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
										),
										'move-menu_item'
									);
								?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up'); ?>">&#8593;</abbr></a>
								|
								<a href="<?php
									echo wp_nonce_url(
										add_query_arg(
											array(
												'action' => 'move-down-menu-item',
												'menu-item' => $item_id,
											),
											remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
										),
										'move-menu_item'
									);
								?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down'); ?>">&#8595;</abbr></a>
							</span>
							<a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php _e('Edit Menu Item'); ?>" href="<?php
								echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
							?>"><?php _e( 'Edit Menu Item' ); ?></a>
						</span>
					</dt>
				</dl>
	
				<div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
					<?php if( 'custom' == $item->type ) : ?>
						<p class="field-url description description-wide">
							<label for="edit-menu-item-url-<?php echo $item_id; ?>">
								<?php _e( 'URL' ); ?><br />
								<input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
							</label>
						</p>
					<?php endif; ?>
					<p class="description description-thin description-label ace_label_desc_on_active">
						<label for="edit-menu-item-title-<?php echo $item_id; ?>">
						<span class='ace_default_label'><?php _e( 'Navigation Label' ); ?></span>
						<span class='ace_mega_label'><?php _e( 'Mega Menu Column Title <span class="ace_supersmall">(if you dont want to display a title just enter a single dash: "-" )</span>' ); ?></span>
							
							<br />
							<input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
						</label>
					</p>
					<p class="description description-thin description-title">
						<label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
							<?php _e( 'Title Attribute' ); ?><br />
							<input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
						</label>
					</p>
					<p class="field-link-target description description-thin">
						<label for="edit-menu-item-target-<?php echo $item_id; ?>">
							<?php _e( 'link Target' ); ?><br />
							<select id="edit-menu-item-target-<?php echo $item_id; ?>" class="widefat edit-menu-item-target" name="menu-item-target[<?php echo $item_id; ?>]">
								<option value="" <?php selected( $item->target, ''); ?>><?php _e('Same window or tab'); ?></option>
								<option value="_blank" <?php selected( $item->target, '_blank'); ?>><?php _e('New window or tab'); ?></option>
							</select>
						</label>
					</p>
					<p class="field-css-classes description description-thin">
						<label for="edit-menu-item-classes-<?php echo $item_id; ?>">
							<?php _e( 'CSS Classes (optional)' ); ?><br />
							<input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
						</label>
					</p>
					<p class="field-xfn description description-thin">
						<label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
							<?php _e( 'link Relationship (XFN)' ); ?><br />
							<input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
						</label>
					</p>
					<p class="field-description description description-wide">
						<label for="edit-menu-item-description-<?php echo $item_id; ?>">
							<?php _e( 'Description' ); ?><br />
							<textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->post_content ); ?></textarea>
						</label>
					</p>
					
					<div class='ace_mega_menu_options'>
					<!-- ################# ace custom code here ################# -->
						<?php
						$title = 'Use as Mega Menu';
						$key = "menu-item-ace-megamenu";
						$value = get_post_meta( $item->ID, '_'.$key, true);
						
						if($value != "") $value = "checked='checked'";
						?>
						
						<p class="description description-wide ace_checkbox ace_mega_menu ace_mega_menu_d0">
							<label for="edit-<?php echo $key.'-'.$item_id; ?>">
								<input type="checkbox" value="active" id="edit-<?php echo $key.'-'.$item_id; ?>" class=" <?php echo $key; ?>" name="<?php echo $key . "[". $item_id ."]";?>" <?php echo $value; ?> /><?php _e( $title ); ?>
							</label>
						</p>
						<!-- ***************  end item *************** -->
					
						<?php
						$title = 'This column should start a new row';
						$key = "menu-item-ace-division";
						$value = get_post_meta( $item->ID, '_'.$key, true);
						
						if($value != "") $value = "checked='checked'";
						?>
						
						<p class="description description-wide ace_checkbox ace_mega_menu ace_mega_menu_d1">
							<label for="edit-<?php echo $key.'-'.$item_id; ?>">
								<input type="checkbox" value="active" id="edit-<?php echo $key.'-'.$item_id; ?>" class=" <?php echo $key; ?>" name="<?php echo $key . "[". $item_id ."]";?>" <?php echo $value; ?> /><?php _e( $title ); ?>
							</label>
						</p>
						<!-- ***************  end item *************** -->
						
					
						
						<?php
						$title = 'Use the description to create a Text Block. Dont display this item as a link. (note: dont remove the label text, otherwise wordpress will delete the item)';
						$key = "menu-item-ace-textarea";
						$value = get_post_meta( $item->ID, '_'.$key, true);
						
						if($value != "") $value = "checked='checked'";
						?>
						
						<p class="description description-wide ace_checkbox ace_mega_menu ace_mega_menu_d2">
							<label for="edit-<?php echo $key.'-'.$item_id; ?>">
								<input type="checkbox" value="active" id="edit-<?php echo $key.'-'.$item_id; ?>" class=" <?php echo $key; ?>" name="<?php echo $key . "[". $item_id ."]";?>" <?php echo $value; ?> /><span class='ace_long_desc'><?php _e( $title ); ?></span>
							</label>
						</p>
						<!-- ***************  end item *************** -->
					
					
					
					
					
					
					
					
					</div>
					<!-- ################# end ace custom code here ################# -->
				
					<div class="menu-item-actions description-wide submitbox">
						<?php if( 'custom' != $item->type ) : ?>
							<p class="link-to-original">
								<?php printf( __('Original: %s'), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
							</p>
						<?php endif; ?>
						<a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
						echo wp_nonce_url(
							add_query_arg(
								array(
									'action' => 'delete-menu-item',
									'menu-item' => $item_id,
								),
								remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
							),
							'delete-menu_item_' . $item_id
						); ?>"><?php _e('Remove'); ?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo $item_id; ?>" href="<?php	echo add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) );
							?>#menu-item-settings-<?php echo $item_id; ?>">Cancel</a>
					</div>
	
					<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
					<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
					<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
					<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
					<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
					<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
				</div><!-- .menu-item-settings-->
				<ul class="menu-item-transport"></ul>
			<?php
			$output .= ob_get_clean();
		}
	}


}




if( !function_exists( 'ace_fallback_menu' ) )
{
	/**
	 * Create a navigation out of pages if the user didnt create a menu in the backend
	 *
	 */
	function ace_fallback_menu()
	{
		$current = "";
		$exclude = ace_get_option('frontpage');
		if (is_front_page()){$current = "class='current-menu-item'";} 
		if ($exclude) $exclude ="&exclude=".$exclude;
		
		echo "<div class='fallback_menu'>";
		echo "<ul class='ace_mega menu'>";
		echo "<li $current><a href='".get_bloginfo('url')."'>Home</a></li>";
		wp_list_pages('title_li=&sort_column=menu_order'.$exclude);
		echo "</ul></div>";
	}
}


