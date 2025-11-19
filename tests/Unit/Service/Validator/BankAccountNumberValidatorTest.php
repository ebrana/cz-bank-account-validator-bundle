<?php

declare(strict_types=1);

namespace Ebrana\CzBankAccountValidatorBundle\Tests\Unit\Service\Validator;

use Ebrana\CzBankAccountValidatorBundle\Exception\Validator\BankAccountNumber\InvalidAccountNumberChecksumException;
use Ebrana\CzBankAccountValidatorBundle\Exception\Validator\BankAccountNumber\InvalidAccountPrefixChecksumException;
use Ebrana\CzBankAccountValidatorBundle\Exception\Validator\BankAccountNumber\InvalidFormatException;
use Ebrana\CzBankAccountValidatorBundle\Exception\Validator\BankAccountNumber\MissingBankCodeException;
use Ebrana\CzBankAccountValidatorBundle\Provider\BankCodesProvider;
use Ebrana\CzBankAccountValidatorBundle\Service\BankAccountNumberValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BankAccountNumberValidatorTest extends TestCase
{
    private BankAccountNumberValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new BankAccountNumberValidator(new BankCodesProvider());
    }

    /**
     * @debug make test1 f=testValidAccount
     */
    #[DataProvider('provideValidBankAccounts')]
    public function testValidAccount(string $number): void
    {
        $this->expectNotToPerformAssertions();
        $this->validator->validate($number);
    }

    /**
     * @debug make test1 f=testInvalidAccountFormat
     */
    #[DataProvider('provideInvalidFormats')]
    public function testInvalidAccountFormat(string $number): void
    {
        $this->expectException(InvalidFormatException::class);
        $this->validator->validate($number);
    }

    /**
     * @debug make test1 f=testInvalidNumberChecksum
     */
    #[DataProvider('provideInvalidChecksumData')]
    public function testInvalidNumberChecksum(string $account): void
    {
        $this->expectException(InvalidAccountNumberChecksumException::class);
        $this->validator->validate($account);
    }

    /**
     * @debug make test1 f=testInvalidPrefixChecksum
     */
    #[DataProvider('provideInvalidPrefixChecksumData')]
    public function testInvalidPrefixChecksum(string $account): void
    {
        $this->expectException(InvalidAccountPrefixChecksumException::class);
        $this->validator->validate($account);
    }

    /**
     * @debug make test1 f=testInvalidBankCode
     */
    #[DataProvider('provideInvalidBankCodes')]
    public function testInvalidBankCode(string $number): void
    {
        $this->expectException(MissingBankCodeException::class);
        $this->validator->validate($number);
    }

    /**
     * @return array<array<string, string>>
     */
    public static function provideValidBankAccounts(): array
    {
        return [
            ['number' => '2171532/0800'],
            ['number' => '1265098001/5500'],
            ['number' => '188505042/0300'],
            ['number' => '35-3355550267/0100'],
        ];
    }

    /**
     * @return iterable<string, array<string, string>>
     */
    public static function provideInvalidFormats(): iterable
    {
        yield 'longPrefix' => ['number' => '7896542-122/0300'];
        yield 'longNumber' => ['number' => '12345678910/0300'];
        yield 'charInPrefix' => ['number' => '12a-123645/0300'];
        yield 'charInNumber' => ['number' => '1123a56/0300'];
        yield 'withDoubleBankCode' => ['number' => '188505042/0300/0300'];
        yield 'withDoublePrefix' => ['number' => '123-12-1234/0300'];
        yield 'withPrefixAndDoubleBankCode' => ['number' => '35-3355550267/0100/0300'];
        yield 'shortBankCode' => ['number' => '188505042/300'];
        yield 'longBankCode' => ['number' => '188505042/03030'];
        yield 'badBankCode' => ['number' => '188505042/x-12'];
    }

    /**
     * @return iterable<string, array<string, string>>
     */
    public static function provideInvalidChecksumData(): iterable
    {
        yield 'badBase' => ['account' => '35-3355550257/0300'];
    }

    /**
     * @return iterable<string, array<string, string>>
     */
    public static function provideInvalidPrefixChecksumData(): iterable
    {
        yield 'badPrefix' => ['account' => '335-3355550267/0300'];
        yield 'badBoth' => ['account' => '735-3355550268/0300'];
    }

    /**
     * @return iterable<string, array<string, string>>
     */
    public static function provideInvalidBankCodes(): iterable
    {
        yield 'nonExistingBank' => ['number' => '188505042/3333'];
    }
}
