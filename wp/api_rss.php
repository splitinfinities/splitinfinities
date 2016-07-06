<?php

function get_medium_feed($feed_url) {
	try {
		$content = file_get_contents($feed_url);

		$xml = new SimpleXmlElement($content);

		foreach($xml->channel->item as $entry) {

			$description = (string) $entry->description;

			preg_match_all('/<img[^>]+>/i', $content, $images);

			foreach( $images[0] as $img_tag ) {

				preg_match_all('/(alt|title|src)=("[^"]*")/i', $img_tag, $cover_image_attrs);
				$cover_image = $cover_image_attrs[2][0];

				break;
			}

			$object_to_return = array(
				'title' => (string) $entry->title,
				'permalink' => (string) $entry->link,
				'image_lg' => array(
					'url' => '',
					'width' => '1600',
					'height' => '800',
				),
				'image_md' => array(
					'url' => '',
					'width' => '1024',
					'height' => '512',
				),
				'image_sm' => array(
					'url' => '',
					'width' => '640',
					'height' => '320',
				),
			);

			$cover_src = explode('/', $cover_image);

			foreach($cover_src as $key => $part) {
				if ($part === 'c') {
					$sm = $cover_src;
					$sm[$key + 1] = 640;
					$sm[$key + 2] = 320;
					$object_to_return['image_sm']['url'] = str_replace('"', '', implode('/', $sm));

					$md = $cover_src;
					$md[$key + 1] = 1024;
					$md[$key + 2] = 512;
					$object_to_return['image_md']['url'] = str_replace('"', '', implode('/', $md));

					$lg = $cover_src;
					$lg[$key + 1] = 1600;
					$lg[$key + 2] = 800;
					$object_to_return['image_lg']['url'] = str_replace('"', '', implode('/', $lg));
				}
			}

			return $object_to_return;
			break;
		}

	}
	catch (Exception $e) {
		// Do something...
	}
}

function get_codepen_feed($feed_url) {
	try {
		$content = file_get_contents($feed_url);

		$xml = new SimpleXmlElement($content);

		foreach($xml->channel->item as $entry) {
			$description = (string) $entry->description;

			$details = explode('/', $entry->link);

			$object_to_return = (object) array(
				'title' => (string) $entry->title,
				'permalink' => (string) $entry->link,
				'user' => $details[3],
				'pen_id' => $details[5],
			);

			return $object_to_return;
			break;
		}

	}
	catch (Exception $e) {
		// Do something...
	}
}
