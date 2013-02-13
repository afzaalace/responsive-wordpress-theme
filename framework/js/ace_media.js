/**
 * This file holds the main javascript functions needed for ace-media uploads
 *
 * @author		Afzaal Ameer
 * @copyright	Copyright ( c ) Afzaal Ameer
 * @link		http://www.afzaalace.com
 * @since		Version 1.0
 * @package 	AceFramework
 */

(function($)
{
	var ace_media = {
	
		aceUseCustomEditor: false,
		acePostID: false,
		insertContainer : false,
		
		// bind the click event to all elements with the class ace_uploader 
		bind_click: function()
		{
			$('.ace_uploader').live('click', function()
			{
				var title  = this.title,
					idBased = "";
				this.title = "";
				

				
				ace_media.acePostID = this.hash.substring(1);
				ace_media.aceUseCustomEditor = true;
				ace_media.insertContainer  = $(this).parents('.ace_upload_container');
				
				//
				if(ace_media.insertContainer.is('.ace_advanced_upload'))
				{
					idBased = "&amp;ace_idbased="+ $('.ace_upload_input', ace_media.insertContainer).attr('name');
				}
				
				var label = $(this).parents('.ace_upload_container').find('.ace_upload_insert_label').trigger('change').val();
				var gallery_mode = $(this).parents('.ace_upload_container').find('.ace_gallery_mode');
				var gallery_mode_val = "";
				
				if(gallery_mode.length)
				{
					gallery_mode_val = '&amp;ace_gallery_mode='+gallery_mode.trigger('change').val();
				}
				
				tb_show( title, 'media-upload.php?post_id='+ace_media.acePostID+idBased+gallery_mode_val+'&amp;ace_label='+encodeURI(label)+"&amp;TB_iframe=true" );
				
				
				return false;
			});
		},
		
		// bind the click event of the remove image links to the removing function
		bind_remove: function()
		{
			$('.ace_remove_image').live('click', function()
			{
				var container = $(this).parents('.ace_upload_container');
					
					container.find('.ace_upload_input').val('').trigger('change');
					container.find('.ace_preview_pic').hide(400, function(){ $(this).html("").css({display:"block"}); });
					
				return false;
			});
		},
		
		bind_blur: function()
		{
			$('.ace_upload_input').live('blur change', function()
			{
				var input = $(this),
					value = input.val(),
					image = '<img src ="'+value+'" alt="" />',
					div = input.parents('.ace_upload_container:eq(0)').find('.ace_preview_pic');
					
					if(value != "")
					{
						div.html('<a href="#" class="ace_remove_image">remove</a>' + image);
					}
					else
					{
						div.html("");
					}
			});
		},
		
		//changes the label of the "insert into post" button to better reflect the current action and hides the use as post-thumb button
		change_label: function()
		{	
			ace_media.idBasedUpload();
			
			var newlabel = $('.ace_insert_button_label').val();
			
			if(newlabel != "" && typeof newlabel == 'string')
			{				
				var savesendContainer = $(".savesend");
				
				if(savesendContainer.length > 0)
				{		
					$(".button", savesendContainer).val(newlabel);
					$(".wp-post-thumbnail", savesendContainer).css('display','none');	
				}
			}
		},
		
		//hijack the original uploader and replace it if a user clicks on one an ace_uploader
		hijack_uploader: function()
		{			
			window.original_send_to_editor = window.send_to_editor;
     		window.send_to_editor = function(html)
     		{     			
     			if(ace_media.aceUseCustomEditor)
				{
					var container = ace_media.insertContainer,
						returned = $(html),
						img = returned.attr('src') || returned.find('img').attr('src') || returned.attr('href'),
						visualInsert = '';
					
					container.find('.ace_upload_input').val(img).trigger('change');
					
					if(img.match(/.jpg$|.jpeg$|.png$|.gif$/))
					{
						visualInsert = '<a href="#" class="ace_remove_image">remove</a><img src="'+img+'" alt="" />';
					}
					else
					{
						visualInsert = '<a href="#" class="ace_remove_image">remove</a><img src="'+ace_framework_globals.frameworkUrl+'images/icons/video.png" alt="" />';
					}
					
					container.find('.ace_preview_pic').html(visualInsert);
					
					tb_remove();
		     		ace_media.reset_uploader();
				}	
				else
				{
					window.original_send_to_editor(html);
				}
     		};
		},
		
		//id based advanced upload
		idBasedUpload: function()
		{
			var idbased = $('.ace_idbased');			
			
			if(idbased.length > 0)
			{
				idbased = idbased.val();
			
				var savesendContainer = $(".savesend"),
					insertInto = $(".button", savesendContainer).not('.del-attachment .button'),
					target =  $("input[name="+idbased+"]", parent.document),
					imageTarget = target.parents('.ace_advanced_upload:eq(0)').find('.ace_preview_pic'),
					filter = $("#filter"), 
					label = $(".ace_insert_button_label"),
					gallery_mode = $(".ace_gallery_mode_active");
					
					
					var gallery_form = $("#gallery-form, #file-form");
					if(gallery_form.length)
					{
						var ref_url = gallery_form.find("input[name=_wp_http_referer]").val(),
							form_url = gallery_form.attr('action'),
							new_url = "";
						
						ref_url = ref_url.replace(/.+media-upload\.php?/,'');
						form_url = form_url.replace(/media-upload\.php?.+/,'');
						new_url = form_url + "media-upload.php" + ref_url;
						
						gallery_form.attr('action', new_url);
					}
					
				if(gallery_mode.length)
				{
					gallery_mode = true;
					
					if($('#ace_update_gallery').length)
					{
						update_gal = $('#ace_update_gallery');
					}
					else
					{
					var save_all = $('#save-all, #save').not('.hidden'),
						save_single = $('#save'),
						update_gal = $('<input type="submit" name="ace_update_gallery" id="ace_update_gallery" class="button savebutton" value="...then close the window and update gallery preview" />');					update_gal.insertAfter(save_all);
						
						//update_gal.insertAfter(save_single);
					}
					
					$('.savesend .button').not('.del-attachment .button').remove();
					insertInto = $('.savesend .button, #insert-gallery, #ace_update_gallery').not('.del-attachment .button').attr('onmousedown',"");
					
					$('#gallery-settings').css({display:'none'});
					
					
				}
				else
				{
					gallery_mode = false;
				}
					
				//add the id based and the insert name field as a form input so it gets sent in case the user uses the search or filter functions
				if(filter.length)
				{
					//duplication check
					var filterInsert = filter.find("input[name=ace_idbased]"),
						labelInsert	 = filter.find("input[name=ace_label]"),
						galleryInsert= filter.find("input[name=ace_gallery_mode]");
					
					if(!filterInsert.length)
					{
						filter.prepend("<input type='hidden' name='ace_idbased' value='"+idbased+"'/>");
					}
					
					if(label.length && !labelInsert.length)
					{
						filter.prepend("<input type='hidden' name='ace_label' value='"+label.val()+"'/>");
					}
					
					if(gallery_mode && !galleryInsert.length)
					{
						filter.prepend("<input type='hidden' name='ace_gallery_mode' value='true'/>");
					}
					
				}
				
				if(gallery_mode)
				{ 
					insertInto.unbind('click').bind('click', function()
					{
						
						var attachment_id = post_id,
							newTarget = target.parents('.ace_control:eq(0)').find('.ace_thumbnail_container');
									
						$.ajax({
				 		  type: "POST",
				 		  url: window.ajaxurl,
				 		  data: "action=ace_ajax_get_gallery&attachment_id="+attachment_id,
				 		  success: function(msg)
				 		  {
				 		  	newTarget.html(msg);
				 		  	parent.tb_remove();
				 		  	ace_media.reset_uploader();
				 		  }
				 		});
				 		
				 		return false;
					});
				}
				else
				{
						insertInto.unbind('click').bind('click', function()
						{
							var attachment_id = this.name.replace(/send\[/,"").replace(/\]/,"");	
										
							$.ajax({
					 		  type: "POST",
					 		  url: window.ajaxurl,
					 		  data: "action=ace_ajax_get_image&attachment_id="+attachment_id,
					 		  success: function(msg)
					 		  {
					 		  	
					 		  	if(msg.match(/^<img/)) //image insert
					 		  	{
					 		  		target.val(attachment_id);
					 		  		imageTarget.html('<a href="#" class="ace_remove_image">remove</a>'+msg);
					 		  	}
					 		  	else //video insert
					 		  	{
					 		  		target.val(msg);
					 		  		visualInsert = '<a href="#" class="ace_remove_image">remove</a><img src="'+ace_framework_globals.frameworkUrl+'images/icons/video.png" alt="" />';
					 		  		imageTarget.html(visualInsert);
					 		  	}
		
					 		  	parent.tb_remove();
					 		  	ace_media.reset_uploader();
					 		  }
					 		});
					 		
					 		return false;
						});
				
				}
				
					
			}
		},
		
		
		
		//reset values for the next upload
		reset_uploader: function()
		{
     		ace_media.aceUseCustomEditor = false;
     		ace_media.acePostID = false;
     		ace_media.insertContainer = false;     		
		}
		
		
	};
	

	
	$(function()
	{
		$('#media-buttons a').click(ace_media.reset_uploader);
		ace_media.bind_click();
		ace_media.bind_blur();
		ace_media.bind_remove();
		ace_media.idBasedUpload();
		ace_media.hijack_uploader();
		ace_media.change_label();
		$(".slidetoggle").live('mouseenter',ace_media.change_label);
 	});

	
})(jQuery);	 