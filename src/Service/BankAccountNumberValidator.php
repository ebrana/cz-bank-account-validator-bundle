<?php

declare(strict_types=1);

namespace Ebrana\CzBankAccountValidatorBundle\Service;

use Ebrana\CzBankAccountValidatorBundle\Exception\Validator\BankAccountNumber\InvalidAccountNumberChecksumException;
use Ebrana\CzBankAccountValidatorBundle\Exception\Validator\BankAccountNumber\InvalidAccountPrefixChecksumException;
use Ebrana\CzBankAccountValidatorBundle\Exception\Validator\BankAccountNumber\InvalidFormatException;
use Ebrana\CzBankAccountValidatorBundle\Exception\Validator\BankAccountNumber\MissingBankCodeException;
use Ebrana\CzBankAccountValidatorBundle\Provider\BankCodesProvider;

final readonly class BankAccountNumberValidator
{
    public function __construct(
        private BankCodesProvider $bankCodesProvider,
    ) {
    }

    public function validate(string $number): bool
    {
        // Váhy pro kontrolu prefixu
        $prefixWeights = [10, 5, 8, 4, 2, 1];

        // Váhy pro kontrolu základní části čísla
        $baseWeights = [6, 3, 7, 9, 10, 5, 8, 4, 2, 1];

        // Kontrola formátu.
        if (!preg_match('/^(([0-9]{0,6})-)?([0-9]{2,10})\/([0-9]{4})$/', $number)) {
            throw new InvalidFormatException(
                'Číslo účtu je ve špatném formátu! Pokud je vč. prefixu, tak musí být oddělený pomlčkou a bez mezer! Např. 11-001111111/0300.'
            );
        }

        $parts = explode('/', $number);
        $accountNumber = $parts[0];
        $bankCode = $parts[1];

        $numberParts = explode('-', $accountNumber);
        $prefix = null;
        if (2 === count($numberParts)) {
            $prefix = $numberParts[0];
            $accountNumber = $numberParts[1];
        }

        // Kontrola prefixu
        if (null !== $prefix) {
            // Doplnění na 6 číslic nulami zleva
            $prefixParts = str_pad($prefix, 6, '0', STR_PAD_LEFT);
            // Suma všech čísel pronásobených jejich váhami
            $sum = 0;
            for ($i = 0; $i < 6; $i++) {
                $sum += (int) $prefixParts[$i] * $prefixWeights[$i];
            }

            // Kontrola na dělitelnost 11
            if (0 !== $sum % 11) {
                throw new InvalidAccountPrefixChecksumException('Číslo účtu nemá validní prefix!');
            }
        }

        // Doplnění na 10 číslic nulami zleva
        $base = str_pad($accountNumber, 10, '0', STR_PAD_LEFT);

        // Suma všech číslic pronásobených jejich vahami
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $base[$i] * $baseWeights[$i];
        }

        // Kontrola na dělitelnost 11
        if (0 !== $sum % 11) {
            throw new InvalidAccountNumberChecksumException('Číslo účtu není validní!');
        }

        // Kontrola bankovního čísla
        if (null === $this->bankCodesProvider->getBankName($bankCode)) {
            throw new MissingBankCodeException(sprintf('Banku s kódem "%s" nemáme v evidenci!', $bankCode));
        }

        return true;
    }
}
