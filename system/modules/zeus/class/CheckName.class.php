<?php
class CheckName {
	public $minLenght = 2;
	public $maxLenght = 15;

	public function getMinLenght()	{ return $this->minLenght; }
	public function getMaxLenght()	{ return $this->maxLenght; }

	public function setMinLenght($i){
		if (is_int($i) AND  n)
		$this->minLenght = $i;
	}
	public function setMaxLenght($i){ $this->maxLenght = $i; }

	public function checkLength($str) {
		$lenght = strlen($str);
		if ($lenght >= $this->minLenght AND $lenght <= $this->maxLenght) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function checkChar($str) {
		if (preg_match('^[\p{L}\p{N}]*\p{L}[\p{L}\p{N}]*$^', $str)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function checkBeauty($str) {
		$newStr = ucfirst($str);

		$revStr = strrev($newStr);
		$troncateStr = $newStr;
		$number = '';
		for ($i = 0; $i < strlen($revStr); $i++) { 
			if (is_numeric($revStr[$i])) {
				$number .= $revStr[$i];
				$troncateStr = substr($troncateStr, 0, -1);
			} else {
				$number = strrev($number);
				$number = intval($number);
				break;
			}
		}

		if ($number !== 0) {
			$brn = array('M', 'CM', 'D', 'CD', 'C', 'XC', 'L', 'XL', 'X', 'IX', 'V', 'IV', 'I');
			$ban = array(1000, 900, 500, 400, 100, 90, 50, 40, 10, 9, 5, 4, 1);

			if ($number > 1200) { $number = rand(50, 1200); }
			if ($number <= 0) { $number = 1; }
			$number = intval($number);
			$romanNumber = '';
			$i = 0;

			if ($number == 1) {
				$romanNumber = 'Ier';
				$number = 0;
			}

			while ($number > 0) {
				if ($number >= $ban[$i]) {
					$number = $number - $ban[$i];
					$romanNumber .= $brn[$i];
				} else {
					$i++;
				}
			}

			$newStr = trim($troncateStr) . ' ' . $romanNumber;

			if (strlen($newStr) > $this->maxLenght) {
				$newStr = substr($newStr, 0, $this->maxLenght);
			}
		}

		if ($newStr === $str) {
			return TRUE;
		} else {
			return $newStr;
		}
	}

	public static function getPackOfNames($size = 5, $used = FALSE, $tag = array()) {
		
	}

	public static function randomize($color = FALSE) {
		$name = array('Ametah', 'Anla', 'Aumshi', 'Bastier', 'Enigma', 'Eirukis', 'Erah', 'Ehdis', 'Fransa', 'Greider', 'Grerid', 'Haema', 'Hemhild', 'Renga', 'Hidar', 'Horski', 'Hreirek', 'Hroa', 'Hordis', 'Hydring', 'Imsin', 'Asmin', 'Ansami', 'Kar', 'Kili', 'Kolver', 'Kolfinna', 'Lisa', 'Marta', 'Meto', 'Leto', 'Ragni', 'Ranela', 'Runa', 'Siri', 'Mastro', 'Svenh', 'Thalestris', 'Thannd', 'Arsine', 'Val', 'Vori', 'Yi', 'Agata', 'Agneta', 'Nolgi', 'Edla', 'Else', 'Eyja', 'Jensine', 'Kirsten', 'Maeva', 'Malena', 'Magarte', 'Olava', 'Petrine', 'Rigmor', 'Signy', 'Sigrid', 'Skjorta');
		return $name[rand(0, (count($name) - 1))];
	}
}
?>