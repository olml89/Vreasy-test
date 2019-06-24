<?php declare(strict_types = 1);


use PHPUnit\Framework\TestCase;


final class SampleTest extends TestCase {


	public function testTrueAssertsToTrue() : void {
		$this->assertTrue(true);
	}


	/** @test **/
	public function that_assert_contains() : void {
		$this->assertContains(4, [1, 2, 3, 4, 5]);
	}


}