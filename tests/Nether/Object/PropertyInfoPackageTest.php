<?php

namespace NetherTestSuite\PropertyInfoPackageTest;
use PHPUnit;

use Attribute;
use ReflectionProperty;
use ReflectionAttribute;
use Throwable;

use Nether\Object\Prototype;
use Nether\Object\Prototype\PropertyInfo;
use Nether\Object\Prototype\PropertyInfoCache;
use Nether\Object\Prototype\PropertyInfoInterface;
use Nether\Object\Package\PropertyInfoPackage;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

#[Attribute(Attribute::TARGET_PROPERTY)]
class TestPropAttrib1
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

#[Attribute(Attribute::TARGET_PROPERTY)]
class TestPropAttribThereCanBeOnlyOne { }

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
class TestPropAttribHousePartyProtocol { }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class TestClassProp1 {

	use
	PropertyInfoPackage;

	public string
	$PropNoAttrib;

	#[TestPropAttrib1]
	public string
	$PropWithAttrib;

}

class TestClassProp2
extends Prototype {

	#[TestPropAttribThereCanBeOnlyOne]
	#[TestPropAttribHousePartyProtocol]
	#[TestPropAttribHousePartyProtocol]
	#[TestPropAttribHousePartyProtocol]
	public int
	$Prop;

}

class TestClassProp3
extends Prototype {

	// see TestPropertyInfoAttributeHandleMultifail

	#[TestPropAttribThereCanBeOnlyOne]
	#[TestPropAttribThereCanBeOnlyOne]
	#[TestPropAttribHousePartyProtocol]
	#[TestPropAttribHousePartyProtocol]
	#[TestPropAttribHousePartyProtocol]
	public int
	$Prop;

}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class PropertyInfoPackageTest
extends PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestPropertyInfoFetchGet():
	void {

		// check that raw reading works as expected.

		$Info1 = TestClassProp1::FetchPropertyInfo('PropWithAttrib');
		$Info2 = TestClassProp1::FetchPropertyInfo('PropWithAttrib');
		$Null = TestClassProp1::FetchPropertyInfo('DoesNotExist');

		$this->AssertInstanceOf(PropertyInfo::class, $Info1);
		$this->AssertTrue($Info1 !== $Info2);
		$this->AssertNull($Null);

		// check that cached reading worked as expected.

		$Info1 = TestClassProp1::GetPropertyInfo('PropWithAttrib');
		$Info2 = TestClassProp1::GetPropertyInfo('PropWithAttrib');
		$Null = TestClassProp1::GetPropertyInfo('DoesNotExist');

		$this->AssertInstanceOf(PropertyInfo::class, $Info1);
		$this->AssertTrue($Info1 === $Info2);
		$this->AssertNull($Null);

		return;
	}

	/** @test */
	public function
	TestPropertyInfoCacheBasics() {

		$this->AssertFalse(PropertyInfoCache::Has('SomethingNeverCached'));
		$this->AssertNull(PropertyInfoCache::Get('SomethingNeverCached'));

		return;
	}

	/** @test */
	public function
	TestPropertyIndexFetch() {

		// test fetching the index.

		$Props = TestClassProp1::FetchPropertyIndex();
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

		$Props = TestClassProp1::GetPropertyIndex();
		$this->AssertTrue(isset($Props['PropNoAttrib']));
		$this->AssertTrue(isset($Props['PropWithAttrib']));
		$this->AssertTrue(PropertyInfoCache::Has(TestClassProp1::class));
		$this->AssertEquals(
			count(PropertyInfoCache::Get(TestClassProp1::class)),
			count($Props)
		);

		// test that the cache actually works by confirming the info
		// instances are the same copies.

		$Cached = TestClassProp1::GetPropertyIndex();
		$Key = NULL;
		$Info = NULL;

		foreach($Cached as $Key => $Info) {
			$this->AssertTrue($Info instanceof PropertyInfo);
			$this->AssertTrue($Props[$Key] === $Info);
		}

		// check we can flush the cache.

		$this->AssertTrue(PropertyInfoCache::Has(TestClassProp1::class));
		PropertyInfoCache::Drop(TestClassProp1::class);
		$this->AssertFalse(PropertyInfoCache::Has(TestClassProp1::class));

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	/** @test */
	public function
	TestPropertyIndexFetchFilteredByAttr() {

		// test fetching the index.

		$Props = TestClassProp1::FetchPropertiesWithAttribute(TestPropAttrib1::class);
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

		$Props = TestClassProp1::GetPropertiesWithAttribute(TestPropAttrib1::class);
		$Props = TestClassProp1::GetPropertiesWithAttribute(TestPropAttrib1::class);
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
	TestPropertyInfoLoadingAttributes() {

		$Props = TestClassProp1::FetchPropertyIndex();
		$AttribYep = $Props['PropWithAttrib'];
		$AttribNope = $Props['PropNoAttrib'];

		$this->AssertEquals(count($AttribYep->Attributes), 1);
		$this->AssertEquals(count($AttribNope->Attributes), 0);

		$this->AssertArrayHasKey(
			TestPropAttrib1::class,
			$AttribYep->Attributes
		);

		$this->AssertInstanceOf(
			TestPropAttrib1::class,
			$AttribYep->Attributes[TestPropAttrib1::class]
		);

		return;
	}

	/** @test */
	public function
	TestPropertyInfoInterface() {

		$Props = TestClassProp1::FetchPropertyIndex();
		$Prop = $Props['PropWithAttrib'];
		$Attrib = $Prop->GetAttribute(TestPropAttrib1::class);

		// test that the attribute implemeneted the prop info interface
		// and that the attribute executed the self learning.

		$this->AssertTrue($Attrib instanceof TestPropAttrib1);
		$this->AssertTrue($Attrib instanceof PropertyInfoInterface);
		$this->AssertTrue($Attrib->DidPropertyInfo);

		return;
	}

	/** @test */
	public function
	TestPropertyInfoAttributeManageMulti() {

		$Props = TestClassProp2::GetPropertyIndex();
		$Prop = $Props['Prop'];
		$A1 = TestPropAttribThereCanBeOnlyOne::class;
		$A3 = TestPropAttribHousePartyProtocol::class;
		$Attrib = NULL;

		// check them raw.

		$this->AssertCount(2, $Prop->Attributes);
		$this->AssertInstanceOf($A1, $Prop->Attributes[$A1]);
		$this->AssertIsArray($Prop->Attributes[$A3]);
		$this->AssertCount(3, $Prop->Attributes[$A3]);

		foreach($Prop->Attributes[$A3] as $Attrib)
		$this->AssertInstanceOf($A3, $Attrib);

		// check them from the api.

		$this->AssertTrue($Prop->HasAttribute($A1));
		$this->AssertFalse($Prop->HasAttribute('ThisDoesNotExist'));
		$this->AssertNull($Prop->GetAttribute('ThisDoesNotExist'));
		$this->AssertInstanceOf($A1, $Prop->GetAttribute($A1));
		$this->AssertIsArray($Prop->GetAttribute($A3));
		$this->AssertCount(3, $Prop->GetAttribute($A3));

		foreach($Prop->GetAttribute($A3) as $Attrib)
		$this->AssertInstanceOf($A3, $Attrib);

		return;
	}

	/** @test-if-php-unstupids-itself */
	public function
	TestPropertyInfoAttributeHandleMultiFail() {

		// there is presently no point fleshing this feature out because
		// php isn't giving us a specific exception type or even a specific
		// error code to test how the attribute failed.

		// string(5) "Error"
		// int(0)
		// string(71) "Attribute "Nether\TestPropAttribThereCanBeOnlyOne"
		// must not be repeated"

		try {
			$Props = TestClassProp3::GetPropertyIndex();
		}

		catch(Throwable $Err) {
			echo PHP_EOL;
			var_dump($Err);
			echo PHP_EOL;
			$this->AssertFalse(TRUE);
		}

		return;
	}

}
