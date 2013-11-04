<?php

require_once '../Account.php';
require_once '../Exchange.php';

class AccountTest extends PHPUnit_Framework_TestCase {

	private $account;

	protected function setUp() {
		$this->account = new Account(123);
	}

	function testItCanCrateANewAccountWithId() {
		$this->assertEquals(123, $this->account->getId());
	}

	function testItCanHavePrimaryAndSecondaryCurrencies() {
		$this->account->setPrimaryCurrency(Currency::EUR());
		$this->account->setSecondaryCurrency(Currency::USD());

		$this->assertEquals(array('primary' => Currency::EUR(), 'secondary' => Currency::USD()), $this->account->getCurrencies());
	}

	function testAccountCanDepositMoney() {
		$this->account->setPrimaryCurrency(Currency::EUR());
		$money = new Money(100, Currency::EUR()); //That's 1 EURO
		$this->account->deposit($money);

		$this->assertEquals($money, $this->account->getPrimaryBalance());
	}

	function testSubsequentDepositsAddUpTheMoney() {
		$this->account->setPrimaryCurrency(Currency::EUR());
		$money = new Money(100, Currency::EUR()); //That's 1 EURO
		$this->account->deposit($money); //One euro in the account
		$this->account->deposit($money); //Twi euros in the account

		$this->assertEquals($money->multiplyBy(2), $this->account->getPrimaryBalance());
	}

	function testAccountCanWithdrawMoneyOfSameCurrency() {
		$this->account->setPrimaryCurrency(Currency::EUR());
		$money = new Money(100, Currency::EUR()); //That's 1 EURO
		$this->account->deposit($money);
		$this->account->withdraw(new Money(70, Currency::EUR()));

		$this->assertEquals(new Money(30, Currency::EUR()), $this->account->getPrimaryBalance());
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage This account has no currency USD
	 */
	function testThrowsExceptionForInexistentCurrencyOnWithdrawal() {
		$this->account->setPrimaryCurrency(Currency::EUR());
		$money = new Money(100, Currency::EUR()); //That's 1 EURO
		$this->account->deposit($money);
		$this->account->withdraw(new Money(70, Currency::USD()));
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Subtracted money is more than what we have
	 */
	function testItThrowsExceptionIfWeTryToSubtractMoreMoneyThanWeHave() {
		$this->account->setPrimaryCurrency(Currency::EUR());
		$money = new Money(100, Currency::EUR()); //That's 1 EURO
		$this->account->deposit($money);

		$this->account->setSecondaryCurrency(Currency::USD());
		$money = new Money(0, Currency::USD());
		$this->account->deposit($money);


		$this->account->withdraw(new Money(150, Currency::EUR()));
	}

	function testItConvertsMoneyFromTheOtherCurrencyWhenWeDoNotHaveEnoughInTheCurrentOne() {
		$this->account->setPrimaryCurrency(Currency::USD());
		$money = new Money(100, Currency::USD()); //That's 1 USD
		$this->account->deposit($money);

		$this->account->setSecondaryCurrency(Currency::EUR());
		$money = new Money(100, Currency::EUR()); //That's 1 EURO = 1.5 USD
		$this->account->deposit($money);

		$this->account->withdraw(new Money(200, Currency::USD())); //That's 2 USD

		$this->assertEquals(new Money(0, Currency::USD()), $this->account->getPrimaryBalance());
		$this->assertEquals(new Money(34, Currency::EUR()), $this->account->getSecondaryBalance());
	}

}

?>
