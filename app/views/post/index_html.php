<?php require('views/today/index_html.php');?>
<nav id="pager">
<?php if(count($posts) > 0 && $page > 1):?>
	<a href="<?php echo Application::urlForWithMember(($name === 'index' ? null : $name . '/')) . ($page > 1 ? $page-1 : null) . ($this->q !== null ? '?q=' . $this->q : null);?>" title="View newer posts">&lt;&lt; newer</a>
<?php else:?>
	<span>&lt;&lt; newer</span>
<?php endif;?>

<?php if(count($posts) >= $limit):?>
	<a href="<?php echo Application::urlForWithMember(($name === 'index' ? null : $name . '/')) . ($page === 0 ? $page+2 : $page+1). ($this->q !== null ? '?q=' . $this->q : null);?>" title="View older posts">older &gt;&gt;</a>
<?php else:?>
	<span>older &gt;&gt;</span>
<?php endif;?>
</nav>