<div class="horizontal slider container">
	<h1>Databases on <?php echo $host;?></h1>
	<ul class="horizontal">
	<?php foreach($databases as $db):?>
		<li class="closed"><a href="javascript:void(0);" class="database"><span><?php echo $db->Database;?></span></a></li>
	<?php endforeach;?>
	</ul>
</div>
<div style="clear: both"></div>
<div class="container">
	<h1>Tables in <span id="db_name"></span>
	<ul id="tables" class="horizontal" style="display: none;"></ul>
</div>
<div style="clear: both"></div>
<textarea id="query" cols="100" rows="20"></textarea>
<a href="javascript:void(0);" id="execute_link">execute!</a>
<div id="query_results"></div>