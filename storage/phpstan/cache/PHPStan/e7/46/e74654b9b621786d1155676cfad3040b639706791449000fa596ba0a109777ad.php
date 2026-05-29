<?php declare(strict_types = 1);

// osfsl-C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../endroid/qr-code/src/Writer/PngWriter.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Endroid\QrCode\Writer\PngWriter
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-deea79d93147ed58f1fade4c5a752ddd9b0115aefeb91831f078de0b822a42c6-8.2.12-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Endroid\\QrCode\\Writer\\PngWriter',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../endroid/qr-code/src/Writer/PngWriter.php',
      ),
    ),
    'namespace' => 'Endroid\\QrCode\\Writer',
    'name' => 'Endroid\\QrCode\\Writer\\PngWriter',
    'shortName' => 'PngWriter',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 29,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Endroid\\QrCode\\Writer\\AbstractGdWriter',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'WRITER_OPTION_COMPRESSION_LEVEL' => 
      array (
        'declaringClassName' => 'Endroid\\QrCode\\Writer\\PngWriter',
        'implementingClassName' => 'Endroid\\QrCode\\Writer\\PngWriter',
        'name' => 'WRITER_OPTION_COMPRESSION_LEVEL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'compression_level\'',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 16,
            'startTokenPos' => 65,
            'startFilePos' => 421,
            'endTokenPos' => 65,
            'endFilePos' => 439,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 71,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'write' => 
      array (
        'name' => 'write',
        'parameters' => 
        array (
          'qrCode' => 
          array (
            'name' => 'qrCode',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Endroid\\QrCode\\QrCodeInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 27,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'logo' => 
          array (
            'name' => 'logo',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 18,
                'endLine' => 18,
                'startTokenPos' => 85,
                'startFilePos' => 516,
                'endTokenPos' => 85,
                'endFilePos' => 519,
              ),
            ),
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
                      'name' => 'Endroid\\QrCode\\Logo\\LogoInterface',
                      'isIdentifier' => false,
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
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 52,
            'endColumn' => 77,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'label' => 
          array (
            'name' => 'label',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 18,
                'endLine' => 18,
                'startTokenPos' => 94,
                'startFilePos' => 546,
                'endTokenPos' => 94,
                'endFilePos' => 549,
              ),
            ),
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
                      'name' => 'Endroid\\QrCode\\Label\\LabelInterface',
                      'isIdentifier' => false,
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
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 80,
            'endColumn' => 107,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 18,
                'endLine' => 18,
                'startTokenPos' => 103,
                'startFilePos' => 569,
                'endTokenPos' => 104,
                'endFilePos' => 570,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 110,
            'endColumn' => 128,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Endroid\\QrCode\\Writer\\Result\\ResultInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 18,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Endroid\\QrCode\\Writer',
        'declaringClassName' => 'Endroid\\QrCode\\Writer\\PngWriter',
        'implementingClassName' => 'Endroid\\QrCode\\Writer\\PngWriter',
        'currentClassName' => 'Endroid\\QrCode\\Writer\\PngWriter',
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