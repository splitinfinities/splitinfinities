<?php while (have_posts()) : the_post(); ?>
	<section class="container single-content">
		<div class="column twelve" itemscope="" itemtype="http://schema.org/Blog">
			<div class="content container bleed resources-list">
				<?php if ( have_rows('group') ): ?>
					<?php while ( have_rows('group') ): the_row(); ?>
						<div class="column four">
							<p class="h2"><strong><?php the_sub_field('name'); ?></strong></p>
						</div>
						<div class="column eight container bleed">
							<?php if ( have_rows('list') ): ?>
								<?php while ( have_rows('list') ): the_row(); ?>
									<div class="column four">
										<div class="list-item">
											<p class="h2"><strong><?php the_sub_field('name'); ?></strong></p>
											<?php if ( have_rows('items') ): ?>
												<?php while ( have_rows('items') ): the_row(); ?>
													<?php if (get_sub_field('link')): ?>
														<p><a href="<?php the_sub_field('link'); ?>" title="<?php the_sub_field('name'); ?>" target="_blank"><?php the_sub_field('name'); ?></a></p>
													<?php else: ?>
														<p><?php the_sub_field('name'); ?></p>
													<?php endif; ?>
												<?php endwhile; ?>
											<?php endif; ?>
										</div>
									</div>
								<?php endwhile; ?>
							<?php endif; ?>
						</div>
					<?php endwhile; ?>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endwhile; ?>
