<?php

declare(strict_types=1);

namespace Ebrana\CzBankAccountValidatorBundle\Validator;

use Ebrana\CzBankAccountValidatorBundle\Exception\Validator\BankAccountNumber\InvalidAccountNumberChecksumException;
use Ebrana\CzBankAccountValidatorBundle\Exception\Validator\BankAccountNumber\InvalidFormatException;
use Ebrana\CzBankAccountValidatorBundle\Exception\Validator\BankAccountNumber\MissingBankCodeException;
use Ebrana\CzBankAccountValidatorBundle\Model\AccountNumberInterface;
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
        if (!$value instanceof AccountNumberInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Constraint "%s" must be applied over instance of "%s"!',
                    AccountNumberValid::class,
                    AccountNumberInterface::class,
                ),
            );
        }

        if (null === $value->getAccountNumber() && null === $value->getBankCode()) {
            return;
        }

        if (null === $value->getAccountNumber()) {
            $this->context
                ->buildViolation($constraint->missingNumberMessage)
                ->atPath($constraint->numberPath)
                ->addViolation()
            ;

            return;
        }

        if (null === $value->getBankCode()) {
            $this->context
                ->buildViolation($constraint->missingBankCodeMessage)
                ->atPath($constraint->bankCodePath)
                ->addViolation()
            ;

            return;
        }

        try {
            $this->validator->validate($value->getAccountNumber(), $value->getBankCode());
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
        } catch (MissingBankCodeException $e) {
            $this->context
                ->buildViolation($constraint->nonExistingBankCodeMessage ?? $e->getMessage())
                ->atPath($constraint->bankCodePath)
                ->addViolation()
            ;
        }
    }
}
