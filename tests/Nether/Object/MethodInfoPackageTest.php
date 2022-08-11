<?php

namespace NetherTestSuite;
use PHPUnit;

use Attribute;
use ReflectionMethod;
use ReflectionAttribute;

use Nether\Object\Prototype;
use Nether\Object\Prototype\MethodInfo;
use Nether\Object\Prototype\MethodInfoCache;
use Nether\Object\Prototype\MethodInfoInterface;
use Nether\Object\Package\MethodInfoPackage;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

#[Attribute(Attribute::TARGET_METHOD)]
class TestMethodAttrib1
implements MethodInfoInterface {

	public bool
	$DidMethodInfo = FALSE;

	public function
	OnMethodInfo(MethodInfo $MI, ReflectionMethod $RM, ReflectionAttribute $RA):
	void {

		$this->DidMethodInfo = TRUE;
		return;
	}

}

#[Attribute(Attribute::TARGET_METHOD)]
class TestMethodAttribThereCanBeOnlyOne { }

#[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
class TestMethodAttribHousePartyProtocol { }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class TestClassMethod1 {

	use
	MethodInfoPackage;

	public function
	MethodNoAttrib():
	void {

		return;
	}

	#[TestMethodAttrib1]
	public function
	MethodWithAttrib():
	void {

		return;
	}

}

class TestClassMethod2
extends Prototype {

	#[TestMethodAttribThereCanBeOnlyOne]
	#[TestMethodAttribHousePartyProtocol]
	#[TestMethodAttribHousePartyProtocol]
	#[TestMethodAttribHousePartyProtocol]
	public function
	Method():
	void {

		return;
	}

}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class MethodInfoPackageTest
extends PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestMethodIndexFetch() {

		// test fetching the index.

		$Methods = TestClassMethod1::FetchMethodIndex();
		$this->AssertTrue(isset($Methods['MethodNoAttrib']));
		$this->AssertTrue(isset($Methods['MethodWithAttrib']));
		$this->AssertTrue(isset($Methods['FetchMethodIndex']));

		// test the index contains things we expected.

		$Info = NULL;

		foreach($Methods as $Info)
		$this->AssertTrue($Info instanceof MethodInfo);

		return;
	}

	/** @test */
	public function
	TestMethodIndexCache() {

		// test that the cache appeared to be working.

		$Methods = TestClassMethod1::GetMethodIndex();
		$this->AssertTrue(isset($Methods['MethodNoAttrib']));
		$this->AssertTrue(isset($Methods['MethodWithAttrib']));
		$this->AssertTrue(isset($Methods['FetchMethodIndex']));
		$this->AssertTrue(MethodInfoCache::Has(TestClassMethod1::class));
		$this->AssertEquals(
			count(MethodInfoCache::Get(TestClassMethod1::class)),
			count($Methods)
		);

		// test that the cache actually works by confirming the info
		// instances are the same copies.

		$Cached = TestClassMethod1::GetMethodIndex();
		$Key = NULL;
		$Info = NULL;

		foreach($Cached as $Key => $Info) {
			$this->AssertTrue($Info instanceof MethodInfo);
			$this->AssertTrue($Methods[$Key] === $Info);
		}

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	/** @test */
	public function
	TestMethodIndexFetchFilteredByAttr() {

		// test fetching the index.

		$Methods = TestClassMethod1::FetchMethodsWithAttribute(TestMethodAttrib1::class);
		$this->AssertFalse(isset($Methods['MethodNoAttrib']));
		$this->AssertTrue(isset($Methods['MethodWithAttrib']));
		$this->AssertEquals(count($Methods), 1);

		// test the index contains things we expected.

		$Info = NULL;

		foreach($Methods as $Info)
		$this->AssertTrue($Info instanceof MethodInfo);

		return;
	}

	/** @test */
	public function
	TestMethodIndexCacheFilteredByAttr() {

		// test fetching the index.

		$Methods = TestClassMethod1::GetMethodsWithAttribute(TestMethodAttrib1::class);
		$this->AssertFalse(isset($Methods['MethodNoAttrib']));
		$this->AssertTrue(isset($Methods['MethodWithAttrib']));
		$this->AssertEquals(count($Methods), 1);

		// test the index contains things we expected.

		$Info = NULL;

		foreach($Methods as $Info)
		$this->AssertTrue($Info instanceof MethodInfo);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	/** @test */
	public function
	TestMethodIndexFetchMethodAttribs() {

		$Methods = TestClassMethod1::FetchMethodIndex();
		$AttribYep = $Methods['MethodWithAttrib'];
		$AttribNope = $Methods['MethodNoAttrib'];

		$this->AssertEquals(count($AttribYep->Attributes), 1);
		$this->AssertEquals(count($AttribNope->Attributes), 0);

		$this->AssertArrayHasKey(
			TestMethodAttrib1::class,
			$AttribYep->Attributes
		);

		$this->AssertInstanceOf(
			TestMethodAttrib1::class,
			$AttribYep->Attributes[TestMethodAttrib1::class]
		);

		return;
	}

	/** @test */
	public function
	TestMethodInfoInterface() {

		$Methods = TestClassMethod1::FetchMethodIndex();
		$Method = $Methods['MethodWithAttrib'];
		$Attrib = $Method->GetAttribute(TestMethodAttrib1::class);

		// test that the attribute implemeneted the method info interface
		// and that the attribute executed the self learning.

		$this->AssertTrue($Attrib instanceof TestMethodAttrib1);
		$this->AssertTrue($Attrib instanceof MethodInfoInterface);
		$this->AssertTrue($Attrib->DidMethodInfo);

		return;
	}

	/** @test */
	public function
	TestPropertyInfoAttributeManageMulti() {

		$Methods = TestClassMethod2::GetMethodIndex();
		$Method = $Methods['Method'];
		$A1 = TestMethodAttribThereCanBeOnlyOne::class;
		$A3 = TestMethodAttribHousePartyProtocol::class;
		$Attrib = NULL;

		// check them raw.

		$this->AssertCount(2, $Method->Attributes);
		$this->AssertInstanceOf($A1, $Method->Attributes[$A1]);
		$this->AssertIsArray($Method->Attributes[$A3]);
		$this->AssertCount(3, $Method->Attributes[$A3]);

		foreach($Method->Attributes[$A3] as $Attrib)
		$this->AssertInstanceOf($A3, $Attrib);

		// check them from the api.

		$this->AssertNull($Method->GetAttribute('ThisDoesNotExist'));
		$this->AssertInstanceOf($A1, $Method->GetAttribute($A1));
		$this->AssertIsArray($Method->GetAttribute($A3));
		$this->AssertCount(3, $Method->GetAttribute($A3));

		foreach($Method->GetAttribute($A3) as $Attrib)
		$this->AssertInstanceOf($A3, $Attrib);

		return;
	}

}
