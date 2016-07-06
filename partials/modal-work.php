<?php
/**
 * 1. set up checkmarks to act appropriately
 * 2. Set up query string `f` the currently selected items.
 * 3. Set up checkmarks to check themselves on page load when a query string of `f` exists
 * 4. Set up the wp_query to pull from the current list as soon as the modal closes.
 */

global $wp_query;
$queried_tags = explode('+', $wp_query->query_vars['work_category']);

?>
<div id="work-modal" class="modal-container anim">
	<div class="modal-off-canvas"></div>
	<div class="modal-wrapper">
		<div class="modal">
			<section id="work-modal-head" class="page-hero">
				<div class="container bleed">
					<div class="column seven">
						<div class="content">
							<h1 class="h1"><em>Filter Work</em></h1>
							<div class="vertical-split"></div>
							<div class="description"><p class="supporting-copy">Click the topics that interest you most.</p></div>
						</div>
					</div>
					<div class="column five">
						<div class="content">
							<a href="#close" class="close caf" data-func="close_modal">&times;</a>
						</div>
					</div>
				</div>
			</section>
			<section id="add-items">
				<form class="container bleed one-third">
				<?php $args = array(
					'hide_empty' => false,
				); ?>
				<?php $terms = get_terms('hashtags', $args); ?>

					<?php $checked = ( count( $queried_tags ) > 1 || $queried_tags[0] !== "" ) ? '' : 'checked'; ?>
					<div class="column<?php echo ( count( $queried_tags ) > 1 || $queried_tags[0] !== "" ) ? '' : ' active'; ?>"><label tabindex="0"><div class="checkbox"><input type="checkbox" id="filtering_all" name="filtering[]" value="all" <?php echo $checked; ?> tabindex="-1" /><div class="outer"><div class="inner"></div></div></div>All</label></div>

				<?php foreach($terms as $term): ?>
					<?php if ( get_field( 'public', $term ) ): ?>
						<?php $checked = ( in_array( $term->slug, $queried_tags ) ) ? 'checked' : ''; ?>
						<div class="column<?php echo ( in_array( $term->slug, $queried_tags ) ) ? ' active' : ''; ?>"><label tabindex="0"><div class="checkbox"><input type="checkbox" id="filtering" name="filtering[]" value="<?php echo $term->slug; ?>" tabindex="-1" <?php echo $checked; ?> /><div class="outer"><div class="inner"></div></div></div><?php echo $term->name ?></label></div>
					<?php endif; ?>
				<?php endforeach; ?>
				</form>
				<div><p>&nbsp;</p><p class="text-center"><a href="/work" class="the_filter_link <?php pjaxify(); ?> button">update filters</a></p></div>
			</section>
		</div>
	</div>
</div>
<?php sendo()->capture_javascript_start(); ?>
<script type="text/javascript">

λ.build_work_filter_url = function() {
	var final_path = '/work/category/';

	$('input:checkbox', '#add-items').each(function() {
		if ( $(this).is(':checked') ) {
			if ( $(this).attr('id') === 'filtering_all' ) {
				final_path = '/work/';
			} else {
				final_path += $(this).val() + '+';
			}
		}
	});

	$('a.the_filter_link').attr('href', final_path.substr(0, final_path.length - 1));

	λ.refresh_page_after_closing_modal = true;
};

$('input:checkbox', '#add-items').on( 'change', function() {
	if ( $(this).is(':checked') ) {
		$(this).closest('div.column').addClass('active');
		if ( $(this).attr('id') === 'filtering_all' ) {
			$('input[name="filtering[]"]:not([value="all"])', '#add-items').prop('checked', false).closest('div.column').removeClass('active');
		} else {
			$('input[value="all"]', '#add-items').prop('checked', false).closest('div.column').removeClass('active');
		}
	} else {
		$(this).closest('div.column').removeClass('active');
	}

	if ($('input:checked', '#add-items').length === ($('input:checkbox', '#add-items').length - 1)) {
		$('input[value="all"]', '#add-items').click();
	}

	if ($('input:checked', '#add-items').length === 0) {
		setTimeout(function() { $('input[value="all"]', '#add-items').click(); }, 1);
	}

	λ.build_work_filter_url();
});
</script>
<?php sendo()->capture_javascript_end(); ?>
