<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Services\GroupName;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\GroupName\GroupNameService;

class GroupNameServiceTest extends TestCase
{
    public function testGetGroupId(): void
    {
        $service = new GroupNameService();

        $groupId = $service->getGroupID('group--product_7d1abedd2d22436385580e2ff42431b9.pack_unit_plural');

        $this->assertEquals('product_7d1abedd2d22436385580e2ff42431b9', $groupId);
    }


    public function testPropertyName(): void
    {
        $service = new GroupNameService();

        $propertyKey = $service->getPropertyName('group--product_7d1abedd2d22436385580e2ff42431b9.pack_unit_plural');

        $this->assertEquals('pack_unit_plural', $propertyKey);
    }

    /**
     * This test verifies that an invalid group id
     * does not return anything...because theres no ID :)
     *
     */
    public function testInvalidGroupId(): void
    {
        $service = new GroupNameService();

        $propertyKey = $service->getGroupID('no-group--1');

        $this->assertEquals('', $propertyKey);
    }


    public function testInvalidGroupName(): void
    {
        $service = new GroupNameService();

        $propertyKey = $service->getPropertyName('no-group--1');

        $this->assertEquals('no-group--1', $propertyKey);
    }
}
