<?php

require_once '../Currency.php';
require_once '../Money.php';

class MoneyTest extends PHPUnit_Framework_TestCase {

	function testWeCanCreateAMoneyObject() {
		$money = new Money(100, Currency::USD());
	}

	function testItCanTellTwoMoneyObjectAreEqual() {
		$m1 = new Money(100, Currency::USD());
		$m2 = new Money(100, Currency::USD());

		$this->assertEquals($m1, $m2);
		$this->assertTrue($m1 == $m2);
	}

	function testOneMoneyIsBiggerThanTheOther() {
		$m1 = new Money(200, Currency::USD());
		$m2 = new Money(100, Currency::USD());

		$this->assertGreaterThan($m2, $m1);
		$this->assertTrue($m1 > $m2);
	}

	function testOneMoneyIsLessThanTheOther() {
		$m1 = new Money(100, Currency::USD());
		$m2 = new Money(200, Currency::USD());

		$this->assertLessThan($m2, $m1);
		$this->assertTrue($m1 < $m2);
	}

	function testTwoMoneyObjectsCanBeAdded() {
		$m1 = new Money(100, Currency::USD());
		$m2 = new Money(200, Currency::USD());
		$sum = new Money(300, Currency::USD());

		$this->assertEquals($sum, $m1->add($m2));
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Both Moneys must be of same currency
	 */
	function testItThrowsExceptionIfWeTryToAddTwoMoneysWithDifferentCurrency() {
		$m1 = new Money(100, Currency::USD());
		$m2 = new Money(100, Currency::EUR());

		$m1->add($m2);
	}

	function testTwoMoneyObjectsCanBeSubtracted() {
		$m1 = new Money(200, Currency::USD());
		$m2 = new Money(100, Currency::USD());
		$result = new Money(100, Currency::USD());

		$this->assertEquals($result, $m1->subtract($m2));
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Both Moneys must be of same currency
	 */
	function testItThrowsExceptionIfWeTryToSubtractTwoMoneysWithDifferentCurrency() {
		$m1 = new Money(100, Currency::USD());
		$m2 = new Money(100, Currency::EUR());

		$m1->subtract($m2);
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Subtracted money is more than what we have
	 */
	function testItThrowsExceptionIfWeTryToSubtractMoreMoneyThanWeHave() {
		$m1 = new Money(100, Currency::USD());
		$m2 = new Money(200, Currency::USD());

		$m1->subtract($m2);
	}

	function testAMoneyObjectCanBeMultiplied() {
		$m1 = new Money(100, Currency::USD());
		$result = new Money(300, Currency::USD());

		$this->assertEquals($result, $m1->multiplyBy(3));
	}

	function testProductIsRoundedAfterMultiplication() {
		$m1 = new Money(10, Currency::USD());
		$this->assertEquals(new Money(3, Currency::USD()), $m1->multiplyBy(0.33));
		$this->assertEquals(new Money(3, Currency::USD()), $m1->multiplyBy(0.25));
	}

	function testRoundingCanUseCustomMethod() {
		$m1 = new Money(10, Currency::USD());
		$this->assertEquals(new Money(3, Currency::USD()), $m1->multiplyBy(0.33, PHP_ROUND_HALF_DOWN));
		$this->assertEquals(new Money(2, Currency::USD()), $m1->multiplyBy(0.25, PHP_ROUND_HALF_DOWN));
	}

	function testItCanAllocateMoneyBetween2Accounts() {
		$a1 = $this->anAccount();
		$a2 = $this->anAccount();
		$money = new Money(5, Currency::USD());
		$money->allocate($a1, $a2, 30, 70);

		$this->assertEquals(new Money(2, Currency::USD()), $a1->getPrimaryBalance());
		$this->assertEquals(new Money(3, Currency::USD()), $a2->getPrimaryBalance());
	}

	private function anAccount() {
		$account = new Account(1);
		$account->setPrimaryCurrency(Currency::USD());
		$account->deposit(new Money(0, Currency::USD()));
		return $account;
	}


}

?>
