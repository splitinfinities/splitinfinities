<footer class="content-info">
	<div class="container">
		<div class="column four">
			<p class="muted"><small>William M. Riley</small></p>
			<p class="muted"><small><a href="/?feed=rss2" target="_blank">RSS</a>  |  <a href="https://www.tinyletter.com/mashthekeyboard" target="_blank">TinyLetter</a></small></p>
		</div>
		<div class="column four"></div>
		<div class="column four">
			<p class="muted text-right caf" data-func="contact_swap" data_open="intro"><small>Contact</small><small>will@mashthekeyboard.com</small></p>
		</div>
	</div>
</footer>
<?php sendo()->capture_javascript_start(); ?>
<script type="text/javascript">
	λ.contact_swap = function(el) {
		if ($(el).attr('data_open') === 'intro') {
			$(el).attr('data_open', 'email');
		} else {
			$(el).attr('data_open', 'intro');
		}

		λ.contact_swap = null;
		$(el).removeClass('caf');
	};
</script>
<?php sendo()->capture_javascript_end(); ?>
