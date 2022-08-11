<?php

use Nether\Object\Package\PropertyAttributePackage;
use Nether\Object\Prototype\PropertyCache;
use Nether\Object\Prototype\PropertyInfo;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

#[Attribute(Attribute::TARGET_PROPERTY)]
class LocalPropAttrib1 { }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class LocalAttributedPropClass {

	use
	PropertyAttributePackage;

	public string
	$PropNoAttrib;

	#[LocalPropAttrib1]
	public string
	$PropWithAttrib;

}

class PropertyAttributePackageTest
extends PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestPropertyIndexFetch() {

		// test fetching the index.

		$Props = LocalAttributedPropClass::FetchPropertyIndex();
		$this->AssertTrue(isset($Props['PropNoAttrib']));
		$this->AssertTrue(isset($Props['PropWithAttrib']));

		// test the index contains things we expected.

		$Info = NULL;

		foreach($Props as $Info)
		$this->AssertTrue($Info instanceof PropertyInfo);

		return;
	}

	/** @test */
	public function
	TestPropertyIndexCache() {

		// test that the cache appeared to be working.

		$Props = LocalAttributedPropClass::GetPropertyIndex();
		$this->AssertTrue(isset($Props['PropNoAttrib']));
		$this->AssertTrue(isset($Props['PropWithAttrib']));
		$this->AssertTrue(PropertyCache::Has(LocalAttributedPropClass::class));
		$this->AssertEquals(
			count(PropertyCache::Get(LocalAttributedPropClass::class)),
			count($Props)
		);

		// test that the cache actually works by confirming the info
		// instances are the same copies.

		$Cached = LocalAttributedPropClass::GetPropertyIndex();
		$Key = NULL;
		$Info = NULL;

		foreach($Cached as $Key => $Info) {
			$this->AssertTrue($Info instanceof PropertyInfo);
			$this->AssertTrue($Props[$Key] === $Info);
		}

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	/** @test */
	public function
	TestPropertyIndexFetchFilteredByAttr() {

		// test fetching the index.

		$Props = LocalAttributedPropClass::FetchPropertiesWithAttribute(LocalPropAttrib1::class);
		$this->AssertFalse(isset($Props['PropNoAttrib']));
		$this->AssertTrue(isset($Props['PropWithAttrib']));
		$this->AssertEquals(count($Props), 1);

		// test the index contains things we expected.

		$Info = NULL;

		foreach($Props as $Info)
		$this->AssertTrue($Info instanceof PropertyInfo);

		return;
	}

	/** @test */
	public function
	TestPropertyIndexCacheFilteredByAttr() {

		// test fetching the index.

		$Props = LocalAttributedPropClass::GetPropertiesWithAttribute(LocalPropAttrib1::class);
		$this->AssertFalse(isset($Props['PropNoAttrib']));
		$this->AssertTrue(isset($Props['PropWithAttrib']));
		$this->AssertEquals(count($Props), 1);

		// test the index contains things we expected.

		$Info = NULL;

		foreach($Props as $Info)
		$this->AssertTrue($Info instanceof PropertyInfo);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	/** @test */
	public function
	TestPropertyIndexFetchMethodInfoAttribs() {

		$Props = LocalAttributedPropClass::FetchPropertyIndex();
		$AttribYep = $Props['PropWithAttrib'];
		$AttribNope = $Props['PropNoAttrib'];

		$this->AssertEquals(count($AttribYep->Attributes), 1);
		$this->AssertEquals(count($AttribNope->Attributes), 0);
		$this->AssertInstanceOf(
			LocalPropAttrib1::class,
			$AttribYep->Attributes[0]
		);

		return;
	}

	/** @test */
	public function
	TestMethodIndexCacheMethodInfoAttribs() {

		$Props = LocalAttributedPropClass::GetPropertyIndex();
		$AttribYep = $Props['PropWithAttrib'];
		$AttribNope = $Props['PropNoAttrib'];

		$this->AssertEquals(count($AttribYep->Attributes), 1);
		$this->AssertEquals(count($AttribNope->Attributes), 0);
		$this->AssertInstanceOf(
			LocalPropAttrib1::class,
			$AttribYep->Attributes[0]
		);

		return;
	}

}
