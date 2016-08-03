<?php

use Conner\Tagging\Providers\TaggingServiceProvider;

abstract class TestCase extends Orchestra\Testbench\TestCase
{
	protected function getPackageProviders($app)
	{
	    return [TaggingServiceProvider::class];
	}

	public function setUp()
	{
		parent::setUp();
	}

	/**
	 * Assert that two arrays are equal. This helper method will sort the two arrays before comparing them if
	 * necessary. This only works for one-dimensional arrays, if you need multi-dimension support, you will
	 * have to iterate through the dimensions yourself.
	 * @param array $expected the expected array
	 * @param array $actual the actual array
	 * @param bool $regard_order whether or not array elements may appear in any order, default is false
	 * @param bool $check_keys whether or not to check the keys in an associative array
	 */
	protected function assertArraysEqual(array $expected, array $actual, $regard_order = false, $check_keys = false) {
	    // check length first
	    $this->assertEquals(count($expected), count($actual), 'Failed to assert that two arrays have the same length.');

	    // sort arrays if order is irrelevant
	    if (!$regard_order) {
	        if ($check_keys) {
	            $this->assertTrue(ksort($expected), 'Failed to sort array.');
	            $this->assertTrue(ksort($actual), 'Failed to sort array.');
	        } else {
	            $this->assertTrue(sort($expected), 'Failed to sort array.');
	            $this->assertTrue(sort($actual), 'Failed to sort array.');
	        }
	    }

	    $this->assertEquals($expected, $actual);
	}
}