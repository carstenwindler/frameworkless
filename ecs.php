<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(SetList::CLEAN_CODE);
    $containerConfigurator->import(SetList::PSR_12);
    $containerConfigurator->import(SetList::STRICT);
    $containerConfigurator->import(SetList::SPACES);
    $containerConfigurator->import(SetList::COMMON);
    $containerConfigurator->import(SetList::COMMENTS);
    $containerConfigurator->import(SetList::CONTROL_STRUCTURES);
    $containerConfigurator->import(SetList::ARRAY);
    $containerConfigurator->import(SetList::NAMESPACES);
};
