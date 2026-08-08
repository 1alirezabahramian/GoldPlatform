<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class BackofficeIdentityOpenApiContractTest extends TestCase
{
    #[Test]
    public function backoffice_identity_openapi_tracks_current_routes_and_fail_closed_readiness(): void
    {
        $routes = (string) file_get_contents(base_path('routes/api.php'));
        $spec = (string) file_get_contents(base_path('../docs/api/backoffice-v1.openapi.yaml'));

        foreach ([
            '/staff/login',
            '/auth/staff/change-password',
            '/settings/identity-onboarding',
        ] as $routeFragment) {
            self::assertStringContainsString($routeFragment, $routes);
        }

        foreach ([
            '/auth/staff/login:',
            '/auth/staff/change-password:',
            '/admin/settings/identity-onboarding:',
            'REGISTRATION_MODE_DEPENDENCY_NOT_READY',
            'enum: [manual, assisted, automatic]',
            'customer_auth_mode: { type: string, const: otp }',
            'staff_auth_mode: { type: string, const: password }',
        ] as $contractFragment) {
            self::assertStringContainsString($contractFragment, $spec);
        }

        self::assertStringNotContainsString('admin/admin', $spec);
        self::assertStringNotContainsString('Kimia Write payload', $spec);
    }
}
