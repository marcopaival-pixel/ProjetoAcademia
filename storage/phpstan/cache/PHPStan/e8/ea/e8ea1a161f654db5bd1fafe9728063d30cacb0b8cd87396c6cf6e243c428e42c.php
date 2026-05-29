<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Models\PdfGenerationLog.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\PdfGenerationLog
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-bb283be9cdcb6be4847e6b386c87c471b60ce0ef9e5c669dd916897b09024f1a',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\PdfGenerationLog',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Models/PdfGenerationLog.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\PdfGenerationLog',
    'shortName' => 'PdfGenerationLog',
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
    'endLine' => 51,
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
      'ACTION_PREVIEW' => 
      array (
        'declaringClassName' => 'App\\Models\\PdfGenerationLog',
        'implementingClassName' => 'App\\Models\\PdfGenerationLog',
        'name' => 'ACTION_PREVIEW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'preview\'',
          'attributes' => 
          array (
            'startLine' => 10,
            'endLine' => 10,
            'startTokenPos' => 35,
            'startFilePos' => 198,
            'endTokenPos' => 35,
            'endFilePos' => 206,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 10,
        'endLine' => 10,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'ACTION_DOWNLOAD' => 
      array (
        'declaringClassName' => 'App\\Models\\PdfGenerationLog',
        'implementingClassName' => 'App\\Models\\PdfGenerationLog',
        'name' => 'ACTION_DOWNLOAD',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'download\'',
          'attributes' => 
          array (
            'startLine' => 12,
            'endLine' => 12,
            'startTokenPos' => 46,
            'startFilePos' => 245,
            'endTokenPos' => 46,
            'endFilePos' => 254,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 12,
        'endLine' => 12,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\PdfGenerationLog',
        'implementingClassName' => 'App\\Models\\PdfGenerationLog',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'user_id\', \'pdf_template_id\', \'historico_pdf_id\', \'document_type\', \'template_name\', \'action\', \'filename\', \'status\', \'error_message\', \'ip_address\', \'user_agent\']',
          'attributes' => 
          array (
            'startLine' => 14,
            'endLine' => 26,
            'startTokenPos' => 55,
            'startFilePos' => 284,
            'endTokenPos' => 90,
            'endFilePos' => 539,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 14,
        'endLine' => 26,
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
 * @return BelongsTo<User, PdfGenerationLog>
 */',
        'startLine' => 31,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PdfGenerationLog',
        'implementingClassName' => 'App\\Models\\PdfGenerationLog',
        'currentClassName' => 'App\\Models\\PdfGenerationLog',
        'aliasName' => NULL,
      ),
      'template' => 
      array (
        'name' => 'template',
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
 * @return BelongsTo<PdfTemplate, PdfGenerationLog>
 */',
        'startLine' => 39,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PdfGenerationLog',
        'implementingClassName' => 'App\\Models\\PdfGenerationLog',
        'currentClassName' => 'App\\Models\\PdfGenerationLog',
        'aliasName' => NULL,
      ),
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
 * @return BelongsTo<HistoricoPdf, PdfGenerationLog>
 */',
        'startLine' => 47,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PdfGenerationLog',
        'implementingClassName' => 'App\\Models\\PdfGenerationLog',
        'currentClassName' => 'App\\Models\\PdfGenerationLog',
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