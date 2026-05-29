<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Models\PdfSignatureAuditLog.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\PdfSignatureAuditLog
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-ff180a65f8cba8893e3a0890f67b537a289abd3e93a1fbc64e839347080eb68e',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\PdfSignatureAuditLog',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Models/PdfSignatureAuditLog.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\PdfSignatureAuditLog',
    'shortName' => 'PdfSignatureAuditLog',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 8,
    'endLine' => 33,
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
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\PdfSignatureAuditLog',
        'implementingClassName' => 'App\\Models\\PdfSignatureAuditLog',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'historico_pdf_id\', \'user_id\', \'evento\', \'detalhe\', \'ip_address\']',
          'attributes' => 
          array (
            'startLine' => 10,
            'endLine' => 16,
            'startTokenPos' => 33,
            'startFilePos' => 194,
            'endTokenPos' => 50,
            'endFilePos' => 306,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 10,
        'endLine' => 16,
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
      'historicoPdf' => 
      array (
        'name' => 'historicoPdf',
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
 * @return BelongsTo<HistoricoPdf, PdfSignatureAuditLog>
 */',
        'startLine' => 21,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PdfSignatureAuditLog',
        'implementingClassName' => 'App\\Models\\PdfSignatureAuditLog',
        'currentClassName' => 'App\\Models\\PdfSignatureAuditLog',
        'aliasName' => NULL,
      ),
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
 * @return BelongsTo<User, PdfSignatureAuditLog>
 */',
        'startLine' => 29,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PdfSignatureAuditLog',
        'implementingClassName' => 'App\\Models\\PdfSignatureAuditLog',
        'currentClassName' => 'App\\Models\\PdfSignatureAuditLog',
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