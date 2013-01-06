jQuery(document).ready(function($){
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
});;