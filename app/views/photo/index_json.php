	[<?php
for($i=0; $i < count($photos); $i++){
	$photo = $photos[$i];
	printf('{"title":"%s",', $photo->title);
	printf('"little_src":"%s",', PhotoResource::getLittleSrc($photo->src));
	printf('"src":"%s",', $photo->src);
	printf('"width":"%s"}%s', PhotoResource::getThumbnailWidth($photo->src), ($i < count($photos) - 1 ? ',' : null));
}
?>]
