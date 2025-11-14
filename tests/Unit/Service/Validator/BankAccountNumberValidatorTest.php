<?php

declare(strict_types=1);

namespace Ebrana\CzBankAccountValidatorBundle\Tests\Unit\Service\Validator;

use Ebrana\CzBankAccountValidatorBundle\Exception\Validator\BankAccountNumber\InvalidAccountNumberChecksumException;
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
    public function testValidAccount(string $account, string $bank): void
    {
        $this->expectNotToPerformAssertions();
        $this->validator->validate($account, $bank);
    }

    /**
     * @debug make test1 f=testInvalidAccountFormat
     */
    #[DataProvider('provideInvalidFormats')]
    public function testInvalidAccountFormat(string $account): void
    {
        $this->expectException(InvalidFormatException::class);
        $this->validator->validate($account, 'Does not matter here');
    }

    /**
     * @debug make test1 f=testInvalidChecksum
     */
    #[DataProvider('provideInvalidChecksumData')]
    public function testInvalidChecksum(string $account): void
    {
        $this->expectException(InvalidAccountNumberChecksumException::class);
        $this->validator->validate($account, 'Does not matter here');
    }

    /**
     * @debug make test1 f=testInvalidBankCode
     */
    #[DataProvider('provideInvalidBankCodes')]
    public function testInvalidBankCode(string $bankCode): void
    {
        $this->expectException(MissingBankCodeException::class);
        $this->validator->validate('188505042', $bankCode);
    }

    /**
     * @return array<array<string, string>>
     */
    public static function provideValidBankAccounts(): array
    {
        return [
            [
                'account' => '2171532',
                'bank' => '0800',
            ],
            [
                'account' => '1265098001',
                'bank' => '5500',
            ],
            [
                'account' => '188505042',
                'bank' => '0300',
            ],
            [
                'account' => '35-3355550267',
                'bank' => '0100',
            ],
        ];
    }

    /**
     * @return iterable<string, array<string, string>>
     */
    public static function provideInvalidFormats(): iterable
    {
        yield 'longPrefix' => ['account' => '7896542-122'];
        yield 'longNumber' => ['account' => '12345678910'];
        yield 'charInPrefix' => ['account' => '12a-123645'];
        yield 'charInNumber' => ['account' => '1123a56'];
        yield 'withBankCode' => ['account' => '188505042/0300'];
        yield 'withDoublePrefix' => ['account' => '123-12-1234'];
        yield 'withPrefixAndBankCode' => ['account' => '35-3355550267/0100'];
    }

    /**
     * @return iterable<string, array<string, string>>
     */
    public static function provideInvalidChecksumData(): iterable
    {
        yield 'badPrefix' => ['account' => '335-3355550267'];
        yield 'badBase' => ['account' => '35-3355550257'];
        yield 'badBoth' => ['account' => '735-3355550268'];
    }

    /**
     * @return iterable<string, array<string, string>>
     */
    public static function provideInvalidBankCodes(): iterable
    {
        yield 'nonExistingBank' => ['bankCode' => '3333'];
        yield 'shortFormat' => ['bankCode' => '300'];
        yield 'longFormat' => ['bankCode' => '03030'];
        yield 'badFormat' => ['bankCode' => 'x-12'];
    }
}
