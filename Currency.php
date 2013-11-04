<?php

class Currency {

	private $centFactor;
	private $stringRepresentation;

	private function __construct($centFactor, $stringRepresentation) {
		$this->centFactor = $centFactor;
		$this->stringRepresentation = $stringRepresentation;
	}

	public function getCentFactor() {
		return $this->centFactor;
	}

	function getStringRepresentation() {
		return $this->stringRepresentation;
	}

	static function USD() {
		return new self(100, 'USD');
	}

	static function EUR() {
		return new self(100, 'EUR');
	}

}

?>
