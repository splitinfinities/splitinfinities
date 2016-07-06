<?php global $post; ?>
<?php if ( have_rows('sdo_social_share', 'options') ): ?>
	<?php $background_image = wp_get_attachment_image_src( get_field( 'background_image', $post->ID ), 'full' ); ?>
	<p class="entry-social-intro"><em>Share this:</em></p>
	<div class="entry-social work">
		<?php while ( have_rows('sdo_social_share', 'options') ) : the_row(); ?>
			<?php if (get_sub_field( 'platform' ) === "twitter"): ?>
				<a target="_blank" href="https://twitter.com/intent/tweet?text=<?php echo get_the_title(); ?>&url=<?php echo get_the_permalink(); ?>" class="<?php echo get_sub_field( 'platform' ); ?>" title="Share this on <?php echo get_sub_field( 'platform' ) ?>"><?php partial('images/social', get_sub_field( 'platform' ) ); ?></a>
			<?php elseif (get_sub_field( 'platform' ) === "facebook"): ?>
				<a target="_blank" href="http://www.facebook.com/sharer/sharer.php?u=<?php echo get_the_permalink(); ?>" class="<?php echo get_sub_field( 'platform' ); ?>" title="Share this on <?php echo get_sub_field( 'platform' ) ?>"><?php partial('images/social', get_sub_field( 'platform' ) ); ?></a>
			<?php elseif (get_sub_field( 'platform' ) === "googleplus"): ?>
				<a target="_blank" href="https://plus.google.com/share?url=<?php echo get_the_permalink(); ?>" class="<?php echo get_sub_field( 'platform' ); ?>" title="Share this on <?php echo get_sub_field( 'platform' ) ?>"><?php partial('images/social', get_sub_field( 'platform' ) ); ?></a>
			<?php elseif (get_sub_field( 'platform' ) === "instagram"): ?>
				<!-- You can't share on Instagram -->
			<?php elseif (get_sub_field( 'platform' ) === "pinterest"): ?>
				<a target="_blank" href="https://www.pinterest.com/pin/create/button/?url=<?php echo get_the_permalink(); ?>&media=<?php echo $background_image[0]; ?>&description=<?php echo get_the_title(); ?>" class="<?php echo get_sub_field( 'platform' ); ?>" title="Pin this on <?php echo get_sub_field( 'platform' ) ?>"><?php partial('images/social', get_sub_field( 'platform' ) ); ?></a>
			<?php elseif (get_sub_field( 'platform' ) === "linkedin"): ?>
				<a target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo get_the_permalink(); ?>&title=<?php echo get_the_title(); ?>&summary=<?php echo strip_tags(get_the_excerpt()); ?>&source=<?php echo get_the_title(); ?>" class="<?php echo get_sub_field( 'platform' ); ?>" title="Share this on <?php echo get_sub_field( 'platform' ) ?>"><?php partial('images/social', get_sub_field( 'platform' ) ); ?></a>
			<?php elseif (get_sub_field( 'platform' ) === "houzz"): ?>
				<a target="_blank" href="http://www.houzz.com/imageClipperUpload?imageUrl=<?php echo $background_image[0]; ?>&title=<?php echo get_the_title(); ?>&link=<?php echo get_the_permalink(); ?>" class="<?php echo get_sub_field( 'platform' ); ?>" title="Save this on <?php echo get_sub_field( 'platform' ) ?>"><?php partial('images/social', get_sub_field( 'platform' ) ); ?></a>
			<?php elseif (get_sub_field( 'platform' ) === "native_fav"): ?>
				<a target="_blank" href="#" class="<?php echo get_sub_field( 'platform' ); ?>" title="Favorite this"><?php partial('images/social', 'native_fav'); ?></a>
			<?php endif; ?>
		<?php endwhile; ?>
		<a href="mailto:?subject=<?php echo get_the_title(); ?>&body=Check this out: http://neighborhood.co/l/<?php echo $post->post_name; ?>" class="email"><?php partial('images/social', 'email' ); ?></a>
		<a class="permalink" onclick="λ.select_text('this_work_link');"><?php partial('images/social', 'link' ); ?><span id="this_work_link">http://neighborhood.co/l/<?php echo $post->post_name; ?></span></a>
	</div>
<?php endif; ?>
