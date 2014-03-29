<?php
if ($_SERVER['HTTP_X_PJAX'] == 'true'): ?>
<div id="inspirations" class="panel" data-snare data-kick>
	<div class="container">
		<h1>a json string of things and people who made me</h1>
		<p>JSON is a data type, or a kind of language, used between systems to communicate a lot of information about a thing. Here, I&rsquo;m using quantified self practices to capture as much of who I am as possible. </p>
		<pre class="json"><code class="json"><?php include('json.php'); ?></code></pre>
		<h6 class="pull-right">databases store real life connections in binary</h6>
		<p>this is in no way complete. You can&rsquo;t ever get a complete snapshot of a person, but it&rsquo;s worth trying. </p>
	</div>
</div>
<script>$('pre code').each(function(i, e) {hljs.highlightBlock(e)})</script>
<?php
else:
	header( 'Location: http://'.$_SERVER['HTTP_HOST'].'?trigger=inspirations' );
endif;
?>
