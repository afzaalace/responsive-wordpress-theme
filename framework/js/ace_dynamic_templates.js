/**
 * This file holds the main javascript functions needed to create option pages on the fly and also add elements to these dynamic option pages
 *
 * @author		Afzaal Ameer
 * @copyright	Copyright ( c ) Afzaal Ameer
 * @link		http://www.afzaalace.com
 * @since		Version 1.1
 * @package 	AceFramework
 */
 


jQuery(function($) { $('#ace_options_page').ace_dynamic_templates(); });



(function($)
{
	ace_framework_globals.ace_ajax_action = false;

	$.fn.ace_dynamic_templates = function(variables) 
	{
		return this.each(function()
		{
			//gather form data
			var container = $(this);
			if(container.length != 1) return;
			
			var createButton = $('.ace_create_options', this),
				createElementButton = $('a.ace_dynamical_add_elements'),
				hiddenDataContainer = $('#ace_hidden_data', this),
				deletePage = $('.ace_remove_dynamic_page',this),
				deleteElement = $('.ace_remove_dynamic_element',this),
				nameElement = createButton.parents('.ace_create_options_container:eq(0)').find('input.ace_create_options_page_new_name'),
				saveData = {
								ajaxUrl :			$('input[name=admin_ajax_url]', hiddenDataContainer).val(),
								prefix :			$('input[name=ace_options_prefix]', hiddenDataContainer).val(),
								optionSlug :		$('input[name=ace_options_page_slug]', hiddenDataContainer).val(),
								_wpnonce  :			$('input[name=ace-nonce]', hiddenDataContainer).val(),
								_wp_http_referer:	$('input[name=_wp_http_referer]', hiddenDataContainer).val()
							 };

				
				
			//bind actions:	
						
			//add page
			createButton.bind('click', {set: saveData}, methods.add_options_page);
			
			//add element
			createElementButton.live('click', {set: saveData}, methods.add_element);
			
			deletePage.live('click', {set: saveData}, methods.delete_options_page);
			
			deleteElement.live('click', {set: saveData}, methods.delete_element);
			
			
			//prevent activating of default save buttons, instead activate the template creation button
			nameElement.bind('keydown change keyup', function(event)
			{
				if(nameElement.val() != "" && nameElement.val().length > 2)
				{
					createButton.removeClass('ace_button_inactive');
				}
				else if(!createButton.is('.ace_button_inactive'))
				{
					createButton.addClass('ace_button_inactive');
				}
				
				if(event.keyCode == 13)
				{
					if(event.type == 'keyup') createButton.trigger('click');
					return false;
				}
				
			});

			
			});
	};
	
	var	methods = {
			
			/************************************************************************
			DELETE element:
			************************************************************************/
			delete_element: function(passed)
			{
				
				if( ace_framework_globals.ace_ajax_action) return false;
				 ace_framework_globals.ace_ajax_action = true;
			
				var params = passed.data.set,
					link = $(this);
					
				var container = link.parents('.ace_row:eq(0)');
				if(!container.length) container = link.parents('.ace_section:eq(0)');
					
				var	loading = $('.ace_removable_element_loading',  container);

				
				params.elementSlug = this.hash.substring(1);
				params.action = 'ace_ajax_delete_dynamic_element';
				
				$.ajax({
					type: "POST",
					url: params.ajaxUrl,
					data: params,
					beforeSend: function()
					{
						link.css('display','none');
						loading.css({opacity:0, display:"block", visibility:'visible'}).animate({opacity:1});
					},
					error: function()
					{
						$('body').ace_alert({the_class:'error', 
											  text:'Couldn\'t remove the element because the server didn’t respond.<br/>Please wait a few seconds, then try again'});
						link.css('display','block');
						loading.css({display:"none"});
						 ace_framework_globals.ace_ajax_action = false;
					},
					success: function(response)
					{
						if(response.match(/ace_removed_element/))
						{
							container.slideUp(400, function()
							{
								container.remove();
								$('.ace_header .ace_button_inactive, .ace_footer .ace_button_inactive').removeClass('ace_button_inactive');
								 ace_framework_globals.ace_ajax_action = false;
							});
								
							
							
						}
						else
						{
							var resulttext = "Something went wrong, please try again in a few seconds.";
							if(response) resulttext +=  "The script returned the following error: <br/><br/>"+response.replace(/ace_removed_element/,'');
							$('body').ace_alert({the_class:'error', text: resulttext});
							link.css('display','block');
							loading.css({display:"none"});
							 ace_framework_globals.ace_ajax_action = false;
						}
						
					}
				});
					
					
					return false;
			},
			
			/************************************************************************
			DELETE options page:
			************************************************************************/
			delete_options_page: function(passed)
			{
				if( ace_framework_globals.ace_ajax_action) return false;
				 ace_framework_globals.ace_ajax_action = true;
			
				var params = passed.data.set,
					link = $(this),
					container = link.parents('.ace_subpage_container:eq(0)'),
					answer = confirm("Do you really want to "+link.text()+"? This action can not be undone and will also delete all attached elements.");


				params.action = 'ace_ajax_delete_dynamic_options';
				params.elementSlug = this.hash.substring(1);
				
				if(answer)
				{
					$.ajax({
					type: "POST",
					url: params.ajaxUrl,
					data: params,
					error: function()
					{
						$('body').ace_alert({the_class:'error', 
											  text:'Couldn\'t remove the options because the server didn’t respond.<br/>Please wait a few seconds, then try again'});
						ace_framework_globals.ace_ajax_action = false;
					},
					success: function(response)
					{
						if(response.match(/ace_removed_page/))
						{
							$('.ace_sidebar_content .ace_active_nav').remove();
							container.remove();
							
							$('.ace_subpage_container').filter(':eq(0)').addClass('ace_active_container');
							$('.ace_sidebar_content .ace_section_header:eq(0)').addClass('ace_active_nav');
								
						}
						else
						{
							var resulttext = "Something went wrong, please try again in a few seconds."
							if(response) resulttext +=  "The script returned the following error: <br/><br/>"+response;
							$('body').ace_alert({the_class:'error', text: resulttext});
						}
						
						ace_framework_globals.ace_ajax_action = false;
					}
				  });

				}
				else
				{
						ace_framework_globals.ace_ajax_action = false;
				}
					
				return false;
			},
			
			/************************************************************************
			Add options element:
			************************************************************************/
			add_element: function(passed)
			{

			
				var params = passed.data.set,
					clickedButton = $(this),
					wrapper = clickedButton.parents('.ace_dynamical_add_elements_container:eq(0)'),
					currentpage = wrapper.parents('.ace_subpage_container:eq(0)'),
					selectElement = $('select.ace_dynamical_add_elements_select', wrapper),
					loading = $('.ace_loading',  wrapper);

				
				if(selectElement.val() == "") return false;
				
				if( ace_framework_globals.ace_ajax_action) return false;
				 ace_framework_globals.ace_ajax_action = true;
				
				params.elementSlug = selectElement.val();
				params.optionSlug = $('input.ace_dynamical_add_elements_parent', wrapper).val();
				params.configFile = $('input.ace_dynamical_add_elements_config_file', wrapper).val();
				params.action = 'ace_ajax_modify_set';
				params.context = 'custom_set';
				params.method = 'add';
				
				
				//send request to add new page
				$.ajax({
					type: "POST",
					url: params.ajaxUrl,
					data: params,
					beforeSend: function()
					{
						clickedButton.addClass('ace_button_inactive');
						loading.css({opacity:0, display:"block", visibility:'visible'}).animate({opacity:1});
					},
					error: function()
					{
						$('body').ace_alert({the_class:'error', 
											  text:'Couldn\'t add the element because the server didn’t respond.<br/>Please wait a few seconds, then try again'});
						clickedButton.removeClass('ace_button_inactive');
					},
					success: function(response)
					{
						var save_result = response.match(/\{ace_ajax_element\}(.+|\s+)\{\/ace_ajax_element\}/);
						
						if(save_result != null)
						{	
							//add new set to the dom
							var newSet = $(save_result[1]).css('display','none');
							
							newSet.appendTo(currentpage).slideDown(400, function()
							{
								//bind events to the created container elements
								newSet.ace_event_binding();
							});
							
							
							//in case the script returns other output tell the user
							if(save_result[0] != response)
							{
								response = response.replace(save_result[0],'');
								$('body').ace_alert({the_class:'error', 
								text:'Adding of element successful but the script generated unexpected output: <br/><br/> '+response, show:6000});	
							}
							
						}
						else
						{
							var resulttext = "Something went wrong, please try again in a few seconds.";
							if(response) resulttext +=  "The script returned the following error: <br/><br/>"+response;
							$('body').ace_alert({the_class:'error', text: resulttext});
						}
						
						
					},
					complete: function(response)
					{	
						loading.fadeOut();
						 ace_framework_globals.ace_ajax_action = false;
						clickedButton.removeClass('ace_button_inactive');
						
					}
				});
				
					
				return false;
			},
			
			
			/************************************************************************
			Add options page:
			************************************************************************/
			add_options_page: function(passed)
			{
			
				var params = passed.data.set,
					clickedButton = $(this),
					wrapper = clickedButton.parents('.ace_create_options_container:eq(0)'),
					nameElement = $('input.ace_create_options_page_new_name', wrapper),
					loading = $('.ace_loading',  wrapper);


				
				if( clickedButton.is('.ace_button_inactive')) {return false;}
				
				if( ace_framework_globals.ace_ajax_action) return false;
				 ace_framework_globals.ace_ajax_action = true;
				
				//elements to pass to the php script
				params.action = 'ace_ajax_create_dynamic_options';
				params.method = 'add_option_page';
				params.name = nameElement.val();
				params.icon = $('input.ace_create_options_page_temlate_icon', wrapper).val();
				params.parent = $('input.ace_create_options_page_temlate_parent', wrapper).val();
				params.defaul_elements = $('.ace_create_options_page_subelements_of', wrapper).val();
				params.remove_label = $('.ace_create_options_page_temlate_remove_label', wrapper).val();
				params.sortable = $('.ace_create_options_page_temlate_sortable', wrapper).val();
				
				// no name? tell the user to add one	
				if(params.name == "")
				{
					$('body').ace_alert({the_class:'error', text:'Ooops!<br/>You forgot to enter a Name for your template :)', show:2500});
					return false;
				}
				
				//send request to add new page
				$.ajax({
					type: "POST",
					url: params.ajaxUrl,
					data: params,
					beforeSend: function()
					{
						clickedButton.addClass('ace_button_inactive');
						loading.css({opacity:0, display:"block", visibility:'visible'}).animate({opacity:1});
					},
					error: function()
					{
						$('body').ace_alert({the_class:'error', 
											  text:'Couldn\'t add the template because the server didn’t respond.<br/>Please wait a few seconds, then try again'});
						clickedButton.removeClass('ace_button_inactive');
						ace_framework_globals.ace_ajax_action = false;
						
					},
					success: function(response)
					{
						var save_result = response.match(/\{ace_ajax_option_page\}(.+|\s+)\{\/ace_ajax_option_page\}/);
						
						if(save_result != null)
						{	
							//add new set to the dom
							var newSet = $(save_result[1]).insertAfter('.ace_subpage_container:last');
							newSet.ace_create_option_navigation(true);
							nameElement.val('');
							$('body').ace_alert({text:'Template added!<br/> You can now select it in your sidebar and start adding elements', show:3500});
							
							//add default elements 
							var default_elements = response.match(/\{ace_ajax_element\}(.+|\s+)\{\/ace_ajax_element\}/);
							if(default_elements != null)
							{
								var newElements = $(default_elements[1]).appendTo(newSet); 
							}
							
							//bind events to the new elements
							newSet.ace_event_binding();
							newSet.ace_edit_dynamic_templates();
						}
						else if(response.match('invalid_data'))
						{
							clickedButton.removeClass('ace_button_inactive');
							$('body').ace_alert({the_class:'error', text:'Sorry, the name is invalid.<br/>Please don’t use any special characters'});
						}
						else if(response.match('name_already_exists'))
						{
							clickedButton.removeClass('ace_button_inactive');
							$('body').ace_alert({the_class:'error', 
							text:'Please choose a different Name:<br/>A template with this name already exists or is reserved for the theme-framework.', show:5000});
						}
						else
						{
							clickedButton.removeClass('ace_button_inactive');
							$('body').ace_alert({the_class:'error', text:'Something went wrong, please try again in a few seconds.'});
						}
						ace_framework_globals.ace_ajax_action = false;
						
					},
					complete: function(response)
					{	
						loading.fadeOut(); 
					}
				});
				
				return false;
			}
				
		};
	
	
})(jQuery);	 


