<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Models\ConfiguracaoEmail.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\ConfiguracaoEmail
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-e50cd3bcbcd2ace4c4ab8e8fa57360ce23250e9aa4e19083fb1692bbc3257b84',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\ConfiguracaoEmail',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Models/ConfiguracaoEmail.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\ConfiguracaoEmail',
    'shortName' => 'ConfiguracaoEmail',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 9,
    'endLine' => 120,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
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
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\ConfiguracaoEmail',
        'implementingClassName' => 'App\\Models\\ConfiguracaoEmail',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'configuracao_email\'',
          'attributes' => 
          array (
            'startLine' => 11,
            'endLine' => 11,
            'startTokenPos' => 38,
            'startFilePos' => 226,
            'endTokenPos' => 38,
            'endFilePos' => 245,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 11,
        'endLine' => 11,
        'startColumn' => 5,
        'endColumn' => 44,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\ConfiguracaoEmail',
        'implementingClassName' => 'App\\Models\\ConfiguracaoEmail',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'empresa_id\', \'nome_provedor\', \'tipo_envio\', \'preset\', \'smtp_host\', \'smtp_porta\', \'smtp_usuario\', \'smtp_senha\', \'criptografia\', \'email_remetente\', \'nome_remetente\', \'timeout\', \'limite_envio_por_hora\', \'ativo\']',
          'attributes' => 
          array (
            'startLine' => 13,
            'endLine' => 28,
            'startTokenPos' => 47,
            'startFilePos' => 275,
            'endTokenPos' => 91,
            'endFilePos' => 603,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 13,
        'endLine' => 28,
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
      'casts' => 
      array (
        'name' => 'casts',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 30,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConfiguracaoEmail',
        'implementingClassName' => 'App\\Models\\ConfiguracaoEmail',
        'currentClassName' => 'App\\Models\\ConfiguracaoEmail',
        'aliasName' => NULL,
      ),
      'empresa' => 
      array (
        'name' => 'empresa',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return BelongsTo<AcademyCompany, ConfiguracaoEmail>
 */',
        'startLine' => 43,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConfiguracaoEmail',
        'implementingClassName' => 'App\\Models\\ConfiguracaoEmail',
        'currentClassName' => 'App\\Models\\ConfiguracaoEmail',
        'aliasName' => NULL,
      ),
      'setSmtpSenhaAttribute' => 
      array (
        'name' => 'setSmtpSenhaAttribute',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 48,
            'endLine' => 48,
            'startColumn' => 43,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 48,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConfiguracaoEmail',
        'implementingClassName' => 'App\\Models\\ConfiguracaoEmail',
        'currentClassName' => 'App\\Models\\ConfiguracaoEmail',
        'aliasName' => NULL,
      ),
      'getDecryptedPassword' => 
      array (
        'name' => 'getDecryptedPassword',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 56,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConfiguracaoEmail',
        'implementingClassName' => 'App\\Models\\ConfiguracaoEmail',
        'currentClassName' => 'App\\Models\\ConfiguracaoEmail',
        'aliasName' => NULL,
      ),
      'resolveMailSettings' => 
      array (
        'name' => 'resolveMailSettings',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Resolve host/porta/criptografia para envio SMTP (inclui tipo API mapeado para SMTP).
 *
 * @return array{
 *   host: string|null,
 *   port: int,
 *   encryption: string|null,
 *   username: string|null,
 *   password: string|null,
 *   from_address: string|null,
 *   from_name: string|null,
 *   timeout: int
 * }
 */',
        'startLine' => 83,
        'endLine' => 119,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConfiguracaoEmail',
        'implementingClassName' => 'App\\Models\\ConfiguracaoEmail',
        'currentClassName' => 'App\\Models\\ConfiguracaoEmail',
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