<?php

namespace NetherTestSuite\DeepstoreTest;
use PHPUnit;

use Nether\Object\Deepstore;
use Throwable;

class DeepstoreTest
extends PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestDeepReading() {

		$Store = new Deepstore;
		$Store->SetData([
			'Key' => 'Value0',
			'Level1' => [
				'Key' => 'Value1',
				'Level2' => [
					'Key' => 'Value2',
					'Level3' => [
						'Key' => 'Value3'
					]
				]
			]
		]);

		$this->AssertTrue($Store->Key === 'Value0');
		$this->AssertTrue($Store->Level1->Key === 'Value1');
		$this->AssertTrue($Store->Level1->Level2->Key === 'Value2');
		$this->AssertTrue($Store->Level1->Level2->Level3->Key === 'Value3');
		return;
	}

	/** @test */
	public function
	TestDeepWriting() {

		$Store = new Deepstore;
		$Store->Key = 'Value0';
		$Store->Level1 = [ 'Key' => 'Value1' ];
		$Store->Level1->Level2 = [ 'Key' => 'Value2' ];
		$Store->Level1->Level2->Level3 = [ 'Key' => 'Value3' ];

		$this->AssertTrue($Store->Key === 'Value0');
		$this->AssertTrue($Store->Level1->Key === 'Value1');
		$this->AssertTrue($Store->Level1->Level2->Key === 'Value2');
		$this->AssertTrue($Store->Level1->Level2->Level3->Key === 'Value3');
		return;
	}

	/** @test */
	public function
	TestDeepWritingBallsDeep() {

		$Store = new Deepstore;
		$Store->Level1->Level2->Level3 = [ 'Key' => 'Value3' ];

		$this->AssertTrue($Store->Level1->Level2->Level3->Key === 'Value3');
		return;
	}

	/** @test */
	public function
	TestDeepWritingMashup() {

		$Store = new Deepstore;
		$Store->Level1 = [
			'Key' => 'Value1',
			'Level2' => [
				'Key' => 'Value2',
				'Level3' => [
					'Key' => 'Value3'
				]
			]
		];

		$this->AssertTrue($Store->Level1->Key === 'Value1');
		$this->AssertTrue($Store->Level1->Level2->Key === 'Value2');
		$this->AssertTrue($Store->Level1->Level2->Level3->Key === 'Value3');
		return;
	}

	/** @test */
	public function
	TestDeepCalling() {

		$Store = new Deepstore;
		$Store->NotCallable = 'asdf';
		$Store->CanHasMethod = function() { return get_class($this); };
		$Store->CopyCat = function($Repeat) { return $Repeat; };

		////////

		$HadException = FALSE;

		try { $Store->DoesNotExist(); }
		catch(Throwable $Err) {
			$HadException = TRUE;
			$this->AssertEquals(1, $Err->GetCode());
		}

		$this->AssertTrue(
			$HadException,
			'we should have had a call exception (does not exist)'
		);

		////////

		$HadException = FALSE;

		try { $Store->NotCallable(); }
		catch(Throwable $Err) {
			$HadException = TRUE;
			$this->AssertEquals(2, $Err->GetCode());
		}

		$this->AssertTrue(
			$HadException,
			'we should have had a call exception (is not callable)'
		);

		$this->AssertTrue($Store->CanHasMethod() === get_class($Store));
		$this->AssertTrue($Store->CopyCat('Repeat') === 'Repeat');
		return;
	}

}
