<?php declare(strict_types = 1);

// osfsl-C:/Projetos/ProjetoAcademia/laravel-app/app/Models/Traits/HasPremiumAccess.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Traits\HasPremiumAccess
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-04a608f3f704937e7f6c33d1fe5ab4ae811c99a3b1a41494fa0a437f099d021d-8.2.12-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Traits\\HasPremiumAccess',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Models/Traits/HasPremiumAccess.php',
      ),
    ),
    'namespace' => 'App\\Models\\Traits',
    'name' => 'App\\Models\\Traits\\HasPremiumAccess',
    'shortName' => 'HasPremiumAccess',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 7,
    'endLine' => 87,
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
      'isPremiumCache' => 
      array (
        'declaringClassName' => 'App\\Models\\Traits\\HasPremiumAccess',
        'implementingClassName' => 'App\\Models\\Traits\\HasPremiumAccess',
        'name' => 'isPremiumCache',
        'modifiers' => 2,
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
                  'name' => 'bool',
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
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 12,
            'endLine' => 12,
            'startTokenPos' => 29,
            'startFilePos' => 225,
            'endTokenPos' => 29,
            'endFilePos' => 228,
          ),
        ),
        'docComment' => '/**
 * Cache estático para evitar múltiplas consultas ao BD na mesma requisição.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 12,
        'endLine' => 12,
        'startColumn' => 5,
        'endColumn' => 43,
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
      'isPremiumActive' => 
      array (
        'name' => 'isPremiumActive',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 14,
        'endLine' => 52,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Traits',
        'declaringClassName' => 'App\\Models\\Traits\\HasPremiumAccess',
        'implementingClassName' => 'App\\Models\\Traits\\HasPremiumAccess',
        'currentClassName' => 'App\\Models\\Traits\\HasPremiumAccess',
        'aliasName' => NULL,
      ),
      'hasPremiumAccess' => 
      array (
        'name' => 'hasPremiumAccess',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Acesso às funcionalidades reservadas ao Premium (export CSV, macros manuais, chat IA sem quota, etc.).
 * Administradores têm o mesmo acesso sem necessidade de assinatura.
 */',
        'startLine' => 58,
        'endLine' => 61,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Traits',
        'declaringClassName' => 'App\\Models\\Traits\\HasPremiumAccess',
        'implementingClassName' => 'App\\Models\\Traits\\HasPremiumAccess',
        'currentClassName' => 'App\\Models\\Traits\\HasPremiumAccess',
        'aliasName' => NULL,
      ),
      'activePlanType' => 
      array (
        'name' => 'activePlanType',
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
        'startLine' => 63,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Traits',
        'declaringClassName' => 'App\\Models\\Traits\\HasPremiumAccess',
        'implementingClassName' => 'App\\Models\\Traits\\HasPremiumAccess',
        'currentClassName' => 'App\\Models\\Traits\\HasPremiumAccess',
        'aliasName' => NULL,
      ),
      'hasPlanFeature' => 
      array (
        'name' => 'hasPlanFeature',
        'parameters' => 
        array (
          'feature' => 
          array (
            'name' => 'feature',
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
            'startLine' => 69,
            'endLine' => 69,
            'startColumn' => 36,
            'endColumn' => 50,
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
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 69,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Traits',
        'declaringClassName' => 'App\\Models\\Traits\\HasPremiumAccess',
        'implementingClassName' => 'App\\Models\\Traits\\HasPremiumAccess',
        'currentClassName' => 'App\\Models\\Traits\\HasPremiumAccess',
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