<?php

use Nether\Object\Package\MethodAttributePackage;
use Nether\Object\Prototype\MethodInfo;
use Nether\Object\Prototype\MethodInfoInterface;
use Nether\Object\Prototype\MethodInfoCache;


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

#[Attribute(Attribute::TARGET_METHOD)]
class LocalTestAttrib1
implements MethodInfoInterface {

	public bool
	$DidMethodInfo = FALSE;

	public function
	OnMethodInfo(MethodInfo $Info, ReflectionMethod $Method, ReflectionAttribute $Attrib):
	void {

		$this->DidMethodInfo = TRUE;
		return;
	}

}

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
		$this->AssertTrue(MethodInfoCache::Has(LocalAttributedClass::class));
		$this->AssertEquals(
			count(MethodInfoCache::Get(LocalAttributedClass::class)),
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
	TestMethodIndexFetchMethodAttribs() {

		$Methods = LocalAttributedClass::FetchMethodIndex();
		$AttribYep = $Methods['MethodWithAttrib'];
		$AttribNope = $Methods['MethodNoAttrib'];

		$this->AssertEquals(count($AttribYep->Attributes), 1);
		$this->AssertEquals(count($AttribNope->Attributes), 0);

		$this->AssertArrayHasKey(
			LocalTestAttrib1::class,
			$AttribYep->Attributes
		);

		$this->AssertInstanceOf(
			LocalTestAttrib1::class,
			$AttribYep->Attributes[LocalTestAttrib1::class]
		);

		return;
	}

	/** @test */
	public function
	TestMethodInfoInterface() {

		$Methods = LocalAttributedClass::FetchMethodIndex();
		$Method = $Methods['MethodWithAttrib'];
		$Attrib = $Method->GetAttribute(LocalTestAttrib1::class);

		// test that the attribute implemeneted the method info interface
		// and that the attribute executed the self learning.

		$this->AssertTrue($Attrib instanceof LocalTestAttrib1);
		$this->AssertTrue($Attrib instanceof MethodInfoInterface);
		$this->AssertTrue($Attrib->DidMethodInfo);

		return;
	}

}
