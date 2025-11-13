<?php

declare(strict_types=1);

namespace Ebrana\CzBankAccountValidatorBundle\Provider;

final readonly class BankCodesProvider
{
    public function getBankName(string $code): ?string
    {
        $codes = $this->provideBankCodes();

        return $codes[$code] ?? null;
    }

    /**
     * update bank codes from https://www.cnb.cz/cs/platebni-styk/ucty-kody-bank/
     *
     * @return iterable<string, string>
     */
    public function provideBankCodes(): iterable
    {
        return [
            '0100' => 'Komerční banka, a.s.',
            '0300' => 'Československá obchodní banka, a. s.',
            '0600' => 'MONETA Money Bank, a.s.',
            '0710' => 'ČESKÁ NÁRODNÍ BANKA',
            '0800' => 'Česká spořitelna, a.s.',
            '2010' => 'Fio banka, a.s.',
            '2060' => 'Citfin, spořitelní družstvo',
            '2070' => 'TRINITY BANK a.s.',
            '2100' => 'Hypoteční banka, a.s.',
            '2200' => 'Peněžní dům, spořitelní družstvo',
            '2220' => 'Artesa, spořitelní družstvo',
            '2250' => 'Banka CREDITAS a.s.',
            '2260' => 'NEY spořitelní družstvo',
            '2275' => 'Podnikatelská družstevní záložna',
            '2600' => 'Citibank Europe plc, organizační složka',
            '2700' => 'UniCredit Bank Czech Republic and Slovakia, a.s.',
            '3030' => 'Air Bank a.s.',
            '3050' => 'BNP Paribas Personal Finance SA, odštěpný závod',
            '3060' => 'PKO BP S.A., Czech Branch',
            '3500' => 'ING Bank N.V.',
            '4000' => 'Max banka a.s.',
            '4300' => 'Národní rozvojová banka, a.s.',
            '5500' => 'Raiffeisenbank a.s.',
            '5800' => 'J&T BANKA, a.s.',
            '6000' => 'PPF banka a.s.',
            '6100' => 'Raiffeisenbank a.s.', // Do 31. 12. 2021 Equa Bank a.s.
            '6200' => 'COMMERZBANK Aktiengesellschaft, pobočka Praha',
            '6210' => 'mBank S.A., organizační složka',
            '6300' => 'BNP Paribas S.A., pobočka Česká republika',
            '6700' => 'Všeobecná úverová banka a.s., pobočka Praha',
            '6800' => 'Sberbank CZ, a.s. v likvidaci',
            '7910' => 'Deutsche Bank Aktiengesellschaft Filiale Prag, organizační složka',
            '7950' => 'Raiffeisen stavební spořitelna a.s.',
            '7960' => 'ČSOB Stavební spořitelna, a.s.',
            '7970' => 'MONETA Stavební Spořitelna, a.s.',
            '7990' => 'Modrá pyramida stavební spořitelna, a.s.',
            '8030' => 'Volksbank Raiffeisenbank Nordoberpfalz eG pobočka Cheb',
            '8040' => 'Oberbank AG pobočka Česká republika',
            '8060' => 'Stavební spořitelna České spořitelny, a.s.',
            '8090' => 'Česká exportní banka, a.s.',
            '8150' => 'HSBC Continental Europe, Czech Republic',
            '8190' => 'Sparkasse Oberlausitz-Niederschlesien',
            '8198' => 'FAS finance company s.r.o.',
            '8199' => 'MoneyPolo Europe s.r.o.',
            '8200' => 'PRIVAT BANK der Raiffeisenlandesbank Oberösterreich Aktiengesellschaft, pobočka Česká republika',
            '8220' => 'Payment execution s.r.o.',
            '8230' => 'ABAPAY s.r.o.',
            '8240' => 'Družstevní záložna Kredit, v likvidaci',
            '8250' => 'Bank of China (CEE) Ltd. Prague Branch',
            '8255' => 'Bank of Communications Co., Ltd., Prague Branch odštěpný závod',
            '8265' => 'Industrial and Commercial Bank of China Limited, Prague Branch, odštěpný závod',
            '8270' => 'Fairplay Pay s.r.o.',
            '8280' => 'B-Efekt a.s.',
            '8293' => 'Mercurius partners s.r.o.',
            '8299' => 'BESTPAY s.r.o.',
            '8500' => 'Multitude Bank p.l.c.',
        ];
    }
}
