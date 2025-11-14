<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ebrana\CzBankAccountValidatorBundle\Provider\BankCodesProvider;
use Ebrana\CzBankAccountValidatorBundle\Service\BankAccountNumberValidator;
use Ebrana\CzBankAccountValidatorBundle\Validator\AccountNumberValidator;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('ebrana.bank_codes_provider', BankCodesProvider::class)
        ->alias(BankCodesProvider::class, 'ebrana.bank_codes_provider')

        ->set('ebrana.bank_account_number_validator', BankAccountNumberValidator::class)
            ->args([
                service('ebrana.bank_codes_provider'),
            ])
        ->alias(BankAccountNumberValidator::class, 'ebrana.bank_account_number_validator')

        ->set('ebrana.account_number_validator', AccountNumberValidator::class)
        ->args([
            service('ebrana.bank_account_number_validator'),
        ])
        ->tag('validator.constraint_validator')
        ->alias(AccountNumberValidator::class, 'ebrana.account_number_validator')
    ;
};
