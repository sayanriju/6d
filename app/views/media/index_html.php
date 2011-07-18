<?php if(AuthController::is_authed() && AuthController::$current_user->id == AppResource::$member->id):?>
<link rel="stylesheet" href="<?php echo App::url_for("plugins/jquery-file-upload/jquery.fileupload-ui.css");?>" />
<h1>Upload Photos</h1>
<form id="photo_upload" action="<?php echo AppResource::url_for_user("photos.json");?>" method="post" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo Settings::$max_filesize;?>" />
    <input type="file" name="files[]" multiple />
    <button>Upload</button>
    <div>Upload files</div>
</form>
<table id="photos"></table>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/jquery-ui.min.js"></script>
<script src="<?php echo App::url_for("plugins/jquery-file-upload/jquery.fileupload.js");?>"></script>
<script src="<?php echo App::url_for("plugins/jquery-file-upload/jquery.fileupload-ui.js");?>"></script>

<script>
$(function () {
    $('#photo_upload').fileUploadUI({
        uploadTable: $('#photos'),
        downloadTable: $('#photos'),
        buildUploadRow: function (files, index) {
            return $('<tr><td>' + files[index].name + '<\/td>' +
                    '<td class="file_upload_progress"><div><\/div><\/td>' +
                    '<td class="file_upload_cancel">' +
                    '<button class="ui-state-default ui-corner-all" title="Cancel">' +
                    '<span class="ui-icon ui-icon-cancel">Cancel<\/span>' +
                    '<\/button><\/td><\/tr>');
        },
        buildDownloadRow: function (file) {
            return $('<tr><td><img src="' + file[0].thumbnail_src + '" />' + file[0].name + '<\/td><\/tr>');
        }
    });
});
</script>
<?php endif;?>

<section id="all_photos">
<ul>
	<?php foreach($media as $post):?>
		<li>
			<a href="<?php echo AppResource::url_for_member('blog/' . $post->url);?>" title="<?php echo $post->title;?>">
				<img src="<?php echo urldecode($post->src);?>" alt="<?php echo $post->title;?>" />
			</a>
		<div class="photo_info">
			<a href="<?php echo AppResource::url_for_member('blog/' . $post->url);?>" rel="bookmark" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a>
		</div>
		</li>
	<?php endforeach;?>
</ul>
