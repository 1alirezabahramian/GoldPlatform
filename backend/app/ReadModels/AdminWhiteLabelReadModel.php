<?php

namespace App\ReadModels;

final class AdminWhiteLabelReadModel
{
    /**
     * @return array<string, mixed>
     */
    public function overview(): array
    {
        return [
            'current_application' => [
                'name' => (string) config('app.name'),
                'locale' => (string) config('app.locale'),
            ],
            'capabilities' => [
                'tenant_entity_supported' => false,
                'company_entity_supported' => false,
                'tenant_scoping_supported' => false,
                'custom_domain_supported' => false,
                'branding_profile_supported' => false,
                'theme_tokens_supported' => false,
                'logo_management_supported' => false,
                'branch_tenant_relation_supported' => false,
                'tenant_user_assignment_supported' => false,
                'tenant_kimia_configuration_supported' => false,
            ],
            'discovery' => [
                'status' => 'not_implemented',
                'source' => 'current_repository_schema_and_code',
                'requires_architecture_decision' => true,
            ],
        ];
    }
}
