<?php

class DeepstoreTest
extends \PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestDeepReading() {

		$Store = new Nether\Object\Deepstore;
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

		$Store = new Nether\Object\Deepstore;
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

		$Store = new Nether\Object\Deepstore;
		$Store->Level1->Level2->Level3 = [ 'Key' => 'Value3' ];

		$this->AssertTrue($Store->Level1->Level2->Level3->Key === 'Value3');
		return;
	}

	/** @test */
	public function
	TestDeepWritingMashup() {

		$Store = new Nether\Object\Deepstore;
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

		$Store = new Nether\Object\Deepstore;

		$Store->CanHasMethod = function() {
			return get_class($this);
		};

		$Store->CopyCat = function($Repeat) {
			return $Repeat;
		};

		$this->AssertTrue($Store->CanHasMethod() === get_class($Store));
		$this->AssertTrue($Store->CopyCat('Repeat') === 'Repeat');
		return;
	}

}
