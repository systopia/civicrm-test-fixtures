<?php

declare(strict_types = 1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\Property\RemoveUselessVarTagRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeFromPropertyTypeRector;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()->withPaths([
  __DIR__ . '/src',
  __DIR__ . '/tests',
])->withSkip([
  ClassPropertyAssignToConstructorPromotionRector::class => NULL,
  AddVoidReturnTypeWhereNoReturnRector::class => NULL,
  ReadOnlyPropertyRector::class => NULL,
  RemoveUselessReturnTagRector::class => NULL,
  RemoveUselessVarTagRector::class => NULL,
  RemoveUselessParamTagRector::class => NULL,
  AddParamTypeFromPropertyTypeRector::class => [
    __DIR__ . '/src/Core/Adapters/*',
  ],
])->withSets([
  SetList::CODE_QUALITY,
  SetList::DEAD_CODE,
  SetList::TYPE_DECLARATION,
  SetList::EARLY_RETURN,
])->withPhpVersion(PhpVersion::PHP_81)->withPhpSets(php81: TRUE);
