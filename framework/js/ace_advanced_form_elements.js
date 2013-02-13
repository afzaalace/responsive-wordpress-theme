/**
 * This file holds the main javascript functions needed to clone option groups and improve those form elements with
 * js behaviour
 *
 * @author		Afzaal Ameer
 * @copyright	Copyright ( c ) Afzaal Ameer
 * @link		http://www.afzaalace.com
 * @since		Version 1.0
 * @package 	AceFramework
 */

jQuery(function($) {

	$('body').ace_event_listener();
    $('.ace_set').ace_clone_sets();
    $('.ace_required_container').ace_form_requirement();
    $('.ace_target_value').ace_target();
    $('.ace_link_controller').ace_prefill_options();
    $('.ace_onchange').ace_on_change();

    //unify select dropdowns
    $('.ace_select_unify select').live('change', function()
    {
    	var el = $(this);
    	el.next('.ace_select_fake_val').text(el.find('option:selected').text());
    });
    
    $('.ace_select_unify select').not('.ace_multiple_select select').each(function()
    {
    	var el = $(this);
    	el.css('opacity',0).next('.ace_select_fake_val').text(el.find('option:selected').text());
    });
    
    
    
  });



/************************************************************************
ace_event_binding

event binding fake plugin to circumvent event cloning problems with external plugins
*************************************************************************/

(function($)
{
	$.fn.ace_event_binding = function(variables) 
	{		
		return this.each(function()
		{		
			if(window.parent && window.parent.document && variables != 'skip')
			{
				parent.jQuery(window.parent.document.body).trigger('ace_event_binding',[this]);
				return;
			}
		
			var container = $(this);
			if($.fn.ace_media_advanced_plugin)		container.ace_media_advanced_plugin();
			if($.fn.ace_color_picker_activation) 	container.ace_color_picker_activation();
			if($.fn.ace_clone_sets) 				container.ace_clone_sets();
			if($.fn.ace_form_requirement) 			$('.ace_required_container', container).ace_form_requirement();
			if($.fn.ace_target) 					$('.ace_target_value', container).ace_target();
			if($.fn.ace_prefill_options) 			$('.ace_link_controller', container).ace_prefill_options();
			if($.fn.ace_on_change) 				$('.ace_on_change', container).ace_on_change();
			
			var saveButton = $('.ace_submit'),
				elements = $('input, select, textarea', container).not('.ace_button_inactive');
			elements.bind('keydown change', function(){saveButton.removeClass('ace_button_inactive'); });
			$('.ace_clone_set, .ace_remove_set, .ace_dynamical_add_elements', container).bind('click', function(){ saveButton.removeClass('ace_button_inactive'); });
			$('.ace_select_unify select').not('.ace_multiple_select select').css('opacity',0);
		
		});
	};
})(jQuery);	


//event binding helper when executing events from an iframe
(function($)
{
	$.fn.ace_event_listener = function(variables) 
	{	
		this.bind('ace_event_binding', function(event, element)
		{
			parent.jQuery(element).ace_event_binding('skip');
		});
	};
})(jQuery);



/************************************************************************
ace_on_change function

execute a function after change event was fired
*************************************************************************/
(function($)
{
	$.fn.ace_on_change = function(variables) 
	{	
		return this.each(function()
		{
			var item 	= $(this),
				event 	= item.data('ace-onchange');	
			
			//available functions
			var methods = 
			{
				ace_add_google_font: function()
				{
					var current = $(this),
						value 	= current.val(),
						cssValue = value.replace(/ /, "+", value);
						parent = current.parents('.ace_control:eq(0)'),
						cssRule = parent.find('.google_css');
						
					cssRule.remove();
					cssRule = $('<div class="google_css"><link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family='+cssValue+'" /> <style type="text/css">.ace_google_font{font-family:'+value+';}</style></div>')
					cssRule.appendTo(parent);
				}
			};
			
			
			item.bind('change', methods[event]).trigger('change');
		});
	};
})(jQuery);	


/************************************************************************
ace_prefill_options

sets element to certain values when a controll element is clicked
*************************************************************************/
(function($)
{
	$.fn.ace_prefill_options = function(variables) 
	{	
		return this.each(function()
		{
			var item = $(this),
				siblings = item.parents('.ace_section:eq(0)').find('.ace_link_controller'),
				htmlData = item.data(),
				i = "";
					
				var methods = {
				
					apply: function()
					{
						siblings.removeClass('ace_link_controller_active');
						item.addClass('ace_link_controller_active');
						
						for (i in htmlData)
						{
							if(typeof htmlData[i] == "string" || typeof htmlData[i] == "number" || typeof htmlData[i] == "boolean")
							{
								var el = $('#'+i);
								if(el.length)
								{
									if(el.is('input[type=text]') || el.is('input[type=hidden]') || el.is('select'))
									{
										el.val(htmlData[i]).trigger('change');
									}
								}
							}
						}
						
						return false;
					}
				};
								
				
			item.bind('click', methods.apply );
				
		});
	};
})(jQuery);	


/************************************************************************
ace_target

injects data into a target field, based on type of data providing element
*************************************************************************/
(function($)
{
	$.fn.ace_target = function(variables) 
	{
		return this.each(function()
		{
		
			var item = $(this),
				container = item.parents('.ace_section:eq(0)'),
				monitorItem = "",
				execute = "",
				values = item.val().split('::'),
				targetContainer = $('#ace_'+values[0]),
				target = $(values[1], targetContainer),
				changeProperty = values[2];				 				
				
				var methods = {
				
					apply: function()
					{
						
						var the_value = monitorItem.val(), hiddenItem = false, property = [];
						
						if(changeProperty.indexOf(',') >= 0 ) 
						{
							property = changeProperty.split(',');
						}
						else
						{
							property = [changeProperty];
						}
						
						for( var i in property)
						{
							if(container.css("display") != "block") {the_value = ""; hiddenItem = true;};
							switch(property[i])
							{
								case 'background-color': target.css({'background-color':the_value}); break;
								case 'background-image': if(!hiddenItem) target.css({'background-image':"url(" + the_value + ")"}); break;
								case 'border-color': target.css({'border-color':the_value}); break;
								case 'color': target.css({'color':the_value}); break;
								case 'set_id': target.attr({'id':the_value.replace(/\./,'-')}); break;
							}
						}
						
						
					}
				};
								
				
				if(container.is('.ace_select'))
				{
					monitorItem = container.find('select');
				}
				
				if(container.is('.ace_colorpicker'))
				{
					monitorItem = container.find('.ace_color_picker');
				}
				
				if(container.is('.ace_upload'))
				{
					monitorItem = container.find('.ace_upload_input');
				}
				
				
				
				if(typeof monitorItem != "string")
				{
					monitorItem.bind('change', function()
					{
						methods.apply();
					});				
				}
				
				setTimeout(function(){ methods.apply(); },200);
		});
	};
})(jQuery);	



/************************************************************************
ace_form_requirement

creates dependencies between various form elements: 
divs with elements get hidden or shown depending on the value of other elements
*************************************************************************/

(function($)
{
	$.fn.ace_form_requirement = function(variables) 
	{	
		
		return this.each(function()
		{
			var container = $(this),
				basicData = { 
							el: container,
							elHeight : container.css({display:"block", height:"auto"}).height(),
							elPadd : { top: container.css("paddingTop"), bottom: container.css("paddingBottom")  },
							required : $('.ace_required', this).val().split('::')
						};
				
				var base_id = $('.ace_required', this).parents('.ace_section:eq(0)').attr('id');
				
				//exception for visual groups
				if(typeof base_id != 'string') base_id = $('.ace_required', this).parents('.ace_visual_set:eq(0)').attr('id');
				
				var unique_event_id = base_id.split('-__-');
				
				if(typeof unique_event_id[1] != 'undefined') 
				{
					unique_event_id = unique_event_id[unique_event_id.length-1];
				}
				else
				{
					unique_event_id = unique_event_id[0];
				}
				
				container.css({display:'none'});
				
				//find the next sibling that has the desired class on our option page
				var elementToWatchWrapper = container.siblings('div[id$='+basicData.required[0]+']');
				
				
				// if we couldn find one check if we are inside a metabox panel by search for the ".inside" parent div
				if(elementToWatchWrapper.length == 0) elementToWatchWrapper = container.parents('.inside').find('div[id$='+basicData.required[0]+']');
				

				// bind the event and set the current state of visibility
				var elementToWatch = $(':input[name$='+basicData.required[0]+']', elementToWatchWrapper);
				
				//if we couldnt find the elment to watch we might need to search on the whole page, it could be outside of the group as a "global" setting
				if(elementToWatch.length == 0) elementToWatch = $(':input[name$='+basicData.required[0]+']');
				
				if(container.is('.inactive_visible'))
				{
					$('<div class="ace_inactive_overlay"><span>'+container.data('group-inactive')+'</span></div>').appendTo(container);
				}
				
				//set current state:
				if(elementToWatch.is(':checkbox'))
				{	
					if((elementToWatch.attr('checked') && basicData.required[1]) || (!elementToWatch.attr('checked') && !basicData.required[1]) ) 
					{ 
						if(container.is('.inactive_visible'))
						{
							container.addClass('ace_visible');
						}
						else
						{
							container.css({display:'block'}); 
						}
					}
				}
				else
				{
					if(elementToWatch.val() == basicData.required[1] || 
					  (elementToWatch.val() != "" && basicData.required[1] == "{true}") || (elementToWatch.val() == "" && basicData.required[1] == "{false}") ||
					  (basicData.required[1].indexOf('{contains}') !== -1 && elementToWatch.val().indexOf(basicData.required[1].replace('{contains}','')) !== -1) ||
					  (basicData.required[1].indexOf('{higher_than}') !== -1 && parseInt(elementToWatch.val()) >= parseInt((basicData.required[1].replace('{higher_than}',''))))
					
					) 
					{ 
						if(container.is('.inactive_visible'))
						{
							container.addClass('ace_visible');
						}
						else
						{
							container.css({display:'block'}); 
						}
					}
				}
				
		
				
				//bind change event for future state changes
				elementToWatch.bind('change', {set: basicData}, methods.change);
						
		});
	};
	
	

	var methods = 
	{
		change: function(passed)
		{
			
			
			var data = passed.data.set,
				elToCheck = $(this);
						
			if(elToCheck.val() == data.required[1] ||
			(elToCheck.val() != "" && data.required[1] == "{true}") || (elToCheck.val() == "" && data.required[1] == "{false}") ||
			(elToCheck.is(':checkbox') && (elToCheck.attr('checked') && data.required[1] || !elToCheck.attr('checked') && !data.required[1])) ||
			(data.required[1].indexOf('{contains}') !== -1 && elToCheck.val().indexOf(data.required[1].replace('{contains}','')) !== -1) ||
			(data.required[1].indexOf('{higher_than}') !== -1 && parseInt(elToCheck.val()) >= parseInt((data.required[1].replace('{higher_than}',''))))
			)
			{
				if(data.el.is('.inactive_visible'))
				{
					data.el.addClass('ace_visible');
					return;
				}
				
				
				if(data.el.css('display') == 'none')
				{
					
					if(data.elHeight == 0)
					{
						data.elHeight = data.el.css({visibility:"hidden", position:'absolute'}).height();
					}
				
					data.el.css( {height:0, opacity:0, overflow:"hidden", display:"block", paddingBottom:0, paddingTop:0, visibility:"visible", position:'relative'}).animate(
							{height: data.elHeight, opacity:1, paddingTop: data.elPadd.top, paddingBottom: data.elPadd.bottom}, function()
							{
								data.el.css({overflow:"visible", height:"auto"});
							});
				}
			}
			else
			{
									
				if(data.el.is('.inactive_visible'))
				{
					data.el.removeClass('ace_visible');
					return;
				}
				
				if(data.el.css('display') == 'block')
				{
					if(data.el.is('.set_blank_on_hide')) { var blank_el = data.el.find('.set_blank_on_hide'); blank_el.val("").trigger('change'); }
					data.el.css({overflow:"hidden"}).animate({height:0, opacity:0, paddingBottom:0, paddingTop:0}, function()
					{
						data.el.css({display:"none", overflow:"visible", height:"auto"});
						
					});
				}
			}
		}
	};
	
})(jQuery);	










/************************************************************************
ace_clone_sets: function to modify sets: add them, remove them and recalculate set ids
*************************************************************************/



(function($)
{
	$.fn.ace_clone_sets = function(variables) 
	{
		return this.each(function()
		{
			//gather form data
			var container = $(this);
			
			if(container.length != 1) return;
			
			var hiddenDataContainer = $('#ace_hidden_data'),
				saveData = {
							container    : 	container,
							createButton : 	$('.ace_clone_set', this),
							removeButton : 	$('.ace_remove_set', this),
							nonce: 			$('input[name=ace-nonce]', hiddenDataContainer).val(),
							ajaxUrl: 		$('input[name=admin_ajax_url]', hiddenDataContainer).val(),
							ref: 			$('input[name=_wp_http_referer]', hiddenDataContainer).val(),
							prefix :		$('input[name=ace_options_prefix]', hiddenDataContainer).val(),
							meta_active:	$('input[name=meta_active]', hiddenDataContainer)
							};
			
			
			//bind actions:
			saveData.createButton.unbind('click').bind('click', {set: saveData}, methods.add); 	//creates a new set
			saveData.removeButton.unbind('click').bind('click', {set: saveData}, methods.remove); 	//remove a  set
			
			
		});
	};
	
	var currentlyModifying = false,
	 	methods = {
	
	
		/**
		 *  This functions adds a new dataset
		 *  Based on the link that was clicked the script checks the containing set contaienr and extracts the id (optionSlug) from that
		 *  container. It then sends an ajax request to the admin-ajax.php script which executes the ace_ajax_modify_set php function 
		 *  The php function searches for an option array that is identical to the optionSlug in the options array and returns the html code
		 *  for this. The script then inserts that code and shows it, then functionallity gets applied
		 */
 
		add: function(passed)
		{
			//security check to prevent ajax request problems: only modify one set at a time
			if(currentlyModifying) return false;
			currentlyModifying = true;
		
			//get the current button, the container to clone and extract the id from that container
			var data = passed.data.set,
				currentButton = $(this),
				loadingIcon = currentButton.prev('.ace_clone_loading'),
				cloneContainer = currentButton.parents('.ace_set:eq(0)'),
				parentCloneContainer = currentButton.parents('.ace_set:eq(1)'),
				elementSlug = cloneContainer.attr('id');
			
			if(parentCloneContainer.length == 1)
			{
				var removeString = parentCloneContainer.attr('id');
				
				elementSlug = elementSlug.replace(removeString+'-__-','').replace(/-__-\d+/,'');
			}
			else
			{
				elementSlug = elementSlug.replace('ace_','').replace(/-__-\d+/,'');
			}
			
			
			//check if its a meta page:
			var page_context = 'options_page';
			if(data.meta_active.length) page_context = 'metabox';
			
			
			//send ajax request to the ajax-admin.php script	
			$.ajax({
					type: "POST",
					url: data.ajaxUrl,
					data: 
					{
						action: 'ace_ajax_modify_set',
						method: 'add',
						elementSlug: elementSlug,
						context: page_context
						
					},
					beforeSend: function()
					{
						loadingIcon.fadeIn(300);
					},
					error: function()
					{
						$('body').ace_alert({the_class:'error', text:'Couldnt connect to your Server <br/> Please wait a few seconds and try again', show:4500});
						loadingIcon.fadeOut(300);
					},
					success: function(response)
					{
						var save_result = response.match(/\{ace_ajax_element\}(.+|\s+)\{\/ace_ajax_element\}/);
						
						if(save_result != null)
						{	

							//add new set to the dom
							var newSet = $(save_result[1]).css('display','none');
							
							methods.setBlank(newSet);
							newSet.insertAfter(cloneContainer).slideDown(400, function()
							{
								//recalculate the id indices that are used for form elements and divs
								data.currentSet = newSet;
								methods.recalcIds(data);
								
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

					},
					complete: function(response)
					{	
						loadingIcon.fadeOut(300);
						currentlyModifying = false;
					}
				});		

			return false;
		},
		
		remove: function(passed)
		{
			//security check to prevent ajax request problems: only modify one set at a time
			if(currentlyModifying) return false;
			currentlyModifying = true;

			var data = passed.data.set,
				currentButton = $(this),
				singleSet = currentButton.parents('.ace_set:eq(0)'),
				id = singleSet.attr('id').replace(/-__-\d+$/,'-__-');
				
				data.setsToCount = singleSet.siblings('.ace_set').filter('[id*='+id+']');

				if(data.setsToCount.length || data.removeButton.is('.remove_all_allowed'))
				{
					data.currentSet = data.setsToCount.filter(':eq(0)');
					
					singleSet.slideUp(400, function()
					{
						singleSet.remove();
						methods.recalcIds(data);
						currentlyModifying = false;	
					});
				
				}
				else
				{
					methods.setBlank(singleSet);
					data.setsToCount = false;
					currentlyModifying = false;	
					
				}
					
			return false;
		},
		/************************************************************************
		empty all elements within a container. usually called if an element is the last one to delete
		*************************************************************************/		
		setBlank: function(container)
		{
			$('input:text, input:hidden, textarea', container).not('.ace_upload_insert_label, .ace_required').val('').trigger('change');						
			$('input:checkbox, input:radio, select', container).removeAttr("checked").removeAttr("selected").trigger('change');
			$('.ace_preview_pic, .ace_color_picker_div', container).html("").css({backgroundColor:'transparent'});
		},


		recalcIds: function(data)
		{
			ace_recalcIds(data);
		}
		

	};
	
	
		/************************************************************************
		recalculate ids whenever an element is added or deleted
		*************************************************************************/
	
	    
	    ace_recalcIds = function(data)
		{	
			//if no element group was passed create one
			//(no elements are passed on delete, we need to pass the group when we delete since the set isnt available any more)
			if(!data.setsToCount)
			{					
				var id = data.container.attr('id').replace(/-__-\d+$/,'-__-');
				data.setsToCount = data.currentSet.siblings('.ace_set').filter('[id*='+id+']').add(data.currentSet);
			}
			
			//check if we got a parent group
			var parentGroup = data.currentSet.parents('.ace_set:eq(0)'),
				newId = "";

			
			
			//if we got a parent group calculate the string that needs to be prepended to all siblings based on that parent
			//otherwise the current group is the highest within the dom and needs to be used as string base	
			if(parentGroup.length == 1)
			{
				newId = data.currentSet.attr('id').replace('ace_','');
				newId = parentGroup.attr('id') +'-__-'+ newId.replace(/\d+$/,'');
			}
			else
			{	
				if(data.currentSet.attr('id'))
				{
					newId = data.currentSet.attr('id').replace(/\d+$/,'');
				}
			}
			
			/**
			 *  
			 *  iterate over all sets that are siblings of the newly added set to recalculate the ids and names of the elements within
			 *  First we modify the set id, based on that id we dig deeper into the dom and whenever a nested set is encountered
			 *  the base string to modify the names and ids of the elements within this set is changed. The id gets always changed.
			 *  If the id ends with -__-(int)  we know that a subset container gets modified and need to adjust the replacement pattern
			 *  The replacement pattern for form elements is: "id of parent element + own id string" 
			 *  The replacement pattern for container is	: "String: "ace_" + id of parent element + own id" 
			 *
			 */
 
			data.setsToCount.each(function(i)
			{
				var currentSet = $(this),
					elements = $('[id*=-__-], [name*=-__-]', this),
					setId = newId + i;
				
				//modify the highest set id as base for all elements within
				currentSet.attr('id', setId);
				
				//now modify all elements within the set
				elements.each(function()
				{
					
					var element = $(this),
						el_attr = element.attr('id'),
						parentSet = element.parents('.ace_set:eq(0)'),
						replacementString = parentSet.attr('id').replace('ace_','');
						
						
						//checks if id is found that ends with -__-(element_name)									
						var match = el_attr.match(/[a-z0-9](-__-[-_a-zA-Z0-9]+-__-\d+)$/);
						
						if(match == null)
						{
							var myRegexp = /.+-__-([-_a-zA-Z0-9]+)$/;
							match = myRegexp.exec(element.attr('id'));
							
							id_string = replacementString + '-__-' + match[1];
							
							if(element.attr('name'))
							{
								element.attr('name', id_string);
							}
							else
							{
								id_string = 'ace_' +id_string;
							}
							element.attr('id', id_string);
							
						}
						else //else we got an element with -__-(int), therefore we need to modify a subset container
						{
							el_attr_array = match[1];
							element.attr('id', 'ace_' + replacementString + el_attr_array);
						}
					
				});
			});

			//delte the setsToCount global for all future iterations
			data.setsToCount = "";
			
			
			return;			
		} //end recalcids

	
})(jQuery);	



	