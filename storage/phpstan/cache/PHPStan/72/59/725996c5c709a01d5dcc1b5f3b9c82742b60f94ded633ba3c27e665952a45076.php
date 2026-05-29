<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Services\MonthlyReportAggregator.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\MonthlyReportAggregator
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-c06e9b0cbbd7a4cd4a360cf439a162c0eabf3a1be2c59a37193981876257e3aa',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\MonthlyReportAggregator',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Services/MonthlyReportAggregator.php',
      ),
    ),
    'namespace' => 'App\\Services',
    'name' => 'App\\Services\\MonthlyReportAggregator',
    'shortName' => 'MonthlyReportAggregator',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 9,
    'endLine' => 228,
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
    ),
    'immediateMethods' => 
    array (
      'forUserMonth' => 
      array (
        'name' => 'forUserMonth',
        'parameters' => 
        array (
          'userId' => 
          array (
            'name' => 'userId',
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
            'startLine' => 37,
            'endLine' => 37,
            'startColumn' => 41,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'monthStart' => 
          array (
            'name' => 'monthStart',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Carbon\\CarbonInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 37,
            'endLine' => 37,
            'startColumn' => 54,
            'endColumn' => 80,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'monthEnd' => 
          array (
            'name' => 'monthEnd',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Carbon\\CarbonInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 37,
            'endLine' => 37,
            'startColumn' => 83,
            'endColumn' => 107,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
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
 * @return array{
 *     days: array<string, array{
 *         label: string, 
 *         kcal_in: int, 
 *         p: float, 
 *         c: float, 
 *         f: float, 
 *         ex_min: int, 
 *         ex_kcal: int, 
 *         water: int, 
 *         weight: float|null
 *     }>,
 *     avg_kcal: int,
 *     avg_p: float,
 *     avg_c: float,
 *     avg_f: float,
 *     days_with_food: int,
 *     total_ex_min: int,
 *     total_ex_kcal: int,
 *     total_water: int,
 *     delta_weight: float|null,
 *     first_weight: float|null,
 *     last_weight: float|null,
 * }
 */',
        'startLine' => 37,
        'endLine' => 227,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\MonthlyReportAggregator',
        'implementingClassName' => 'App\\Services\\MonthlyReportAggregator',
        'currentClassName' => 'App\\Services\\MonthlyReportAggregator',
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