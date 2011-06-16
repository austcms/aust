<?php
require_once 'PHPUnit/Framework.php';

#####################################
require_once 'tests/config/auto_include.php';
require_once 'core/class/SQLObject.class.php';
#####################################

class ResourcesTest extends PHPUnit_Framework_TestCase
{

    public function testNumberToCurrency(){
		// buggy
        $this->assertEquals('R$ 0,00', Resources::numberToCurrency("0", "R$") );

        $this->assertEquals('R$ 10,20', Resources::numberToCurrency("10.20", "R$") );
        $this->assertEquals('R$ 10,20', Resources::numberToCurrency("10.2", "R$") );
        $this->assertEquals('R$ 10,00', Resources::numberToCurrency("10.0", "R$") );
        $this->assertEquals('R$ 10,00', Resources::numberToCurrency("10", "R$") );
    }

    public function testCurrencyToFloat(){

		// buggy
        $this->assertEquals('0', 		Resources::currencyToFloat("") );
        $this->assertEquals('0', 		Resources::currencyToFloat("R$ ") );

		// general tests using R$
        $this->assertEquals('10.20', 		Resources::currencyToFloat("R$ 10,20") );
        $this->assertEquals('10.20', 		Resources::currencyToFloat("R$10,20") );
        $this->assertEquals('10.20', 		Resources::currencyToFloat("R$ 10.20") );
		$this->assertEquals('100000200.00', 	Resources::currencyToFloat("R$ 100.000,200") );
        $this->assertEquals('100000.20', 	Resources::currencyToFloat("R$ 100,000.2") );
        $this->assertEquals('100000.20', 	Resources::currencyToFloat("R$ 100.000,2") );
        $this->assertEquals('100000.20', 	Resources::currencyToFloat("R$ 100.000.2") );
        $this->assertEquals('100000.20', 	Resources::currencyToFloat("R$ 100,000.20") );
        $this->assertEquals('100000.20', 	Resources::currencyToFloat("R$ 100.000,20") );
        $this->assertEquals('100000.20', 	Resources::currencyToFloat("R$ 100.000.20") );
        $this->assertEquals('100000.20', 	Resources::currencyToFloat("R$ 100.000,20") );
        $this->assertEquals('100000.20', 	Resources::currencyToFloat("R$100.000.20") );
        $this->assertEquals('100000.20', 	Resources::currencyToFloat("100.000,20") );

		// other currencies
        $this->assertEquals('10.20', 		Resources::currencyToFloat("US$ 10.2") );
        $this->assertEquals('10',	 		Resources::currencyToFloat("US$ 10") );
        $this->assertEquals('0.20', 		Resources::currencyToFloat("US$ 0.20") );

    }

}
?>