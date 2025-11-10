<?php
use PHPUnit\Framework\TestCase;
// Include your controller
require_once __DIR__ . '/../../controllers/admin_controller.php';

class ExampleTest extends TestCase
{

public function testIsValidUuid(): void
    {
        // Use reflection to access the private static method
        $reflection = new ReflectionClass('admin_controller');
        $method = $reflection->getMethod('is_valid_uuid');
        $method->setAccessible(true);

        // Valid UUIDs
        $valid_uuids = [
            '123e4567-e89b-12d3-a456-426614174000',
            '550e8400-e29b-41d4-a716-446655440000',
            'f47ac10b-58cc-4372-a567-0e02b2c3d479'
        ];

        // Invalid UUIDs
        $invalid_uuids = [
            '',                                 // empty string
            'not-a-uuid',                       // random string
            '12345678-1234-1234-1234-1234567', // too short
            'g47ac10b-58cc-4372-a567-0e02b2c3d479', // invalid character
        ];

        // Assert valid UUIDs return true
        foreach ($valid_uuids as $uuid) {
            $this->assertTrue($method->invoke(null, $uuid), "Failed asserting that '$uuid' is valid.");
        }

        // Assert invalid UUIDs return false
        foreach ($invalid_uuids as $uuid) {
            $this->assertFalse($method->invoke(null, $uuid), "Failed asserting that '$uuid' is invalid.");
        }
    }

        public function testTrueIsTrue(): void
    {
        // Simple assertion to ensure PHPUnit is working
        $this->assertTrue(true);
    }
}