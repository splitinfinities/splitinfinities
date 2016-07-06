<?php global $post; ?>
<section <?php if (get_sub_field('section_name')): ?>id="<?php echo $post->post_name.'-'.sanitize_title(get_sub_field('section_name')); ?>"<?php endif; ?> class="layout-hero anim fadeIn<?php echo(get_sub_field('breathing_room')) ? ' '.get_sub_field('breathing_room') : ''; ?> <?php echo(get_field('navigation_overlay')) ? ' '.get_field('navigation_overlay') : ''; ?>" style="background-color:<?php echo (get_sub_field('background_color')) ? get_sub_field('background_color') : get_field('brand_primary_color', 'option') ; ?>;">
<?php $hero_classes = 'hero anim fadeIn delay-500 '.get_sub_field('hero_size'); ?>
<?php if (get_sub_field('background_type') == 'image'): ?>
	<div class="<?php echo $hero_classes; ?>" <?php echo responsive_bg(get_sub_field('hero_image')); ?>>
<?php elseif (get_sub_field('background_type') == 'video'): ?>
	<div class="<?php echo $hero_classes; ?>">
<?php elseif (get_sub_field('background_type') == 'interactive'): ?>
	<?php partial( 'sections/content/hero', get_sub_field('hero_partial_name') ); ?>
	<div class="<?php echo $hero_classes; ?>">
<?php elseif (get_sub_field('background_type') == 'none'): ?>
	<div class="<?php echo $hero_classes; ?>">
<?php endif; ?>
		<?php if (get_sub_field('content') !== 'nothing'): ?>
			<div class="container">
				<div class="column">
					<div class="content kitchensink">
					<?php if ( get_sub_field('hero_content') ): ?>
						<?php the_sub_field('hero_content'); ?>
					<?php else: ?>
						<?php if ($post->post_type === 'team'): ?>
							<h1 style="text-align: center;" class="anim fadeInLeft delay-400"><?php the_title(); ?></h1>
							<p class="smallcaps anim fadeInLeft delay-600" style="text-align: center;"><?php get_field('position'); ?></p>
						<?php elseif ($post->post_type === 'blog'): ?>
							<p class="h6 anim fadeInUp delay-500"><?php echo wp_get_object_terms( $post->ID, 'blog_categories', array( 'fields' => 'names' ) )[0]; ?></p>
							<h1 class="anim fadeInLeft delay-700"><?php the_title(); ?></h1>
							<?php $link = ( get_the_author_meta('user_name') === "neighborhood" ) ? '/about/' . get_the_author_meta('user_name')  . '/posts' : '/about/aiga-nebraska/posts'; ?>
							<p class="byline anim fadeInLeft delay-800"><em><span><a href="<?php echo $link; ?>" class="<?php pjaxify(); ?>"><?php the_author(); ?></a></span></em> / <?php the_date(); ?></p>
						<?php else: ?>
							<p class="h6 anim fadeInUp delay-500"><?php echo wp_get_object_terms( $post->ID, 'blog_categories', array( 'fields' => 'names' ) )[0]; ?></p>
							<h1 class="anim fadeInLeft delay-700"><?php the_title(); ?></h1>
							<?php $link = ( get_the_author_meta('user_name') === "neighborhood" ) ? '/about/' . get_the_author_meta('user_name')  . '/posts' : '/about/aiga-nebraska/posts'; ?>
							<p class="byline anim fadeInLeft delay-800"><em>by <span><a href="<?php echo $link; ?>" class="<?php pjaxify(); ?>"><?php the_author(); ?></a></span></em> / <?php the_date(); ?></p>
						<?php endif; ?>
					<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	<?php if ( get_sub_field('hero_size') === "hero-large" ): ?>
	<img src="data:image/gif;base64,R0lGODlhGQAMAIAAAP///wAAACH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMwNjcgNzkuMTU3NzQ3LCAyMDE1LzAzLzMwLTIzOjQwOjQyICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxNSAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2RDVDOTFGOTZGOUQxMUU1ODY4RkQ0NEMzOEQxOUJFQiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2RDVDOTFGQTZGOUQxMUU1ODY4RkQ0NEMzOEQxOUJFQiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjZENUM5MUY3NkY5RDExRTU4NjhGRDQ0QzM4RDE5QkVCIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjZENUM5MUY4NkY5RDExRTU4NjhGRDQ0QzM4RDE5QkVCIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Af/+/fz7+vn49/b19PPy8fDv7u3s6+rp6Ofm5eTj4uHg397d3Nva2djX1tXU09LR0M/OzczLysnIx8bFxMPCwcC/vr28u7q5uLe2tbSzsrGwr66trKuqqainpqWko6KhoJ+enZybmpmYl5aVlJOSkZCPjo2Mi4qJiIeGhYSDgoGAf359fHt6eXh3dnV0c3JxcG9ubWxramloZ2ZlZGNiYWBfXl1cW1pZWFdWVVRTUlFQT05NTEtKSUhHRkVEQ0JBQD8+PTw7Ojk4NzY1NDMyMTAvLi0sKyopKCcmJSQjIiEgHx4dHBsaGRgXFhUUExIREA8ODQwLCgkIBwYFBAMCAQAAIfkEAAAAAAAsAAAAABkADAAAAg+Ej6nL7Q+jnLTai7PevAAAOw==" width="25" height="12" class="sizer" />
	<?php elseif ( get_sub_field('hero_size') === "hero-medium" ): ?>
	<img src="data:image/gif;base64,R0lGODlhAgABAIAAAP///wAAACH5BAAAAAAALAAAAAACAAEAAAICBAoAOw==" width="2" height="1" class="sizer" />
	<?php elseif ( get_sub_field('hero_size') === "hero-small" ): ?>
	<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" width="1440" height="720" class="sizer" />
	<?php endif; ?>
		</div>
</section>
