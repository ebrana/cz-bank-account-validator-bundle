<?php

declare(strict_types=1);

namespace Ebrana\CzBankAccountValidatorBundle\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS)]
final class AccountNumberValid extends Constraint
{
    public function __construct(
        public string $numberPath = 'accountNumber',
        public string $bankCodePath = 'bankCode',
        public string $missingNumberMessage = 'Vyplňte číslo účtu!',
        public string $missingBankCodeMessage = 'Vyberte kód banky!',
        public ?string $invalidFormatMessage = null,
        public ?string $invalidChecksumMessage = null,
        public ?string $nonExistingBankCodeMessage = null,
        mixed $options = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct($options, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return AccountNumberValidator::class;
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
