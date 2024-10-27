jQuery(document).ready(function($) {
    
	$('#upload_img_button').click(function() {
        tb_show('Upload an image', 'media-upload.php?referer=adbuddy&type=image&TB_iframe=true&post_id=0', false);
        return false;
    });
	
	window.send_to_editor = function(html) {
      
	  var image_url = $('img',html).attr('src');
	  $('#adbuddy_display_img').val(image_url);
      tb_remove();
	  
	  $('#upload_image_preview img').attr('src',image_url);
	
    }
	
});

