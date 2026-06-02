<?php

declare(strict_types=1);

namespace Hausformat\Lib\Tests\Unit\Utility;

use Hausformat\Lib\Utility\ObjectStorageUtility;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ObjectStorageUtilityTest extends UnitTestCase
{
    #[Test]
    public function getValueFromDottedPathReturnsValueForFlatArray(): void
    {
        $result = ObjectStorageUtility::getValueFromDottedPath('name', ['name' => 'Alice']);

        $this->assertSame('Alice', $result);
    }

    #[Test]
    public function getValueFromDottedPathTraversesNestedArrayByDottedKey(): void
    {
        $result = ObjectStorageUtility::getValueFromDottedPath('user.name', ['user' => ['name' => 'Alice']]);

        $this->assertSame('Alice', $result);
    }

    #[Test]
    public function getValueFromDottedPathReturnsNullForMissingKey(): void
    {
        $result = ObjectStorageUtility::getValueFromDottedPath('missing', ['name' => 'Alice']);

        $this->assertNull($result);
    }

    #[Test]
    public function getValueFromDottedPathReturnsPrimitiveSubjectAsIs(): void
    {
        $this->assertSame('hello', ObjectStorageUtility::getValueFromDottedPath('any', 'hello'));
        $this->assertSame(42, ObjectStorageUtility::getValueFromDottedPath('any', 42));
    }

    #[Test]
    public function usortByParamReturnsZeroWhenBothParamsResolveToNull(): void
    {
        $result = ObjectStorageUtility::usortByParam(null, null, 'field');

        $this->assertSame(0, $result);
    }

    #[Test]
    public function usortByParamReturnsNegativeOneWhenFirstParamIsNull(): void
    {
        $result = ObjectStorageUtility::usortByParam(null, ['field' => 'value'], 'field');

        $this->assertSame(-1, $result);
    }

    #[Test]
    public function usortByParamReturnsPositiveOneWhenSecondParamIsNull(): void
    {
        $result = ObjectStorageUtility::usortByParam(['field' => 'value'], null, 'field');

        $this->assertSame(1, $result);
    }

    #[Test]
    public function usortByParamComparesNumericFieldsCorrectly(): void
    {
        $higher = ['score' => 5];
        $lower  = ['score' => 3];

        $this->assertSame(1, ObjectStorageUtility::usortByParam($higher, $lower, 'score'));
        $this->assertSame(-1, ObjectStorageUtility::usortByParam($lower, $higher, 'score'));
    }

    #[Test]
    public function sortByOrdersArrayOfAssociativeArraysByNumericFieldAscending(): void
    {
        $data = [
            ['age' => 30],
            ['age' => 20],
            ['age' => 25],
        ];

        $result = ObjectStorageUtility::sortBy($data, 'age');

        $this->assertSame(20, $result[0]['age']);
        $this->assertSame(25, $result[1]['age']);
        $this->assertSame(30, $result[2]['age']);
    }

    #[Test]
    public function sortByWithReverseModeReversesSortedOrder(): void
    {
        $data = [['age' => 10], ['age' => 30], ['age' => 20]];

        $result = ObjectStorageUtility::sortBy($data, 'age', ObjectStorageUtility::MODE_REVERSE);

        $this->assertSame(30, $result[0]['age']);
        $this->assertSame(20, $result[1]['age']);
        $this->assertSame(10, $result[2]['age']);
    }

    #[Test]
    public function getOjectStorageFromIterableReturnsExistingObjectStorageUnchanged(): void
    {
        $storage = new ObjectStorage();

        $result = ObjectStorageUtility::getOjectStorageFromIterable($storage);

        $this->assertSame($storage, $result);
    }

    #[Test]
    public function getOjectStorageFromIterableWrapsArrayInObjectStorage(): void
    {
        $itemA = new \stdClass();
        $itemB = new \stdClass();

        $result = ObjectStorageUtility::getOjectStorageFromIterable([$itemA, $itemB]);

        $this->assertInstanceOf(ObjectStorage::class, $result);
        $this->assertCount(2, $result);
    }
}
