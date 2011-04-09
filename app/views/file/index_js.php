
<script>
	var result = <?php echo json_encode($result);?>;
	top.chin.default_center.post("photos_were_uploaded", null, result);
</script>