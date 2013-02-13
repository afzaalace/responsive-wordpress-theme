/**
 * This file holds the main javascript functions needed to edit dynamic option pages on the fly and also add elements to these dynamic option pages
 *
 * @author		Afzaal Ameer
 * @copyright	Copyright ( c ) Afzaal Ameer
 * @link		http://www.afzaalace.com
 * @since		Version 1.1
 * @package 	AceFramework
 */
 


jQuery(function($) { $('.ace_sortable').ace_edit_dynamic_templates(); });



(function($)
{
	ace_framework_globals.ace_ajax_action = false;

	$.fn.ace_edit_dynamic_templates = function(variables) 
	{
		return this.each(function()
		{
			//gather form data
			var container = $(this);
			if(container.length != 1) return;
			
			container.sortable({
				
				handle: '.ace-row-portlet-header',
				cancel: 'a',
				items: '.ace_row',
				update: function(event, ui) 
				{
					$('.ace_button_inactive').removeClass('ace_button_inactive');
				}

			});
			
			//disable text selection in the header	
			$( ".ace-row-portlet-header" ).disableSelection();	
			
			$('.ace-item-edit', container).live('click', function()
			{
				var edit_link = $(this),
					container = edit_link.parents('.ace_row:eq(0)'),
					content = $('.ace-portlet-content', container);
				
				if(content.is(':visible'))
				{
					content.slideUp(200);
				}
				else
				{
					content.slideDown(200);
				}
				
				return false;
				
			});
			
			

		});
	}
	
})(jQuery);	 


