<?php

namespace phpunit\Services\GroupName;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\GroupName\GroupNameService;

class GroupNameServiceTest extends TestCase
{

    /**
     * @return void
     */
    public function testGetGroupId()
    {
        $service = new GroupNameService();

        $groupId = $service->getGroupID('group--product_7d1abedd2d22436385580e2ff42431b9.pack_unit_plural');

        $this->assertEquals('product_7d1abedd2d22436385580e2ff42431b9', $groupId);
    }

    /**
     * @return void
     */
    public function testPropertyName()
    {
        $service = new GroupNameService();

        $propertyKey = $service->getPropertyName('group--product_7d1abedd2d22436385580e2ff42431b9.pack_unit_plural');

        $this->assertEquals('pack_unit_plural', $propertyKey);
    }

    /**
     * This test verifies that an invalid group id
     * does not return anything...because theres no ID :)
     *
     * @return void
     */
    public function testInvalidGroupId()
    {
        $service = new GroupNameService();

        $propertyKey = $service->getGroupID('no-group--1');

        $this->assertEquals('', $propertyKey);
    }

    /**
     * @return void
     */
    public function testInvalidGroupName()
    {
        $service = new GroupNameService();

        $propertyKey = $service->getPropertyName('no-group--1');

        $this->assertEquals('no-group--1', $propertyKey);
    }

}