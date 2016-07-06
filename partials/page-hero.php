<?php
global $wp_query;
if( empty($wp_query->post->post_parent) ) {
	$parent = $wp_query->post->ID;
} else {
	$parent = $wp_query->post->post_parent;
} ?>
<section class="hero bg text-center" <?php if ( get_field( 'hero_bg' ) ): ?>style="background-image: url('<?php the_field('hero_bg') ?>')" <?php endif; ?>>
	<div class="content">
		<div class="full">
			<h1><?php echo ( get_field('hero_head') ) ? the_field('hero_head') : the_title(); ?></h1>
			<p><?php the_field('hero_copy'); ?></p>
			<?php if (get_field('hero_call_to_action_link')): ?>
				<div class="button-container">
					<a href="<?php the_field('hero_call_to_action_link'); ?>" class="button"><?php the_field('hero_call_to_action_copy'); ?></a>
				</div>
			<?php endif; ?>
		</div>
		<?php if ( list_pages( array( 'child_of' => $parent, 'echo' => 0 ) ) ): ?>
				<?php
				$section_id = empty( $post->ancestors ) ? $post->ID : end( $post->ancestors );
				$locations = get_nav_menu_locations();
				$menu = wp_get_nav_menu_object( $locations[ 'primary_navigation' ] );
				$menu_items = wp_get_nav_menu_items( $menu->term_id, array( 'post_parent' => $section_id ) ); ?>

				<?php if ( !empty( $menu_items ) ): ?>
					<ul class="tabs">
						<?php foreach( $menu_items as $menu_item ): ?>
							<li<?php echo ($menu_item->object_id == $post->ID) ? ' class="active"' : ""; ?>><a href="<?php echo $menu_item->url; ?>"><?php echo $menu_item->title; ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
