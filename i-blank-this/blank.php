<?php
require_once '../../../wp-config.php';

global $wpdb;
$post_ID = $_POST['id'];
$ip = $_SERVER['REMOTE_ADDR'];
$blank = get_post_meta($post_ID, '_blanked', true);

if($post_ID != '') {
	$IBTvoteStatusByIp = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."iblankthis_votes WHERE post_id = '$post_ID' AND ip = '$ip'");
	
    if (!isset($_COOKIE['blanked-'.$post_ID]) && $IBTvoteStatusByIp == 0) {
		$blankNew = $blank + 1;
		update_post_meta($post_ID, '_blanked', $blankNew);
		//blankNew deprecauted

		setcookie('blanked-'.$post_ID, time(), time()+3600*24*365, '/');
		$wpdb->query("INSERT INTO ".$wpdb->prefix."iblankthis_votes VALUES ('', NOW(), '$post_ID', '$ip')");
		
		$text_right_after_click = get_option('ibt_i_blanked_this_text');
		echo $text_right_after_click;
	}
	else {
		echo $blank;
	}
}
?>