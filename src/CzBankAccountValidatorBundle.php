<?php

declare(strict_types=1);

namespace Ebrana\CzBankAccountValidatorBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class CzBankAccountValidatorBundle extends AbstractBundle
{
    protected string $extensionAlias = 'ebrana_cz_bank_account_validator';

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');
    }
}
