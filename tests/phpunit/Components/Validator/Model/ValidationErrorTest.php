<?php

namespace phpunit\Components\Validator\Model;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\Model\ValidationError;

class ValidationErrorTest extends TestCase
{

    /**
     * @var ValidationError
     */
    private $error;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->error = new ValidationError(
            'NOT_FOUND',
            'Translation not found',
            'en_US',
            'en.json',
            'ID-123',
            14
        );
    }

    /**
     * @return void
     */
    public function testClassification(): void
    {
        $this->assertEquals('NOT_FOUND', $this->error->getClassification());
    }

    /**
     * @return void
     */
    public function testLocale(): void
    {
        $this->assertEquals('en_US', $this->error->getLocale());
    }

    /**
     * @return void
     */
    public function testFilename(): void
    {
        $this->assertEquals('en.json', $this->error->getFilename());
    }

    /**
     * @return void
     */
    public function testMessage(): void
    {
        $this->assertEquals('Translation not found', $this->error->getMessage());
    }

    /**
     * @return void
     */
    public function testIdentifier(): void
    {
        $this->assertEquals('ID-123', $this->error->getIdentifier());
    }

    /**
     * @return void
     */
    public function testLineNumber(): void
    {
        $this->assertEquals('14', $this->error->getLineNumber());
    }
}
