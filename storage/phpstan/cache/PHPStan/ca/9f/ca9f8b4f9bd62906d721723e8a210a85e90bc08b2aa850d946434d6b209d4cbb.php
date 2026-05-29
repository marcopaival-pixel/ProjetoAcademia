<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Services\TenantBackupService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\TenantBackupService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-63c7dc22f817faadb9ff2d37ca0b9f3da24f40da1cb10f29a5543feb218e5bdb',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\TenantBackupService',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Services/TenantBackupService.php',
      ),
    ),
    'namespace' => 'App\\Services',
    'name' => 'App\\Services\\TenantBackupService',
    'shortName' => 'TenantBackupService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 170,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'directTables' => 
      array (
        'declaringClassName' => 'App\\Services\\TenantBackupService',
        'implementingClassName' => 'App\\Services\\TenantBackupService',
        'name' => 'directTables',
        'modifiers' => 2,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[\'academy_units\', \'clinic_onboarding_steps\', \'clinic_protocols\', \'financial_logs\', \'historico_pdfs\', \'medical_prescriptions\', \'menu_permission_audit_logs\', \'omni_agents\', \'omni_bots\', \'omni_business_hours\', \'omni_channels\', \'omni_chatbot_rules\', \'omni_conversations\', \'omni_queues\', \'pdf_number_sequences\', \'pdf_templates\', \'role_menu_permissions\', \'subscriptions\', \'users\']',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 36,
            'startTokenPos' => 48,
            'startFilePos' => 337,
            'endTokenPos' => 106,
            'endFilePos' => 868,
          ),
        ),
        'docComment' => '/**
 * Tables that have academy_company_id directly.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'userRelatedTables' => 
      array (
        'declaringClassName' => 'App\\Services\\TenantBackupService',
        'implementingClassName' => 'App\\Services\\TenantBackupService',
        'name' => 'userRelatedTables',
        'modifiers' => 2,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[\'user_profiles\', \'training_plans\', \'meal_templates\', \'body_assessments\', \'exercise_entries\', \'food_entries\', \'water_entries\', \'weight_entries\', \'ai_chats\', \'evolution_photos\', \'workout_sessions\', \'load_logs\', \'achievements\', \'user_achievements\', \'user_plans\']',
          'attributes' => 
          array (
            'startLine' => 41,
            'endLine' => 57,
            'startTokenPos' => 119,
            'startFilePos' => 1006,
            'endTokenPos' => 165,
            'endFilePos' => 1391,
          ),
        ),
        'docComment' => '/**
 * Tables that don\'t have academy_company_id but are linked via user_id.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      'export' => 
      array (
        'name' => 'export',
        'parameters' => 
        array (
          'companyId' => 
          array (
            'name' => 'companyId',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 59,
            'endLine' => 59,
            'startColumn' => 28,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 59,
        'endLine' => 109,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\TenantBackupService',
        'implementingClassName' => 'App\\Services\\TenantBackupService',
        'currentClassName' => 'App\\Services\\TenantBackupService',
        'aliasName' => NULL,
      ),
      'restore' => 
      array (
        'name' => 'restore',
        'parameters' => 
        array (
          'companyId' => 
          array (
            'name' => 'companyId',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 111,
            'endLine' => 111,
            'startColumn' => 29,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'filePath' => 
          array (
            'name' => 'filePath',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 111,
            'endLine' => 111,
            'startColumn' => 45,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 111,
        'endLine' => 169,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\TenantBackupService',
        'implementingClassName' => 'App\\Services\\TenantBackupService',
        'currentClassName' => 'App\\Services\\TenantBackupService',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));