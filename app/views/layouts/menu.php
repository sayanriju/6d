<?php if(AuthController::isAuthorized()):?>
<nav id="admin_menu" class="main">
	<a href="<?php echo FrontController::urlFor('post');?>" id="new_post_link" title="new post">new post</a>
    <a href="<?php echo FrontController::urlFor('posts');?>" id="all_posts_link" title="show all posts">posts</a>
	<a href="<?php echo FrontController::urlFor('addressbook');?>" id="addressbook_link" title="show your addressbook">addressbook</a>
	<a href="<?php echo FrontController::urlFor('profile');?>" id="profile_link" title="show your profile">profile</a>
	<?php if(AuthController::isSuperAdmin()):?>
	<a href="<?php echo FrontController::urlFor('members');?>" id="members_link" title="See all the members in your network">members</a>
	<a href="<?php echo FrontController::urlFor('member');?>" id="member_link" title="Create a new member">add a member</a>
	<?php endif;?>
 	<a href="<?php echo FrontController::urlFor('logout');?>" id="logout_link">logout</a>
</nav>
<?php endif;?>
