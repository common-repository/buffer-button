<?php
/*
Plugin Name: Buffer Button
Plugin URI: http://blog.bufferapp.com/buffer-button
Description: Add the Buffer Button to your blog
Author: Buffer (Joel Gascoigne)
Version: 0.2.5
Author URI: http://bufferapp.com
License: GPL2
*/

class buffer_button {
	
	function get_options() {
		// retrieve saved parameters, and set defaults
		$buffer_button_count = get_option("buffer_button_count");
		if(empty($buffer_button_count)) $buffer_button_count = "horizontal";
		$buffer_button_via = get_option("buffer_button_via");
		if(empty($buffer_button_via)) $buffer_button_via = "bufferapp";
		$buffer_button_custom_css = get_option("buffer_button_custom_css");
		if(empty($buffer_button_custom_css)) $buffer_button_custom_css = "float: right;";
		$buffer_button_position = get_option("buffer_button_position");
		if(empty($buffer_button_position)) $buffer_button_position = "both";
		$buffer_button_only_single = get_option("buffer_button_only_single");
		if(empty($buffer_button_only_single)) $buffer_button_only_single = false;
		
		return 	array(
					'buffer_button_count' => $buffer_button_count,
					'buffer_button_via' => $buffer_button_via,
					'buffer_button_custom_css' => $buffer_button_custom_css,
					'buffer_button_position' => $buffer_button_position,
					'buffer_button_only_single' => $buffer_button_only_single
				);
	}
	
	function the_content($content) {
		
		// grab the options
		$options = call_user_func(array('buffer_button', 'get_options'));
		
		if($options['buffer_button_only_single'] && !is_single()) return $content;
		
		$button = '<div style="'.$options['buffer_button_custom_css'].'"><a href="http://bufferapp.com/add" class="buffer-add-button" data-text="'.the_title('','',false).'" data-url="'.get_permalink().'" data-via="'.$options['buffer_button_via'].'" data-count="'.$options['buffer_button_count'].'">Buffer</a><script type="text/javascript" src="http://static.bufferapp.com/js/button.js"></script></div>';
		
		if($options['buffer_button_position'] == "both") return $button.$content.$button;
		elseif($options['buffer_button_position'] == "above") return $button.$content;
		elseif($options['buffer_button_position'] == "below") return $content.$button;
	}
	
	function the_excerpt($excerpt) {
		
		// grab the options
		$options = call_user_func(array('buffer_button', 'get_options'));
		
		$button = '<div style="'.$options['buffer_button_custom_css'].'"><a href="http://bufferapp.com/add" class="buffer-add-button" data-text="'.the_title('','',false).'" data-url="'.get_permalink().'" data-via="'.$options['buffer_button_via'].'" data-count="'.$options['buffer_button_count'].'">Buffer</a><script type="text/javascript" src="http://static.bufferapp.com/js/button.js"></script></div>'.$content;
		$excerpt = get_the_excerpt();
		
		if($options['buffer_button_only_single'] && !is_single()) {
			echo $excerpt;
		} else {
			if($options['buffer_button_position'] == "both" || $options['buffer_button_position'] == "above") {
				$excerpt = substr($excerpt, 6, strlen($excerpt));
			}
			if($options['buffer_button_position'] == "both") echo $button.$excerpt.$button;
			elseif($options['buffer_button_position'] == "above") echo $button.$excerpt;
			elseif($options['buffer_button_position'] == "below") echo $excerpt.$button;
		}
	}

	function admin_menu() {
		add_options_page('Buffer Button Options', 'Buffer Button', 'manage_options', 'buffer-button', array('buffer_button', 'options_page'));
	}

	function options_page() {
		if (isset($_POST['buffer_save'])) call_user_func(array('buffer_button', 'save_options'), $_POST);
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		
		// grab the options
		$options = call_user_func(array('buffer_button', 'get_options'));
		
		$html = '<div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div>
		<h2>Buffer Button Settings</h2>

		<form name="buffer_button_form" method="post" action="options-general.php?page=buffer-button">
		<table class="form-table">
			<tbody>
				<tr valign="top">
				<th scope="row"><label for="buffer_button_count">Buffer Button Count Style</label></th>
				<td>
					<select name="buffer_button_count" id="buffer_button_count">
						<option value="none"';
						if($options['buffer_button_count']=='none') $html .= ' selected="selected"';
						$html .= '>None</option>';
						$html .= '<option value="horizontal"';
						if($options['buffer_button_count']=='horizontal') $html .= ' selected="selected"';
						$html .= '>Horizontal (small)</option>';
						$html .= '<option value="vertical"';
						if($options['buffer_button_count']=='vertical') $html .= ' selected="selected"';
						$html .= '>Vertical (big)</option>
					</select>
				</td>
				</tr>
				<tr valign="top">
				<th scope="row"><label for="buffer_button_position">Position of the Buffer Button</label></th>
				<td>
					<select name="buffer_button_position" id="buffer_button_position">
						<option value="both"';
						if($options['buffer_button_position']=='both') $html .= ' selected="selected"';
						$html .= '>Above and below the post</option>';
						$html .= '<option value="above"';
						if($options['buffer_button_position']=='above') $html .= ' selected="selected"';
						$html .= '>Only above the post</option>';
						$html .= '<option value="below"';
						if($options['buffer_button_position']=='below') $html .= ' selected="selected"';
						$html .= '>Only below the post</option>
					</select>
				</td>
				</tr>
				<tr valign="top">
				<th scope="row"><label for="buffer_button_via">Mention this @username:</label></th>
				<td>
				@<input name="buffer_button_via" type="text" id="buffer_button_via" value="'.$options['buffer_button_via'].'" class="medium-text"></td>
				</tr>
				<tr valign="top">
				<th scope="row"><label for="buffer_button_only_single">Only show the button on individual post pages?:</label></th>
				<td>
				<input name="buffer_button_only_single" type="checkbox" id="buffer_button_only_single" ';
				if(!empty($options["buffer_button_only_single"])) $html .= " checked=\"checked\"";
				$html .=' ></td>
				</tr>
				<tr valign="top">
				<th scope="row"><label for="buffer_button_custom_css">Custom CSS styling:</label></th>
				<td>
				<input name="buffer_button_custom_css" type="text" id="buffer_button_custom_css" value="'.$options['buffer_button_custom_css'].'" class="medium-text"></td>
				</tr>
			</tbody>
		</table>


		<p class="submit"><input type="submit" name="buffer_save" id="submit" class="button-primary" value="Save Changes"></p></form>
		</div>';
		echo $html;
	}
	
	function save_options($post) {
		update_option('buffer_button_count', $post['buffer_button_count']);
		update_option('buffer_button_via', $post['buffer_button_via']);
		update_option('buffer_button_position', $post['buffer_button_position']);
		update_option('buffer_button_custom_css', $post['buffer_button_custom_css']);
		update_option('buffer_button_only_single', ($post['buffer_button_only_single']=="on"));
		echo '<div id="message" class="updated"><p>Buffer Button settings updated.</p></div>';
	}
}

add_action('the_content', array('buffer_button', 'the_content'));
add_action('the_excerpt', array('buffer_button', 'the_excerpt'));
add_action('admin_menu', array('buffer_button', 'admin_menu'));