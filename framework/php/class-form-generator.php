<?php  if (  ! defined( 'ACE_FW' ) ) exit( 'No direct script access allowed' );
/**
 * This file holds the ace_form class which is needed to build contact and other forms for the website
 *
 * @todo: improve backend so users can build forms on the fly, add aditional elements like selects, checkboxes and radio buttons
 *
 * @author		Afzaal Ameer
 * @copyright	Copyright ( c ) Afzaal Ameer
 * @link		http://www.afzaalace.com
 * @since		Version 1.0
 * @package 	AceFramework
 */


/**
 * ACE Form
 * A simple class that is able to build and submit contact forms with the help of simple arrays that are passed to the form
 * It is build in a way that ajax sending is easily possible, but also works without javascript
 * 
 */
 
if( ! class_exists( 'ace_form' ) )
{
	class ace_form
	{
		/**
		 * This array holds some default parameters for each form that gets created
		 * @var array
		 */
		var $form_paramas;
		
		
		/**
		 * This array holds the form elements that where set by the create elements function
		 * @var array
		 */
		var $form_elements;
		
		/**
		 * This string holds the fnal html output
		 * @var string
		 */
		var $output = "";
		
		
		/**
		 * This string holds the html output for elements that gets merged with the final output in case an error occured or no submission took place
		 * @var string
		 */
		var $elements_html = "";
		
		
		/**
		 * This variable holds the information if we should display the form or not. it has to be displayed if an error occurs wihle validating or if no submission took place yet
		 * @var bool
		 */
		var $submit_form = true;
		
		
		/**
         * Constructor
         *
         * The constructor sets up the default params
         * @param array $params array with default form information such as submit button label, heading and success message
         */
		function ace_form($params)
		{
			$this->form_paramas = $params;
			$this->output  = '<form action="'.$params['action'].'" method="post" class="ajax_form"><fieldset>';
			$this->output .=  $params['heading'];
			
			if(!isset($_POST) || !count($_POST))
			{
				$this->submit_form = false;
			}
		}
		
		
		/**
         * create_elements
         *
         * The create_elements method iterates over a set of elements passed and creates the according form element in the frontend
         * @param array $elements array with elements that should eb created
         */
		function create_elements($elements)
		{
			$this->form_elements = $elements;
		
			foreach($elements as $key => $element)
			{
				
			
				if(isset($element['type']) && method_exists($this, $element['type']))
				{
					$this->$element['type'](ace_backend_safe_string('ace_'.$key), $element);
				}
			}
		}
		
		
		/**
         * display_form
         *
         * Checks if an error occured and if the user tried to send, if thats the case, and if sending worked display a success message, otherwise display the whole form
         */
		function display_form()
		{
			$success = '<div id="ajaxresponse" class="hidden"></div>';
		
			if($this->submit_form && $this->send())
			{
				$success = '<div id="ajaxresponse" class="hidden">'.$this->form_paramas['success'].'</div>';
			}
			else
			{
				$this->output .= $this->elements_html;
				$this->output .= '<p><input type="submit" value="'.$this->form_paramas['submit'].'" class="button" /></p>';
			}

			
			$this->output .= '</fieldset></form>'.$success;
			echo $this->output;
		}
		
		
		/**
         * text
         *
         * The text method creates input elements with type text, and prefills them with $_POST values if available.
         * The method also checks against various input validation cases
         * @param string $id holds the key of the element
         * @param array $element data array of the element that should be created
         */
		function text($id, $element)
		{
			$p_class = $required = $element_class = $value = "";
			
			if(!empty($element['check'])) 
			{ 
				$required = "*"; 
				$element_class = $element['check'];
				$p_class = $this->check_element($id, $element);
			}
			
			if(!empty($_POST[$id])) $value = urldecode($_POST[$id]);
			
			$this->elements_html .= "<p class='".$p_class."'>";
			$this->elements_html .= '    <input name="'.$id.'" class="text_input '.$element_class.'" type="text" id="'.$id.'" value="'.$value.'"/><label for="'.$id.'">'.$element['label'].$required.'</label>';
			$this->elements_html .= "</p>";
		}
		
		/**
         * checkbox
         *
         * The text method creates input elements with type checkbox, and prefills them with $_POST values if available.
         * The method also checks against various input validation cases
         * @param string $id holds the key of the element
         * @param array $element data array of the element that should be created
         */
		function checkbox($id, $element)
		{
			$p_class = $required = $element_class = $checked = "";
			
			if(!empty($element['check'])) 
			{ 
				if(!empty($_POST[$id])) $checked = 'checked="checked"';
				$required = "*"; 
				$element_class = $element['check'];
				$p_class = $this->check_element($id, $element);
			}
			if(empty($_POST[$id])) $_POST[$id] = "false";
			
			
			$this->elements_html .= "<p class='".$p_class."'>";
			$this->elements_html .= '    <input '.$checked.' name="'.$id.'" class="input_checkbox '.$element_class.'" type="checkbox" id="'.$id.'" value="true"/><label for="'.$id.'">'.$element['label'].$required.'</label>';
			$this->elements_html .= "</p>";
		}
		
		
		/**
         * Select
         *
         * The select method creates a dropdown element with type select, and prefills them with $_POST values if available.
         * The method also checks against various input validation cases
         * @param string $id holds the key of the element
         * @param array $element data array of the element that should be created
         */
		function select($id, $element)
		{
	
			if(empty($element['options'])) return;
			$element['options'] = explode(',',$element['options']);
			
			$p_class = $required = $element_class = $prefilled_value = $select = "";
			
			if(!empty($element['check'])) 
			{ 
				$required = "*"; 
				$element_class = $element['check'];
				$p_class = $this->check_element($id, $element);
			}
			
			if(!empty($_POST[$id])) $prefilled_value = urldecode($_POST[$id]);
			
			foreach($element['options'] as $option)
			{
				$key = $value = trim($option);
				$suboptions =  explode('|',$option);
				if(is_array($suboptions) && !empty($suboptions[1]))
				{
					$key = trim($suboptions[1]);
					$value = trim($suboptions[0]);
				}
				
			
				$active = $value == $prefilled_value ? "selected='selected'" : "";
				$select .= "<option $active value ='$key'>$value</option>";
			}
			
			
			$this->elements_html .= "<p class='".$p_class."'>";
			$this->elements_html .= '    <select name="'.$id.'" class="select '.$element_class.'" id="'.$id.'">'.$select.'</select><label for="'.$id.'">'.$element['label'].$required.'</label>';
			$this->elements_html .= "</p>";
		}
		
		
		/**
         * textarea
         *
         * The textarea method creates textarea elements, and prefills them with $_POST values if available.
         * The method also checks against various input validation cases
         * @param string $id holds the key of the element
         * @param array $element data array of the element that should be created
         */
		function textarea($id, $element)
		{
			$p_class = $required = $element_class = $value = "";
			
			if(!empty($element['check'])) 
			{ 
				$required = "*"; 
				$element_class = $element['check'];
				$p_class = $this->check_element($id, $element);
			}
			
			if(!empty($_POST[$id])) $value = urldecode($_POST[$id]);
			
			$this->elements_html .= "<p class='".$p_class."'>";
			$this->elements_html .= '	 <label for="'.$id.'" class="textare_label hidden textare_label_'.$id.'">'.$element['label'].$required.'</label>';
			$this->elements_html .= '	 <textarea name="'.$id.'" class="text_area '.$element_class.'" cols="40" rows="7" id="'.$id.'" >'.$value.'</textarea>';
			$this->elements_html .= "</p>";
		}
		
		
		
		/**
         * decoy
         *
         * The decoy method creates input elements with type text but with an extra class that hides them
		 * The method is used to fool bots into filling the form element. Upon submission we check if the element contains any value, if so we dont submit the form
         * @param string $id holds the key of the element
         * @param array $element data array of the element that should be created
         */
		function decoy($id, $element)
		{
			$p_class = $required = $element_class = "";
			
			if(!empty($element['check'])) 
			{ 
				$this->check_element($id, $element);
			}
			
			$this->elements_html .= '<input type="text" name="'.$id.'" class="hidden '.$element_class.'" id="'.$id.'" value="" />';
		}
		
		/**
         * hidden
         *
         * The hidden method creates input elements with type hidden, and prefills them with values if available.
         * @param string $id holds the key of the element
         * @param array $element data array of the element that should be created
         */
		function hidden($id, $element)
		{
			$this->elements_html .= '<input type="hidden" name="'.$id.'" id="'.$id.'" value="'.$element['value'].'" />';
		}
		
		
		/**
         * Send the form
         *
         * The send method tries to send the form. It builds the necessary email and submits it via wp_mail
         */
		function send()
		{
			$new_post = array();
			foreach ($_POST as $key => $post) 
			{
				$new_post[str_replace('ace_','',$key)] = $post;
			}
			
			
			$mymail 	= empty($this->form_paramas['myemail']) ? $new_post['myemail'] : $this->form_paramas['myemail'];
			$myblogname = empty($this->form_paramas['myblogname']) ? $new_post['myblogname'] : $this->form_paramas['myblogname'];
			$subject 	= empty($new_post['subject']) ? __("New Message", 'ace_framework') : $new_post['subject'];
			
			$default_from = parse_url(get_option('home'));
			
			
			//set the email adress
			$from = "no-reply@wp-message.com";
			$usermail = false;
			
			if(!empty($default_from['host'])) $from = "no-reply@".$default_from['host'];
			$email_variations = array('mail','email','e-mail','your e-mail','your email', 'your mail', 'your mail adress', 'your email adress', 'your e-mail adress');
			foreach($email_variations as $key)
			{
				if(!empty($new_post[$key])) 
				{
					$from = $new_post[$key];
					$usermail = true;
				}
			}


			$to = urldecode( $mymail );
			$from = urldecode( $from );
			$subject = urldecode( $subject  . " (".__('sent by contact form at ','ace_framework').$myblogname.")" );
			$message = "";
			
			
			foreach($this->form_elements as $key => $element)
			{
				$key = ace_backend_safe_string($key);
				if(!empty($new_post[$key]))
				{
					if($element['type'] != 'hidden' && $element['type'] != 'decoy')
					{
						if($element['type'] == 'textarea') $message .= "<br/>";
						$message .= $element['label'].": ".nl2br(urldecode($new_post[$key]))."<br/>";
						if($element['type'] == 'textarea') $message .= "<br/>";
					}
				}
			}
			
			
			$header  = 'MIME-Version: 1.0' . "\r\n";
			$header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$header .= 'From:'. $from . " \r\n";
			mail($to, $subject, $message, $header);
			
			
			//autoresponder?
			if($usermail && !empty($this->form_paramas['autoresponder']))
			{
				$header  = 'MIME-Version: 1.0' . "\r\n";
				$header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$header .= 'From:'. urldecode( $this->form_paramas['autoresponder_email']) . " \r\n";
				$message = $this->form_paramas['autoresponder']."<br/><br/><br/><strong>Your Message:</strong><br/><br/>".$message;
				mail($from, $this->form_paramas['autoresponder_subject'], $message, $header);
			}
			
			return true;
			//return wp_mail( $to, $subject, $message , $header);
			
			
		}
		
		
		/**
         * Check the value of an element
         *
         * The check_element method creates checks if the submitted value of a post element is valid
         * @param string $id holds the key of the element
         * @param array $element data array of the element that should be created
         */
		function check_element($id, $element)
		{	

			if(isset($_POST) && count($_POST))
			{
				switch ($element['check'])
				{
					case 'is_empty':
					
						if(!empty($_POST[$id])) return "valid";
							
					break;
					
					case 'must_empty':
					
						if(isset($_POST[$id]) && $_POST[$id] == "") return "valid";
							
					break; 
					
					case 'is_email':
					
						if(preg_match("!^\w[\w|\.|\-]+@\w[\w|\.|\-]+\.[a-zA-Z]{2,4}$!", urldecode($_POST[$id]))) return "valid";
							
					break; 
				
				} //end switch
				
				$this->submit_form = false;
				return "error";
			}
		}
	}
}









