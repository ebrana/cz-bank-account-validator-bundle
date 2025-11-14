<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ebrana\CzBankAccountValidatorBundle\Provider\BankCodesProvider;
use Ebrana\CzBankAccountValidatorBundle\Service\BankAccountNumberValidator;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('ebrana.bank_codes_provider', BankCodesProvider::class)
        ->alias(BankCodesProvider::class, 'ebrana.bank_codes_provider')

        ->set('ebrana.cz_bank_validator', BankAccountNumberValidator::class)
            ->args([
                service('ebrana.bank_codes_provider'),
            ])
        ->alias(BankAccountNumberValidator::class, 'ebrana.cz_bank_validator')
    ;
};
