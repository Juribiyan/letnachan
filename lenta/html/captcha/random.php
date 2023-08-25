<?php
	mb_internal_encoding("UTF-8");
	function generate_code() 
	{   
	$percentage = 15; //С какой частотой будут появляться слова из словаря
	$prcroll = mt_rand(1, 100);
	if($prcroll < $percentage) { //словарная капча
		$my_array = array(
'мята',
'мяту',
'мяте',
'мяты',
'слоу',
'анон',
'сажа',
'бамп',
'лурк',
'пони',
'коты',
'форс',
'лаги',
'трап',
'бред',
'йоба',
'саси',
'омск',
'анус',
'десу',
'вайп',
'фейл',
'алсо',
'лол',
'фсб',
'кгб',
'фап',
'суп',
'пруф',
'еот'
	);
$rand_array = array_rand($my_array, 2);		
		return $my_array[$rand_array[0]];
		}
	else {
		  $chars = 'абвгдеёжзийклмнопрстуфхцчшщъыьэюя';
		  $length = 4;
		  $numChars = mb_strlen($chars); 
		  $str = '';
		  for ($i = 0; $i < $length; $i++) {
			$str .= mb_substr($chars, rand(1, $numChars) - 1, 1);
		  } 
			$array_mix = preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);
		return implode("", $array_mix);
		}
	srand ((float)microtime()*1000000);
	}
