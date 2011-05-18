function blankThis(postId) {
	if (postId != '') {
		jQuery('#IBlankThis-'+postId+' .ibt_counter').text('...');
		
		jQuery.post(blogUrl + "/wp-content/plugins/i-blank-this/blank.php",
			{ id: postId },
			function(data){
				jQuery('#IBlankThis-'+postId+' .ibt_counter').text(data);
			});
	}
}