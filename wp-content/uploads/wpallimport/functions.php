<?php
function convert_booking_time($value) {
	$value = date('YmdHis', strtotime($value));
	return $value;
}

function convert_resource_id($value) {
	$array = array(
		'1' => 562,
		'2' => 561,
		'3' => 560,
		'4' => 556,
		'5' => 555,
		'6' => 564,
		'7' => 554,
		'8' => 553,
		'9' => 557,
		'10' => 559,
		'11' => 552,
		'13' => 558,
		'14' => 567
	);
	
	return $array[$value];
}
?>