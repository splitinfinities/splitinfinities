<?php $social_connect_transient = get_transient('social_connect'); ?>
<?php if ($social_connect_transient && ! is_user_logged_in( ) ): ?>
	<?php echo $social_connect_transient; ?>
<?php else: ?>
	<?php delete_transient( 'social_connect' ); ?>
	<?php ob_start(); ?>
	<?php if ( have_rows('sdo_social_connect', 'options') ): ?>
		<div class="entry-social-connect">
			<?php while ( have_rows('sdo_social_connect', 'options') ) : the_row(); ?>
			<a target="_blank" href="<?php echo ucfirst( get_sub_field( 'platform_url' ) ); ?>" title="<?php echo get_bloginfo( 'name' ); ?> on <?php echo ucfirst( get_sub_field( 'platform' ) ); ?>" class="<?php echo get_sub_field( 'platform' ); ?>"><?php partial('images/social', get_sub_field( 'platform' ) ); ?></a>
			<?php endwhile; ?>
		</div>
	<?php endif; ?>
	<?php $social_connect_content = ob_get_clean(); ?>
	<?php echo $social_connect_content; ?>
	<?php set_transient( 'social_connect', $social_connect_content, 12 * HOUR_IN_SECONDS); ?>
<?php endif; ?>
