<?php

declare(strict_types=1);

namespace Ebrana\CzBankAccountValidatorBundle\Validator;

use Ebrana\CzBankAccountValidatorBundle\Exception\Validator\BankAccountNumber\InvalidAccountNumberChecksumException;
use Ebrana\CzBankAccountValidatorBundle\Exception\Validator\BankAccountNumber\InvalidAccountPrefixChecksumException;
use Ebrana\CzBankAccountValidatorBundle\Exception\Validator\BankAccountNumber\InvalidFormatException;
use Ebrana\CzBankAccountValidatorBundle\Exception\Validator\BankAccountNumber\MissingBankCodeException;
use Ebrana\CzBankAccountValidatorBundle\Service\BankAccountNumberValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class AccountNumberValidator extends ConstraintValidator
{
    public function __construct(
        private BankAccountNumberValidator $validator,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        assert($constraint instanceof AccountNumberValid);
        if (!is_string($value) && !is_null($value)) {
            throw new \InvalidArgumentException('Constraint validates only over strings!');
        }

        if (null === $value) {
            return;
        }

        try {
            $this->validator->validate($value);
        } catch (InvalidFormatException|InvalidAccountNumberChecksumException $e) {
            $message = $e instanceof InvalidFormatException
                ? $constraint->invalidFormatMessage
                : $constraint->invalidChecksumMessage
            ;
            $this->context
                ->buildViolation($message ?? $e->getMessage())
                ->atPath($constraint->numberPath)
                ->addViolation()
            ;
        } catch (InvalidAccountPrefixChecksumException $e) {
            $this->context
                ->buildViolation($constraint->invalidPrefixChecksumMessage ?? $e->getMessage())
                ->atPath($constraint->prefixPath)
                ->addViolation()
            ;
        } catch (MissingBankCodeException $e) {
            $this->context
                ->buildViolation($constraint->nonExistingBankCodeMessage ?? $e->getMessage())
                ->atPath($constraint->bankCodePath)
                ->addViolation()
            ;
        }
    }
}
