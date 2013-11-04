<?php

class Exchange {

	function convert(Money $money, Currency $toCurrency) {
		if ($toCurrency == Currency::EUR() && $money->getCurrency() == Currency::USD())
			return new Money($money->multiplyBy(0.67)->getAmount(), $toCurrency);
		if ($toCurrency == Currency::USD() && $money->getCurrency() == Currency::EUR())
			return new Money($money->multiplyBy(1.5)->getAmount(), $toCurrency);
		return $money;
	}

}

?>
