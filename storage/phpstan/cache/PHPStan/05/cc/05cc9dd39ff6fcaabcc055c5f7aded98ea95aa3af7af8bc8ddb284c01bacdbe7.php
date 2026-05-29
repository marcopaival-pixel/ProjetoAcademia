<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Models\MercadoPagoSubscription.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\MercadoPagoSubscription
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-72a0f841465284c8b44381ab4c7a81f9f23b0268dc45e386e9560097824fd64e',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\MercadoPagoSubscription',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Models/MercadoPagoSubscription.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\MercadoPagoSubscription',
    'shortName' => 'MercadoPagoSubscription',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Representa uma assinatura recorrente do Mercado Pago.
 * Tabela correspondente: mercadopago_subscriptions
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 12,
    'endLine' => 46,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'App\\Models\\Traits\\BelongsToCompany',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'companyColumn' => 
      array (
        'declaringClassName' => 'App\\Models\\MercadoPagoSubscription',
        'implementingClassName' => 'App\\Models\\MercadoPagoSubscription',
        'name' => 'companyColumn',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'user_id\'',
          'attributes' => 
          array (
            'startLine' => 15,
            'endLine' => 15,
            'startTokenPos' => 40,
            'startFilePos' => 352,
            'endTokenPos' => 40,
            'endFilePos' => 360,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 15,
        'endLine' => 15,
        'startColumn' => 5,
        'endColumn' => 41,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\MercadoPagoSubscription',
        'implementingClassName' => 'App\\Models\\MercadoPagoSubscription',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'mercadopago_subscriptions\'',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 16,
            'startTokenPos' => 49,
            'startFilePos' => 386,
            'endTokenPos' => 49,
            'endFilePos' => 412,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 51,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'primaryKey' => 
      array (
        'declaringClassName' => 'App\\Models\\MercadoPagoSubscription',
        'implementingClassName' => 'App\\Models\\MercadoPagoSubscription',
        'name' => 'primaryKey',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'mp_preapproval_id\'',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 19,
            'startTokenPos' => 60,
            'startFilePos' => 514,
            'endTokenPos' => 60,
            'endFilePos' => 532,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 48,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'incrementing' => 
      array (
        'declaringClassName' => 'App\\Models\\MercadoPagoSubscription',
        'implementingClassName' => 'App\\Models\\MercadoPagoSubscription',
        'name' => 'incrementing',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 20,
            'endLine' => 20,
            'startTokenPos' => 69,
            'startFilePos' => 562,
            'endTokenPos' => 69,
            'endFilePos' => 566,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'keyType' => 
      array (
        'declaringClassName' => 'App\\Models\\MercadoPagoSubscription',
        'implementingClassName' => 'App\\Models\\MercadoPagoSubscription',
        'name' => 'keyType',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'string\'',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 78,
            'startFilePos' => 594,
            'endTokenPos' => 78,
            'endFilePos' => 601,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\MercadoPagoSubscription',
        'implementingClassName' => 'App\\Models\\MercadoPagoSubscription',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'mp_preapproval_id\', \'user_id\', \'plan_code\', \'status\', \'coupon_id\']',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 29,
            'startTokenPos' => 87,
            'startFilePos' => 631,
            'endTokenPos' => 104,
            'endFilePos' => 745,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 29,
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
      'user' => 
      array (
        'name' => 'user',
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
 * Relacionamento com o usuário dono da assinatura.
 */',
        'startLine' => 34,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\MercadoPagoSubscription',
        'implementingClassName' => 'App\\Models\\MercadoPagoSubscription',
        'currentClassName' => 'App\\Models\\MercadoPagoSubscription',
        'aliasName' => NULL,
      ),
      'coupon' => 
      array (
        'name' => 'coupon',
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
 * Relacionamento opcional com um cupom aplicado.
 */',
        'startLine' => 42,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\MercadoPagoSubscription',
        'implementingClassName' => 'App\\Models\\MercadoPagoSubscription',
        'currentClassName' => 'App\\Models\\MercadoPagoSubscription',
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