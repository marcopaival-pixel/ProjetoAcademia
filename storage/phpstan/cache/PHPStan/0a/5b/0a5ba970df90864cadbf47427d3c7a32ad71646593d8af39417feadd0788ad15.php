<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Models\MarketingBanner.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\MarketingBanner
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-727e8c6680ba14717cc9c9caac45faf00bd0d97155ead6ef8b94d102f5f68067',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\MarketingBanner',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Models/MarketingBanner.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\MarketingBanner',
    'shortName' => 'MarketingBanner',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 10,
    'endLine' => 85,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\MarketingBanner',
        'implementingClassName' => 'App\\Models\\MarketingBanner',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'title\', \'subtitle\', \'description\', \'image_desktop\', \'image_mobile\', \'background_color\', \'icon\', \'primary_button_text\', \'primary_button_link\', \'secondary_button_text\', \'secondary_button_link\', \'start_date\', \'end_date\', \'priority\', \'is_active\', \'allow_dismiss\', \'dont_show_again_option\', \'display_type\', \'frequency_days\', \'segmentation\']',
          'attributes' => 
          array (
            'startLine' => 14,
            'endLine' => 35,
            'startTokenPos' => 48,
            'startFilePos' => 313,
            'endTokenPos' => 110,
            'endFilePos' => 816,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 14,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'casts' => 
      array (
        'declaringClassName' => 'App\\Models\\MarketingBanner',
        'implementingClassName' => 'App\\Models\\MarketingBanner',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'start_date\' => \'datetime\', \'end_date\' => \'datetime\', \'is_active\' => \'boolean\', \'allow_dismiss\' => \'boolean\', \'dont_show_again_option\' => \'boolean\', \'segmentation\' => \'array\', \'priority\' => \'integer\', \'frequency_days\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 37,
            'endLine' => 46,
            'startTokenPos' => 119,
            'startFilePos' => 843,
            'endTokenPos' => 177,
            'endFilePos' => 1145,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 46,
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
      'targets' => 
      array (
        'name' => 'targets',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 48,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\MarketingBanner',
        'implementingClassName' => 'App\\Models\\MarketingBanner',
        'currentClassName' => 'App\\Models\\MarketingBanner',
        'aliasName' => NULL,
      ),
      'roles' => 
      array (
        'name' => 'roles',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 53,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\MarketingBanner',
        'implementingClassName' => 'App\\Models\\MarketingBanner',
        'currentClassName' => 'App\\Models\\MarketingBanner',
        'aliasName' => NULL,
      ),
      'views' => 
      array (
        'name' => 'views',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 58,
        'endLine' => 61,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\MarketingBanner',
        'implementingClassName' => 'App\\Models\\MarketingBanner',
        'currentClassName' => 'App\\Models\\MarketingBanner',
        'aliasName' => NULL,
      ),
      'clicks' => 
      array (
        'name' => 'clicks',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 63,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\MarketingBanner',
        'implementingClassName' => 'App\\Models\\MarketingBanner',
        'currentClassName' => 'App\\Models\\MarketingBanner',
        'aliasName' => NULL,
      ),
      'dismissals' => 
      array (
        'name' => 'dismissals',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 68,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\MarketingBanner',
        'implementingClassName' => 'App\\Models\\MarketingBanner',
        'currentClassName' => 'App\\Models\\MarketingBanner',
        'aliasName' => NULL,
      ),
      'scopeActive' => 
      array (
        'name' => 'scopeActive',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 73,
            'endLine' => 73,
            'startColumn' => 33,
            'endColumn' => 38,
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
        'startLine' => 73,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\MarketingBanner',
        'implementingClassName' => 'App\\Models\\MarketingBanner',
        'currentClassName' => 'App\\Models\\MarketingBanner',
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