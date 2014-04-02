<link href="<?php echo $this->handleCallbackJsDir;?>/swfupload.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/swfupload/swfupload.js"></script>
<script type="text/javascript" src="<?php echo $this->handleCallbackJsDir;?>/swfupload.queue.js"></script>
<script type="text/javascript" src="<?php echo $this->handleCallbackJsDir;?>/fileprogress.js"></script>
<script type="text/javascript" src="<?php echo $this->handleCallbackJsDir;?>/handlers.js"></script>
<script type="text/javascript">
	var swfu;
	var jsonPostParams = {};
		jsonPostParams.PHPSESSID = "<?php echo session_id(); ?>";
		jsonPostParams.r = "admin/uploadHeadImg";
	<?php foreach( $this->postData as $k=>$v ):?>
		jsonPostParams.<?php echo $k?> = "<?php echo $v;?>";
	<?php endforeach;?>
	
	window.onload = function() {
		var settings = {
			flash_url : "<?php echo UPLOAD_DOMAIN?>/js/swfupload/swfupload.swf",
			upload_url: "<?php echo UPLOAD_DOMAIN;?>/index.php",
			post_params: jsonPostParams,
			file_size_limit : "<?php echo $this->filesize?>",
			file_types : "<?php echo $this->filetypes;?>",
			file_types_description : "<?php echo $this->filetypesDescription?>",
			file_upload_limit : <?php echo $this->fileUploadLimit;?>,
			file_queue_limit : <?php echo $this->fileQueueLimit;?>,
			custom_settings : {
				progressTarget : "fsUploadProgress",
				cancelButtonId : "btnCancel"
			},
			debug: false,

			// Button settings
			button_image_url: "<?php echo $this->handleCallbackJsDir;?>/bg_btn.png",
			button_width: "150",
			button_height: "29",
			button_placeholder_id: "spanButtonPlaceHolder",
			button_text: '<span class="theFont">选择文件并上传</span>',
			button_text_style: ".theFont { font-size: 16; }",
			button_text_left_padding: 12,
			button_text_top_padding: 3,
			
			// The event handler functions are defined in handlers.js
			file_queued_handler : fileQueued,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,
			queue_complete_handler : queueComplete	// Queue plugin event
		};

		swfu = new SWFUpload(settings);
     };
</script>
<div id="fsUploadProgress"></div>
<!--<div id="divStatus">0 Files Uploaded</div>-->
<div id="jqSelectFiles">
	<span id="spanButtonPlaceHolder"></span>
	<input id="btnCancel" type="button" value="　取消上传　" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;" />
</div>