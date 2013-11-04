<?php

class Account {

	private $id;
	private $primaryCurrency;
	private $secondaryCurrency;
	private $secondaryBalance;
	private $primaryBalance;

	function getSecondaryBalance() {
		return $this->secondaryBalance;
	}

	function getPrimaryBalance() {
		return $this->primaryBalance;
	}

	function __construct($id) {
		$this->id = $id;
	}

	function getId() {
		return $this->id;
	}

	function setPrimaryCurrency(Currency $currency) {
		$this->primaryCurrency = $currency;
	}

	function setSecondaryCurrency(Currency $currency) {
		$this->secondaryCurrency = $currency;
	}

	function getCurrencies() {
		return array('primary' => $this->primaryCurrency, 'secondary' => $this->secondaryCurrency);
	}

	function deposit(Money $money) {
		if ($this->primaryCurrency == $money->getCurrency()){
			$this->primaryBalance = $this->primaryBalance ? : new Money(0, $this->primaryCurrency);
			$this->primaryBalance = $this->primaryBalance->add($money);
		}else {
			$this->secondaryBalance = $this->secondaryBalance ? : new Money(0, $this->secondaryCurrency);
			$this->secondaryBalance = $this->secondaryBalance->add($money);
		}
	}

	function withdraw(Money $money) {
		$this->validateCurrencyFor($money);
		if ($this->primaryCurrency == $money->getCurrency()) {
			if( $this->primaryBalance >= $money ) {
				$this->primaryBalance = $this->primaryBalance->subtract($money);
			}else{
				$ourMoney = $this->primaryBalance->add($this->secondaryToPrimary());
				$remainingMoney = $ourMoney->subtract($money);
				$this->primaryBalance = new Money(0, $this->primaryCurrency);
				$this->secondaryBalance = (new Exchange())->convert($remainingMoney, $this->secondaryCurrency);
			}

		} else {
			$this->secondaryBalance = $this->secondaryBalance->subtract($money);
		}
	}

	private function validateCurrencyFor(Money $money) {
		if (!in_array($money->getCurrency(), $this->getCurrencies()))
			throw new Exception(
			sprintf(
					'This account has no currency %s', $money->getCurrency()->getStringRepresentation()
			)
			);
	}

	private function secondaryToPrimary() {
		return (new Exchange())->convert($this->secondaryBalance, $this->primaryCurrency);
	}

}

?>
