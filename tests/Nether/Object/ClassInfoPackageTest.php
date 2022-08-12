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
	TestClassInfoCacheBasics() {

		$this->AssertFalse(ClassInfoCache::Has('SomethingNeverCached'));
		$this->AssertNull(ClassInfoCache::Get('SomethingNeverCached'));

		return;
	}

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
		$CI3 = NULL;

		// check for expected values.

		$this->AssertInstanceOf(ClassInfo::class, $CI1);
		$this->AssertEquals(
			TestClass1::class,
			"{$CI1->Namespace}\\{$CI1->Name}"
		);

		// check that the same instance.

		$this->AssertTrue($CI1 === $CI2);

		// check we can flush the cache.

		$this->AssertTrue(ClassInfoCache::Has(TestClass1::class));
		ClassInfoCache::Drop(TestClass1::class);
		$this->AssertFalse(ClassInfoCache::Has(TestClass1::class));

		$CI3 = TestClass1::GetClassInfo();
		$this->AssertFalse($CI1 === $CI3);

		return;
	}

	/** @test */
	public function
	TestClassInfoFetchAttributes() {

		$CI = TestClass1::GetClassInfo();

		// check for expected values.

		$this->AssertInstanceOf(ClassInfo::class, $CI);
		$this->AssertIsArray($CI->Attributes);
		$this->AssertCount(1, $CI->Attributes);
		$this->AssertInstanceOf(
			TestClassAttrib1::class,
			$CI->GetAttribute(TestClassAttrib1::class)
		);

		return;
	}

	/** @test */
	public function
	TestClassInfoAttributeManageMulti() {

		$Class = TestClass2::GetClassInfo();
		$A1 = TestClassAttribThereCanBeOnlyOne::class;
		$A3 = TestClassAttribHousePartyProtocol::class;
		$Attrib = NULL;

		// check them raw.

		$this->AssertCount(2, $Class->Attributes);
		$this->AssertInstanceOf($A1, $Class->Attributes[$A1]);
		$this->AssertIsArray($Class->Attributes[$A3]);
		$this->AssertCount(3, $Class->Attributes[$A3]);

		foreach($Class->Attributes[$A3] as $Attrib)
		$this->AssertInstanceOf($A3, $Attrib);

		// check them from the api.

		$this->AssertTrue($Class->HasAttribute($A1));
		$this->AssertFalse($Class->HasAttribute('ThisDoesNotExist'));
		$this->AssertNull($Class->GetAttribute('ThisDoesNotExist'));
		$this->AssertInstanceOf($A1, $Class->GetAttribute($A1));
		$this->AssertIsArray($Class->GetAttribute($A3));
		$this->AssertCount(3, $Class->GetAttribute($A3));

		foreach($Class->GetAttribute($A3) as $Attrib)
		$this->AssertInstanceOf($A3, $Attrib);

		return;
	}

}
