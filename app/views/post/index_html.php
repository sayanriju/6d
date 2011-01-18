<nav class="page">
<?php if($page > 1):?>
	<a href="<?php echo Application::url_with_member(($name === 'index' ? 'blog' : $name) . '/' . ($page - 1));?>"><span>&lt;</span></a>
<?php else:?>
	<a class="off" href="javascript:void(0);"><span>&lt;</span></a>
<?php endif;?>

<?php if($page*$limit < $total->number):?>
	<a class="" href="<?php echo Application::url_with_member(($name === 'index' ? 'blog' : $name) . '/' . ($page + 1));?>"><span>&gt;</span></a>
<?php else:?>
	<a class="off" href="javascript:void(0);"><span>&gt;</span></a>
<?php endif;?>
</nav>
<section class="posts">
<?php foreach($posts as $post):?>
	<?php require('show_html.php');?>
<?php endforeach;?>
</section>