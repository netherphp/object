<?php

namespace NetherTestSuite\ClassInfoPackage;
use PHPUnit;

use Attribute;
use ReflectionClass;
use ReflectionAttribute;

use Nether\Object\Prototype;
use Nether\Object\Prototype\ClassInfo;
use Nether\Object\Prototype\ClassInfoCache;
use Nether\Object\Prototype\ClassInfoInterface;
use Nether\Object\Package\ClassInfoPackage;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

#[Attribute(Attribute::TARGET_CLASS)]
class TestClassAttrib1
implements ClassInfoInterface {

	public bool
	$DidClassInfo = FALSE;

	public function
	OnClassInfo(ClassInfo $MI, ReflectionClass $RM, ReflectionAttribute $RA):
	void {

		$this->DidClassInfo = TRUE;
		return;
	}

}

#[Attribute(Attribute::TARGET_CLASS)]
class TestClassAttribThereCanBeOnlyOne { }

#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class TestClassAttribHousePartyProtocol { }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

#[TestClassAttrib1]
class TestClass1 {

	use
	ClassInfoPackage;

}

#[TestClassAttribThereCanBeOnlyOne]
#[TestClassAttribHousePartyProtocol]
#[TestClassAttribHousePartyProtocol]
#[TestClassAttribHousePartyProtocol]
class TestClass2
extends Prototype {

}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class ClassInfoPackageTest
extends PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestClassInfoFetch() {

		$CI = TestClass1::FetchClassInfo();

		// check for expected values.

		$this->AssertInstanceOf(ClassInfo::class, $CI);
		$this->AssertEquals(
			TestClass1::class,
			"{$CI->Namespace}\\{$CI->Name}"
		);

		return;
	}

	/** @test */
	public function
	TestClassInfoCache() {

		$CI1 = TestClass1::GetClassInfo();
		$CI2 = TestClass1::GetClassInfo();

		// check for expected values.

		$this->AssertInstanceOf(ClassInfo::class, $CI1);
		$this->AssertEquals(
			TestClass1::class,
			"{$CI1->Namespace}\\{$CI1->Name}"
		);

		// check that the same instance.

		$this->AssertTrue($CI1 === $CI2);

		return;
	}

	/** @test */
	public function
	TestClassInfoAttributes() {

		$CI = TestClass1::GetClassInfo();

		// check for expected values.

		$this->AssertInstanceOf(ClassInfo::class, $CI);
		$this->AssertIsArray($CI->Attributes);
		$this->AssertCount(1, $CI->Attributes);

		return;
	}


}
