<?php
require_once('../../library/common.class.php');
include_once(APPPATH .'/backend/trades.class.php');

class Test extends \PHPUnit_Framework_TestCase {
	protected $trades;

    protected function setUp()
    {
        $this->trades = new Trades();
    }
	
	function test_selectSilverLoginVolumeByLastWeek() { 
		$res = $this->trades->selectSilverLoginVolumeByLastWeek();
		$this->assertEmpty(! $res); 
	}
	function test2() { 
		$this->assertTrue( 1 + 1  == 2 ); 
	}
}

?>
