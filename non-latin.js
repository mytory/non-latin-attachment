jQuery(document).ready(function($){

	// GD bbPress Attachments
	$('[id^="d4p-bbp-attachment_"]:not(:has("img"))').each(function(){
		var $this = $(this);
		
		var parameter = {
			id: $(this).attr('id').replace(/d4p-bbp-attachment_/,''),
			action: 'filename_for_download'
		};
		
		$.get(nlf.ajaxurl, parameter,function(title){
			$this.find('a:first').text(title).attr('title',title);
		});
	});

	// replace download link
	var attachments = [];
	$('a[href^="' + nlf.upload_baseurl + '"]').each(function(){
		attachments.push($(this).attr('href'));
	});
	if(attachments.length > 0){
		var post_vars = {
			'action': 'nlf_get_download_url',
			'attachments': attachments
		};
		$.post(nlf.ajaxurl, post_vars, function(result){
			for (var i = result.length - 1; i >= 0; i--) {
				var obj = result[i];
				$('a[href="' + obj.guid + '"]').attr('href', obj.download_url);
			}
		}, 'json');
	}
});