<?php
$the_request_was_successful = true;

$testimonials = array();

$testimonial_content = array();

if ( have_rows('testimonials') ):
	while( have_rows('testimonials') ) : the_row();
			/* if this is a tweet */
		if (get_sub_field('tweet_url')):

			$twitteruser = "neighborhood";
			$consumerkey = "Usr0I9I8qNdkG13vnPvmLk0eS";
			$consumersecret = "626zzIBlHjiNcQgxiGwcaUlatx6FNj34Nu95BFKXM4gZmJ8spq";
			$accesstoken = "352662428-y9B2tAEEcDDgirizjZzBjviTu0kbCIWSU6JOmHnX";
			$accesstokensecret = "Ra2n5lECfGLAwE9MghKEO273wcQ9poBAm7kTLS6WGTdqr";

			function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
				$connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
				return $connection;
			}

			$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);

			$tweet_id = explode('/', get_sub_field('tweet_url'));

			$tweet_id = $tweet_id[5];

			$tweet = $connection->get("https://api.twitter.com/1.1/statuses/show.json?id=" . $tweet_id . "&include_entities=true");

			$regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?).*$)@";

			$testimonial_content = array(
				'color' => $tweet->user->profile_background_color,
				'text' => '<p class="h1">'.word_wrapper( preg_replace( $regex, ' ', $tweet->text ) ).'</p>',
				'background_image' => $tweet->user->profile_background_image_url,
				'profile_picture' => str_replace('_normal', '_bigger', $tweet->user->profile_image_url),
				'name' => $tweet->user->name,
				'title' => 'Twitter',
			);

			/* if this is a facebook review */
		elseif (get_sub_field('facebook_url')):

			try {
				$facebook_review = get_facebook_testimonial(get_sub_field('facebook_url'));

				$facebook_reviewer = get_facebook_user($facebook_review['reviewer']['id']);

				$testimonial_content = array(
					'color' => '3b5998',
					'text' => '<p class="h1">'.word_wrapper($facebook_review['review_text']).'</p>',
					'background_image' => $facebook_reviewer['cover']['source'],
					'profile_picture' => 'http://graph.facebook.com/'.$facebook_review['reviewer']['id'].'/picture?type=square&width=200',
					'name' => $facebook_review['reviewer']['name'],
					'title' => 'Facebook Review',
				);

			} catch(ErrorException $e) {
				$our_request_error = $e;
				$the_request_was_successful = false;
			}

			/* If this is a testimonial */
		elseif (get_sub_field('testimonial')):
			$testimonial_post_id = get_sub_field('testimonial');

			$testimonial_content = array(
				'color' => get_field('background_color', $testimonial_post_id),
				'text' => get_post_field('post_content', $testimonial_post_id),
				'background_image' => get_field('background_image', $testimonial_post_id),
				'profile_picture' => wp_get_attachment_image_src( get_field('profile_picture', $testimonial_post_id), 'full')[0],
				'name' => get_field('persons_name', $testimonial_post_id),
				'title' => get_field('persons_title', $testimonial_post_id),
			);

		endif;

		$testimonials[] = $testimonial_content;
	endwhile;
endif;

?>
<?php if ($the_request_was_successful): ?>
	<?php $is_dark = is_reverse_contrast(get_sub_field('background_color')); ?>
	<section <?php if(get_sub_field('section_name')):?>id="<?php echo $post->post_name.'-'.sanitize_title(get_sub_field('section_name')); ?>"<?php endif; ?> <?php if (get_sub_field('background_color')): ?>style="background-color:<?php echo get_sub_field('background_color'); ?>;" <?php endif; ?>class="container bleed testimonial<?php if(get_sub_field('background_color')):?> <?php echo $is_dark; ?><?php endif;?>">
		<div class="testimonial-slider">
			<?php foreach ( $testimonials as $testimonial_content ): ?>
				<div class="slide">
					<div class="container">
						<div class="column">
							<div class="content kitchensink text-center">
								<?php echo $testimonial_content['text']; ?>
								<img src="<?php echo $testimonial_content['profile_picture']; ?>" />
								<p class="h5 name"><?php echo $testimonial_content['name']; ?></p>
								<p class="title"><?php echo $testimonial_content['title']; ?></p>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
<?php else: ?>
	<?php if (is_user_logged_in()): ?>
		<?php echo $our_request_error->getMessage(); ?>
	<?php endif; ?>
<?php endif; ?>
