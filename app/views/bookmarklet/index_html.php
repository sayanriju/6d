<h1>Bookmarklets</h1>
<dl>
	<dt>Post</dt>
	<dd><a href="javascript:(function(){if(document.getElementById('__6d_post_script') === null){var s=document.createElement('script');s.setAttribute('language','JavaScript');s.setAttribute('src','<?php echo App::url_for('js/bookmarklets/post/main.js');?>?time=' + (new Date()).getTime());s.id='__6d_post_script';document.body.appendChild(s);}else{main.execute();}})();">New Post</a></dd>
</dl>
<textarea cols="50" rows="10" id="pre_code"></textarea>
<pre>
	<code id="post_code"></code>
</pre>
<button id="go">GO</button>
