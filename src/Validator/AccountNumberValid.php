<?php

declare(strict_types=1);

namespace Ebrana\CzBankAccountValidatorBundle\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class AccountNumberValid extends Constraint
{
    public function __construct(
        public string $numberPath = 'accountNumber',
        public string $bankCodePath = 'accountNumber',
        public string $prefixPath = 'accountNumber',
        public ?string $invalidFormatMessage = null,
        public ?string $invalidChecksumMessage = null,
        public ?string $invalidPrefixChecksumMessage = null,
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

    /**
     * @return string[]
     */
    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }
}
