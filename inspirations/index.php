<?php
if ($_SERVER['HTTP_X_PJAX'] == 'true'): ?>
<div id="inspirations" class="panel">
	<div class="container">
		<h1>Inspirations</h1>
		<p><a href="http://en.wikipedia.org/wiki/JSON" target="_blank" title="JSON" class="underline">JSON</a> is a data format, or a kind of language, that&rsquo;s used between systems to communicate a lot of information about a thing. Here, I&rsquo;m using <a href="http://en.wikipedia.org/wiki/Quantified_Self" target="_blank" title="Quantified Self" class="underline">&ldquo;quantified self&rdquo;</a> practices to capture as much of who I am as&nbsp;possible. </p>
		<pre class="json"><code class="json"><?php include('../json.php'); ?></code></pre>
	</div>
</div>
<script>$('pre code').each(function(i, e) {hljs.highlightBlock(e)})</script>
<?php
else:
	header( 'Location: http://'.$_SERVER['HTTP_HOST'].'?trigger=inspirations' );
endif;
?>
