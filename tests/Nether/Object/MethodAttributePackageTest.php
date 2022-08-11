<?php

use Nether\Object\Package\MethodAttributePackage;
use Nether\Object\Prototype\MethodCache;
use Nether\Object\Prototype\MethodInfo;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

#[Attribute(Attribute::TARGET_METHOD)]
class LocalTestAttrib1 { }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class LocalAttributedClass {

	use
	MethodAttributePackage;

	public function
	MethodNoAttrib():
	void {

		return;
	}

	#[LocalTestAttrib1]
	public function
	MethodWithAttrib():
	void {

		return;
	}

}

class MethodAttributePackageTest
extends PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestMethodIndexFetch() {

		// test fetching the index.

		$Methods = LocalAttributedClass::FetchMethodIndex();
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

		$Methods = LocalAttributedClass::GetMethodIndex();
		$this->AssertTrue(isset($Methods['MethodNoAttrib']));
		$this->AssertTrue(isset($Methods['MethodWithAttrib']));
		$this->AssertTrue(isset($Methods['FetchMethodIndex']));
		$this->AssertTrue(MethodCache::Has(LocalAttributedClass::class));
		$this->AssertEquals(
			count(MethodCache::Get(LocalAttributedClass::class)),
			count($Methods)
		);

		// test that the cache actually works by confirming the info
		// instances are the same copies.

		$Cached = LocalAttributedClass::GetMethodIndex();
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

		$Methods = LocalAttributedClass::FetchMethodsWithAttribute(LocalTestAttrib1::class);
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

		$Methods = LocalAttributedClass::GetMethodsWithAttribute(LocalTestAttrib1::class);
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
	TestMethodIndexFetchMethodInfoAttribs() {

		$Methods = LocalAttributedClass::FetchMethodIndex();
		$AttribYep = $Methods['MethodWithAttrib'];
		$AttribNope = $Methods['MethodNoAttrib'];

		$this->AssertEquals(count($AttribYep->Attributes), 1);
		$this->AssertEquals(count($AttribNope->Attributes), 0);
		$this->AssertInstanceOf(
			LocalTestAttrib1::class,
			$AttribYep->Attributes[0]
		);

		return;
	}

	/** @test */
	public function
	TestMethodIndexCacheMethodInfoAttribs() {

		$Methods = LocalAttributedClass::GetMethodIndex();
		$AttribYep = $Methods['MethodWithAttrib'];
		$AttribNope = $Methods['MethodNoAttrib'];

		$this->AssertEquals(count($AttribYep->Attributes), 1);
		$this->AssertEquals(count($AttribNope->Attributes), 0);
		$this->AssertInstanceOf(
			LocalTestAttrib1::class,
			$AttribYep->Attributes[0]
		);

		return;
	}

}
