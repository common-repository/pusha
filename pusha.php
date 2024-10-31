<?php
/*
Plugin Name: Pusha
Plugin URI: http://www.pusha.se/pushaknapp/
Description: L&auml;gger automatiskt till en knapp till den svenska sociala bokm&auml;rkessajten Pusha tillsammans med dina bloggposter och p&aring; dina vanliga sidor, s&aring; att dina bes&ouml;kare enkelt kan r&ouml;sta p&aring; dina artiklar och ge dig fler bes&ouml;kare.
Version: 3.4
Author: Pusha
Author URI: http://www.pusha.se
*/

function push_button(){
	global $post;
	return '<script type="text/javascript">
		var pusha_url = "'.get_permalink($post->ID).'";
		var pusha_titel = "'.str_replace("'", "\'", get_the_title()).'";
		var pusha_bakgrund = "'.((get_option('push_bgcolor'))? get_option('push_bgcolor') : '#FFFFFF').'";
		var pusha_nyttfonster = '.((get_option('push_open_new_window')=='Ja')? 'true' : 'false').';
	</script>
	<script src="http://static.pusha.se/knapp.js" type="text/javascript"></script>';
}

function push_add_button($content){
	if ((is_page() && get_option('push_add_to_page') != 'Nej') || (!is_page() && get_option('push_add_to_full_post') != 'Nej') && !is_feed()) return $content . push_button();
	return $content;
}

function push(){
	echo push_button();
}

function push_options_form(){
	print('
			<div class="wrap">
				<h2>Pusha inst&auml;llningar</h2>
				<form id="form_pusha" name="form_pusha" action="' . get_bloginfo('wpurl') . '/wp-admin/index.php" method="post">
				<p>
					<label for="push_add_to_full_post">L&auml;gg automatiskt till en Pusha-knapp i dina bloggposter? *</label><br />
					<select name="push_add_to_full_post" id="push_add_to_full_post">
						<option value="Ja"'.((!get_option("push_add_to_full_post") || get_option("push_add_to_full_post")=='Ja')? ' selected="selected"' : '').'>Ja</option>
						<option value="Nej"'.((get_option("push_add_to_full_post")=='Nej')? ' selected="selected"' : '').'>Nej</option>
					</select>
				</p>
				<p>
					<label for="push_add_to_page">L&auml;gg automatiskt till en Pusha-knapp p&aring; vanliga sidor? *</label><br />
					<select name="push_add_to_page" id="push_add_to_page">
						<option value="Ja"'.((!get_option("push_add_to_page") || get_option("push_add_to_page")=='Ja')? ' selected="selected"' : '').'>Ja</option>
						<option value="Nej"'.((get_option("push_add_to_page")=='Nej')? ' selected="selected"' : '').'>Nej</option>
					</select>
				</p>
				<p>
					<label for="push_open_new_window">&Ouml;ppna l&auml;nken till Pusha i ett nytt f&ouml;nster?</label><br />
					<select name="push_open_new_window" id="push_open_new_window">
						<option value="Ja"'.((get_option("push_open_new_window")=='Ja')? ' selected="selected"' : '').'>Ja</option>
						<option value="Nej"'.((!get_option("push_open_new_window") || get_option("push_open_new_window")=='Nej')? ' selected="selected"' : '').'>Nej</option>
					</select>
				</p>
				<p>
					<label for="push_bgcolor">Bakgrundsf&auml;rg runt knappen: </label><br />
					<input type="text" name="push_bgcolor" id="push_bgcolor" value="'.((get_option("push_bgcolor"))? get_option("push_bgcolor") : '#FFFFFF').'" />
				</p>

				<p>* Om du valt "Nej". L&auml;gg till denna kod i dina temamallar p&aring; de st&auml;llen knappen ska visas:</p>
				<code>&lt;?php if (function_exists(\'push\')) push(); ?&gt;</code>

				<p class="submit">
					<input type="hidden" name="push_action" value="update" />
					<input type="submit" name="submit_button" value="Spara inst&auml;llningar" />
				</p>
				</form>
			</div>
	');
}

function push_menu_items(){
	add_options_page('Pusha inst&auml;llningar','Pusha',8,basename(__FILE__),'push_options_form');
}

function push_request_handler(){
	if (isset($_POST['push_action'])){
		if (isset($_POST['push_add_to_full_post'])) update_option('push_add_to_full_post',$_POST['push_add_to_full_post']);
		if (isset($_POST['push_add_to_page'])) update_option('push_add_to_page',$_POST['push_add_to_page']);
		if (isset($_POST['push_open_new_window'])) update_option('push_open_new_window',$_POST['push_open_new_window']);
		if (isset($_POST['push_bgcolor'])) update_option('push_bgcolor',$_POST['push_bgcolor']);

		header('Location: ' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=pusha.php&updated=true');
		exit;
	}
}

if (get_option('push_add_to_full_post') != 'Nej' || get_option('push_add_to_page') != 'Nej') add_filter('the_content', 'push_add_button');
add_action('admin_menu', 'push_menu_items');
add_action('init', 'push_request_handler', 9999);
?>