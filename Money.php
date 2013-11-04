<?php

class Money {

	private $amount;
	private $currency;

	function __construct($amount, Currency $currency) {
		$this->amount = $amount;
		$this->currency = $currency;
	}

	public function getAmount() {
		return $this->amount;
	}

	public function getCurrency() {
		return $this->currency;
	}

	function add(Money $other) {
		$this->ensureSameCurrencyWith($other);
		return new Money($this->amount + $other->getAmount(), $this->currency);
	}

	function subtract(Money $other) {
		$this->ensureSameCurrencyWith($other);
		if ($other > $this)
			throw new Exception("Subtracted money is more than what we have");
		return new Money($this->amount - $other->getAmount(), $this->currency);
	}

	function multiplyBy($multiplier, $roundMethod = PHP_ROUND_HALF_UP) {
		$product = round($this->amount * $multiplier, 0, $roundMethod);
		return new Money($product, $this->currency);
	}

	function allocate(Account $a1, Account $a2, $a1Percent, $a2Percent) {
		$exactA1Balance = $this->amount * $a1Percent / 100;
		$exactA2Balance = $this->amount * $a2Percent / 100;

		while ($this->amount > 0) {
			$this->allocateTo($a1, $exactA1Balance);
			if ($this->amount <= 0)
				break;
			$this->allocateTo($a2, $exactA2Balance);
		}
	}

	private function allocateTo($account, $exactBalance) {
		if ($account->getPrimaryBalance()->getAmount() < $exactBalance) {
			$account->deposit(new Money(1, $this->currency));
			$this->amount--;
		}
	}

	private function ensureSameCurrencyWith(Money $other) {
		if ($this->currency != $other->getCurrency())
			throw new Exception("Both Moneys must be of same currency");
	}

}

?>
