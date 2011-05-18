<?php
/*
Plugin Name: I Blank This
Plugin URI: http://slobros.com/2011/wp/plugins/i-blank-this/
Description: "I Blank This" is an "I Like This" button, except you can replace the text with anything you want.
Version: 1.0
Author: SloBros
Author URI: http://slobros.com/

Copyright 2011  SloBros  (email : support@slobros.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
*/
#### INSTALL PROCESS ####
$ibt_dbVersion = "1.0";

function setOptionsIBT() {
	global $wpdb;
	global $ibt_dbVersion;
	
	$ibt_table_name = $wpdb->prefix . "iblankthis_votes";
	if($wpdb->get_var("show tables like '$ibt_table_name'") != $ibt_table_name) {
		$sql = "CREATE TABLE " . $ibt_table_name . " (
			id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
			time TIMESTAMP NOT NULL,
			post_id BIGINT(20) NOT NULL,
			ip VARCHAR(15) NOT NULL,
			UNIQUE KEY id (id)
		);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		add_option("ibt_dbVersion", $ibt_dbVersion);
	}
	
	add_option('ibt_jquery', '1', '', 'yes');
	add_option('ibt_onPage', '1', '', 'yes');
	add_option('ibt_text', 'I like this', '', 'yes');
	add_option('ibt_after_click1', 'person likes this', '', 'yes');
	add_option('ibt_after_click2', 'people like this', '', 'yes');
	add_option('ibt_i_blanked_this_text', 'I liked this', '', 'yes');

}

register_activation_hook(__FILE__, 'setOptionsIBT');

function unsetOptionsIBT() {
	global $wpdb;
	$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."iblankthis_votes");

	delete_option('ibt_jquery');
	delete_option('ibt_onPage');
	delete_option('ibt_text');
	delete_option('ibt_most_blanked_posts');
	delete_option('ibt_dbVersion');
	delete_option('ibt_after_click1');
	delete_option('ibt_after_click2');
	delete_option('ibt_i_blanked_this_text');

}

register_uninstall_hook(__FILE__, 'unsetOptionsIBT');

#### ADMIN OPTIONS ####
function IBlankThisAdminMenu() {
	add_options_page('I Blank This', 'I Blank This', 'activate_plugins', 'IBlankThisAdminMenu', 'IBlankThisAdminContent');
}
add_action('admin_menu', 'IBlankThisAdminMenu');

function IBlankThisAdminRegisterSettings() {
	register_setting( 'ibt_options', 'ibt_jquery' );
	register_setting( 'ibt_options', 'ibt_onPage' );
	register_setting( 'ibt_options', 'ibt_text' );
	register_setting( 'ibt_options', 'ibt_after_click1' );
	register_setting( 'ibt_options', 'ibt_after_click2' );
	register_setting( 'ibt_options', 'ibt_i_blanked_this_text' );

}
add_action('admin_init', 'IBlankThisAdminRegisterSettings');

function IBlankThisAdminContent() {
?>
<div class="wrap">
	<h2>"I Blank This" Options</h2>
	<br class="clear" />
			
	<div id="poststuff" class="ui-sortable meta-box-sortables">
		<div id="IBlankThisoptions" class="postbox">
		<h3><?php _e('Configuration'); ?></h3>
			<div class="inside">
			<form method="post" action="options.php">
			<?php settings_fields('ibt_options'); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ibt_jquery"><?php _e('jQuery framework', 'i-blank-this'); ?></label></th>
					<td>
						<select name="ibt_jquery" id="ibt_jquery">
							<?php echo get_option('ibt_jquery') == '1' ? '<option value="1" selected="selected">'.__('Enabled', 'i-blank-this').'</option><option value="0">'.__('Disabled', 'i-blank-this').'</option>' : '<option value="1">'.__('Enabled', 'i-blank-this').'</option><option value="0" selected="selected">'.__('Disabled', 'i-blank-this').'</option>'; ?>
						</select>
						<span class="description"><?php _e('Disable it if you already have the jQuery framework enabled in your theme.', 'i-blank-this'); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><legend>Text descriptions</legend></th>
					<td>	
						<label for="ibt_text">
						<input type="text" name="ibt_text" id="ibt_text" value="<?php echo get_option('ibt_text'); ?>" />
						</label><span class="description"> Text before vote, e.g. "I like this"</span>
						
						<br />
						<label for="ibt_i_blanked_this_text">
						<input type="text" name="ibt_i_blanked_this_text" id="ibt_i_blanked_this_text" value="<?php echo get_option('ibt_i_blanked_this_text'); ?>" />
						</label><span class="description"> Text after vote, e.g. "I liked this"</span>
						
						<br /><strong>1</strong>
						<label for="ibt_after_click1">
						<input type="text" name="ibt_after_click1" id="ibt_after_click1" value="<?php echo get_option('ibt_after_click1'); ?>" />
						</label><span class="description"> e.g. "person likes this"</span>
						
						<br /><strong>2</strong>
						<label for="ibt_after_click2">
						<input type="text" name="ibt_after_click2" id="ibt_after_click2" value="<?php echo get_option('ibt_after_click2'); ?>" />
						</label><span class="description"> e.g. "people like this"</span>

						</td>
				</tr>
				<tr valign="top">
					<th scope="row"><legend><?php _e('Automatic display', 'i-blank-this'); ?></legend></th>
					<td>
						<label for="ibt_onPage">
						<?php echo get_option('ibt_onPage') == '1' ? '<input type="checkbox" name="ibt_onPage" id="ibt_onPage" value="1" checked="checked">' : '<input type="checkbox" name="ibt_onPage" id="ibt_onPage" value="1">'; ?>
						<?php _e('<strong>On all posts</strong> (home, archives, search) at the bottom of the post', 'i-blank-this'); ?>
						</label>
						<p class="description"><?php _e('If you disable this option, you have to manually put the code', 'i-blank-this'); ?><code>&lt;?php if(function_exists(getIBlankThis)) getIBlankThis('get'); ?&gt;</code> <?php _e('wherever you want in your template.', 'i-blank-this'); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><input class="button-primary" type="submit" name="Save" value="<?php _e('Save Options', 'i-blank-this'); ?>" /></th>
					<td></td>
				</tr>
			</table>
			</form>
			</div>
		</div>
	</div>
</div>
<?php
}
####


#### WIDGET ####
function ibt_most_blanked_posts($numberOf, $before, $after, $show_count) {
	global $wpdb;

    $request = "SELECT ID, post_title, meta_value FROM $wpdb->posts, $wpdb->postmeta";
    $request .= " WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id";
    $request .= " AND post_status='publish' AND post_type='post' AND meta_key='_blanked'";
    $request .= " ORDER BY $wpdb->postmeta.meta_value+0 DESC LIMIT $numberOf";
    $posts = $wpdb->get_results($request);

    foreach ($posts as $post) {
    	$post_title = stripslashes($post->post_title);
    	$permalink = get_permalink($post->ID);
    	$post_count = $post->meta_value;
    	
    	echo $before.'<a href="' . $permalink . '" title="' . $post_title.'" rel="nofollow">' . $post_title . '</a>';
		echo $show_count == '1' ? ' ('.$post_count.')' : '';
		echo $after;
    }
}

function add_widget_ibt_most_blanked_posts() {
	function widget_ibt_most_blanked_posts($args) {
		extract($args);
		$options = get_option("ibt_most_blanked_posts");
		if (!is_array( $options )) {
			$options = array(
			'title' => 'Most voted posts',
			'number' => '5',
			'show_count' => '0'
			);
		}
		$title = $options['title'];
		$numberOf = $options['number'];
		$show_count = $options['show_count'];
		
		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo '<ul class="mostlikedposts">';

		ibt_most_blanked_posts($numberOf, '<li>', '</li>', $show_count);
		
		echo '</ul>';
		echo $after_widget;
	}	
		wp_register_sidebar_widget('IBlankThis1','Most voted posts', 'widget_ibt_most_blanked_posts');

	
	function options_widget_ibt_most_blanked_posts() {
		$options = get_option("ibt_most_blanked_posts");
		
		if (!is_array( $options )) {
			$options = array(
			'title' => 'Most voted posts',
			'number' => '5',
			'show_count' => '0'
			);
		}
		
		if (isset($_POST['mlp-submit'])) {
			$options['title'] = htmlspecialchars($_POST['mlp-title']);
			if (ctype_digit($_POST['mlp-number'])) {
				$options['number'] = ($_POST['mlp-number']);

			} else {
				$options['number'] = '0';
			}
							
			if (isset($_POST['mlp-show-count'])) {
				$options['show_count'] = '1';
			} else {
				$options['show_count'] = '0';
			}

			if ( $options['number'] > 15) { $options['number'] = 15; }
			
			update_option("ibt_most_blanked_posts", $options);
		}
		?>
		<p><label for="mlp-title"><?php _e('Title:', 'i-blank-this'); ?><br />
		<input class="widefat" type="text" id="mlp-title" name="mlp-title" value="<?php echo $options['title'];?>" /></label></p>
		
		<p><label for="mlp-number"><?php _e('Number of posts to show:', 'i-blank-this'); ?><br />
		<input type="text" id="mlp-number" name="mlp-number" style="width: 25px;" value="<?php echo $options['number'];?>" /> <small>(max. 15)</small></label></p>
		
		<p><label for="mlp-show-count"><input type="checkbox" id="mlp-show-count" name="mlp-show-count" value="1"<?php if($options['show_count'] == '1') echo 'checked="checked"'; ?> /> <?php _e('Show post count', 'i-blank-this'); ?></label></p>
		
		<input type="hidden" id="mlp-submit" name="mlp-submit" value="1" />
		<?php
	}
		wp_register_widget_control('IBlankThis1','Most voted posts', 'options_widget_ibt_most_blanked_posts');

} 

add_action('init', 'add_widget_ibt_most_blanked_posts');
####


#### FRONT-END VIEW ####
function getIBlankThis($arg) {
	global $wpdb;
	$post_ID = get_the_ID();
	$ip = $_SERVER['REMOTE_ADDR'];
	
    $blanked = get_post_meta($post_ID, '_blanked', true) != '' ? get_post_meta($post_ID, '_blanked', true) : '0';
	$IBTvoteStatusByIp = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."iblankthis_votes WHERE post_id = '$post_ID' AND ip = '$ip'");
		   
		$ibt_i_blanked_this_text = get_option('ibt_i_blanked_this_text');

	    if (!isset($_COOKIE['blanked-'.$post_ID]) && $IBTvoteStatusByIp == 0) {
	
	$ibt_counter = '<a onclick="blankThis('.$post_ID.');">'.get_option('ibt_text').'</a>';
	}	else {
		$ibt_i_blanked_this_text = get_option('ibt_i_blanked_this_text');
		$ibt_counter = $ibt_i_blanked_this_text;
    }
	
    $ibt_after_click1 = get_option('ibt_after_click1');
	$ibt_after_click2 = get_option('ibt_after_click2');
    $IBlankThis = '<div id="IBlankThis-'.$post_ID.'" class="IBlankThis">';
    $IBlankThis .= '<span class="ibt_counter">'.$ibt_counter.'</span>';

	if ($blanked == 1) {
			$IBlankThis .= '<br /><span class="IBTnumbers">'.$blanked.' '.$ibt_after_click1.'</span></div>';
		} elseif ($blanked > 1) {
			$IBlankThis .= '<br /><span class="IBTnumbers">'.$blanked.' '.$ibt_after_click2.'</span></div>';
		} else {
			$IBlankThis .= '</div>';
	}
	
	
    if ($arg == 'put') {
	    return $IBlankThis;
    }
    else {
    	echo $IBlankThis;
    }
	}


if (get_option('ibt_onPage') == '1') {
	function putIBlankThis($content) {
		if(!is_feed() && !is_page()) {
			$content.= getIBlankThis('put');
		}
	    return $content;
	}

	add_filter('the_content', 'putIBlankThis');
}

function enqueueScripts() {
	if (get_option('ibt_jquery') == '1') {
	    wp_enqueue_script('IBlankThis', WP_PLUGIN_URL.'/i-blank-this/js/i-blank-this.js', array('jquery'));	
	}
	else {
	    wp_enqueue_script('IBlankThis', WP_PLUGIN_URL.'/i-blank-this/js/i-blank-this.js');	
	}
}

function addHeaderLinks() {
	echo '<link rel="stylesheet" type="text/css" href="'.WP_PLUGIN_URL.'/i-blank-this/css/i-blank-this.css" media="screen" />'."\n";
	echo '<script type="text/javascript">var blogUrl = \''.get_bloginfo('wpurl').'\'</script>'."\n";
}

add_action('init', 'enqueueScripts');
add_action('wp_head', 'addHeaderLinks');
?>