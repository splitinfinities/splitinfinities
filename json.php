<?php
	$williamriley = array(
		"first_name" => "William",
		"middle_name" => "Michael",
		"last_name" => "Riley",
		"gender" => "male",
		"religion" => "faith",
		"born" => "Modesto, California",
		"timezone" => "-5",
		"locale" => "en_US",
		"at_age" => [
			23 => array(
				"link" => "https://www.facebook.com/billoriley",
				"location" => array(
					"id" => "103107309730219",
					"name" => "Council Bluffs, Iowa"
				),
				"inspirations" => array(
					"d.s.", // david shreffler
					"m.p.v.b.", // martin patrick victor boehme
					"t.j.f.", // thomas johnathan frank
					"q.h.r.", // quinton hugh rau
					"a.m.", // angela miller
					"a.m.",
					"j.s.",
				)
			),
		]
	);

	echo json_encode($williamriley);
?>
