<?php

use Nether\Object\Prototype\PropertyInfo;
use Nether\Object\Prototype\PropertyInfoCache;
use Nether\Object\Prototype\PropertyInfoInterface;
use Nether\Object\Package\PropertyInfoPackage;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

#[Attribute(Attribute::TARGET_PROPERTY)]
class LocalPropAttrib1
implements PropertyInfoInterface {

	public bool
	$DidPropertyInfo = FALSE;

	public function
	OnPropertyInfo(PropertyInfo $PI, ReflectionProperty $RP, ReflectionAttribute $RA):
	void {

		$this->DidPropertyInfo = TRUE;
		return;
	}

}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class LocalAttributedPropClass {

	use
	PropertyInfoPackage;

	public string
	$PropNoAttrib;

	#[LocalPropAttrib1]
	public string
	$PropWithAttrib;

}

class PropertyInfoPackageTest
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
		$this->AssertTrue(PropertyInfoCache::Has(LocalAttributedPropClass::class));
		$this->AssertEquals(
			count(PropertyInfoCache::Get(LocalAttributedPropClass::class)),
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
	TestPropertyIndexFetchPropertyAttribs() {

		$Props = LocalAttributedPropClass::FetchPropertyIndex();
		$AttribYep = $Props['PropWithAttrib'];
		$AttribNope = $Props['PropNoAttrib'];

		$this->AssertEquals(count($AttribYep->Attributes), 1);
		$this->AssertEquals(count($AttribNope->Attributes), 0);

		$this->AssertArrayHasKey(
			LocalPropAttrib1::class,
			$AttribYep->Attributes
		);

		$this->AssertInstanceOf(
			LocalPropAttrib1::class,
			$AttribYep->Attributes[LocalPropAttrib1::class]
		);

		return;
	}

	/** @test */
	public function
	TestPropertyInfoInterface() {

		$Props = LocalAttributedPropClass::FetchPropertyIndex();
		$Prop = $Props['PropWithAttrib'];
		$Attrib = $Prop->GetAttribute(LocalPropAttrib1::class);

		// test that the attribute implemeneted the prop info interface
		// and that the attribute executed the self learning.

		$this->AssertTrue($Attrib instanceof LocalPropAttrib1);
		$this->AssertTrue($Attrib instanceof PropertyInfoInterface);
		$this->AssertTrue($Attrib->DidPropertyInfo);

		return;
	}

}