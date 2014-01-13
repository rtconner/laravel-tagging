<?php namespace Rtconner\Tagging\Tests;

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	public function createApplication() {
		$unitTesting = true;

		$testEnvironment = 'testing';

		return require __DIR__.'/../../bootstrap/start.php';
	}

	private function prepareForTests() {
		Artisan::call('migrate');
	}
	
	public function setUp() {
		parent::setUp();
		$this->prepareForTests();
	}
	
	private function createStub() {
		return new \Rtconner\Tagging\Tests\Stub;
	}
	
}
