<?php


namespace App\Helpers;


use Stripe\StripeClient;

class CurrencyHelper extends StripeClient
{
    /**
     * @param null $code
     * @param null $key
     * @return string|string[]|\string[][]|null
     */
    public static function get($code = null, $key = null)
    {
        $list = [
            "AFA" => ["code" => "AFA", "name" => "Afghan Afghani", "symbol" => "؋"],
            "ALL" => ["code" => "ALL", "name" => "Albanian Lek", "symbol" => "Lek"],
            "DZD" => ["code" => "DZD", "name" => "Algerian Dinar", "symbol" => "دج"],
            "AOA" => ["code" => "AOA", "name" => "Angolan Kwanza", "symbol" => "Kz"],
            "ARS" => ["code" => "ARS", "name" => "Argentine Peso", "symbol" => "$"],
            "AMD" => ["code" => "AMD", "name" => "Armenian Dram", "symbol" => "֏"],
            "AWG" => ["code" => "AWG", "name" => "Aruban Florin", "symbol" => "ƒ"],
            "AUD" => ["code" => "AUD", "name" => "Australian Dollar", "symbol" => "$"],
            "AZN" => ["code" => "AZN", "name" => "Azerbaijani Manat", "symbol" => "m"],
            "BSD" => ["code" => "BSD", "name" => "Bahamian Dollar", "symbol" => "B$"],
            "BHD" => ["code" => "BHD", "name" => "Bahraini Dinar", "symbol" => ".د.ب"],
            "BDT" => ["code" => "BDT", "name" => "Bangladeshi Taka", "symbol" => "৳"],
            "BBD" => ["code" => "BBD", "name" => "Barbadian Dollar", "symbol" => "Bds$"],
            "BYR" => ["code" => "BYR", "name" => "Belarusian Ruble", "symbol" => "Br"],
            "BEF" => ["code" => "BEF", "name" => "Belgian Franc", "symbol" => "fr"],
            "BZD" => ["code" => "BZD", "name" => "Belize Dollar", "symbol" => "$"],
            "BMD" => ["code" => "BMD", "name" => "Bermudan Dollar", "symbol" => "$"],
            "BTN" => ["code" => "BTN", "name" => "Bhutanese Ngultrum", "symbol" => "Nu."],
            "BTC" => ["code" => "BTC", "name" => "Bitcoin", "symbol" => "฿"],
            "BOB" => ["code" => "BOB", "name" => "Bolivian Boliviano", "symbol" => "Bs."],
            "BAM" => ["code" => "BAM", "name" => "Bosnia", "symbol" => "KM"],
            "BWP" => ["code" => "BWP", "name" => "Botswanan Pula", "symbol" => "P"],
            "BRL" => ["code" => "BRL", "name" => "Brazilian Real", "symbol" => "R$"],
            "GBP" => ["code" => "GBP", "name" => "British Pound Sterling", "symbol" => "£"],
            "BND" => ["code" => "BND", "name" => "Brunei Dollar", "symbol" => "B$"],
            "BGN" => ["code" => "BGN", "name" => "Bulgarian Lev", "symbol" => "Лв."],
            "BIF" => ["code" => "BIF", "name" => "Burundian Franc", "symbol" => "FBu"],
            "KHR" => ["code" => "KHR", "name" => "Cambodian Riel", "symbol" => "KHR"],
            "CAD" => ["code" => "CAD", "name" => "Canadian Dollar", "symbol" => "$"],
            "CVE" => ["code" => "CVE", "name" => "Cape Verdean Escudo", "symbol" => "$"],
            "KYD" => ["code" => "KYD", "name" => "Cayman Islands Dollar", "symbol" => "$"],
            "XOF" => ["code" => "XOF", "name" => "CFA Franc BCEAO", "symbol" => "CFA"],
            "XAF" => ["code" => "XAF", "name" => "CFA Franc BEAC", "symbol" => "FCFA"],
            "XPF" => ["code" => "XPF", "name" => "CFP Franc", "symbol" => "₣"],
            "CLP" => ["code" => "CLP", "name" => "Chilean Peso", "symbol" => "$"],
            "CNY" => ["code" => "CNY", "name" => "Chinese Yuan", "symbol" => "¥"],
            "COP" => ["code" => "COP", "name" => "Colombian Peso", "symbol" => "$"],
            "KMF" => ["code" => "KMF", "name" => "Comorian Franc", "symbol" => "CF"],
            "CDF" => ["code" => "CDF", "name" => "Congolese Franc", "symbol" => "FC"],
            "CRC" => ["code" => "CRC", "name" => "Costa Rican ColÃ³n", "symbol" => "₡"],
            "HRK" => ["code" => "HRK", "name" => "Croatian Kuna", "symbol" => "kn"],
            "CUC" => ["code" => "CUC", "name" => "Cuban Convertible Peso", "symbol" => "$, CUC"],
            "CZK" => ["code" => "CZK", "name" => "Czech Republic Koruna", "symbol" => "Kč"],
            "DKK" => ["code" => "DKK", "name" => "Danish Krone", "symbol" => "Kr."],
            "DJF" => ["code" => "DJF", "name" => "Djiboutian Franc", "symbol" => "Fdj"],
            "DOP" => ["code" => "DOP", "name" => "Dominican Peso", "symbol" => "$"],
            "XCD" => ["code" => "XCD", "name" => "East Caribbean Dollar", "symbol" => "$"],
            "EGP" => ["code" => "EGP", "name" => "Egyptian Pound", "symbol" => "ج.م"],
            "ERN" => ["code" => "ERN", "name" => "Eritrean Nakfa", "symbol" => "Nfk"],
            "EEK" => ["code" => "EEK", "name" => "Estonian Kroon", "symbol" => "kr"],
            "ETB" => ["code" => "ETB", "name" => "Ethiopian Birr", "symbol" => "Nkf"],
            "EUR" => ["code" => "EUR", "name" => "Euro", "symbol" => "€"],
            "FKP" => ["code" => "FKP", "name" => "Falkland Islands Pound", "symbol" => "£"],
            "FJD" => ["code" => "FJD", "name" => "Fijian Dollar", "symbol" => "FJ$"],
            "GMD" => ["code" => "GMD", "name" => "Gambian Dalasi", "symbol" => "D"],
            "GEL" => ["code" => "GEL", "name" => "Georgian Lari", "symbol" => "ლ"],
            "DEM" => ["code" => "DEM", "name" => "German Mark", "symbol" => "DM"],
            "GHS" => ["code" => "GHS", "name" => "Ghanaian Cedi", "symbol" => "GH₵"],
            "GIP" => ["code" => "GIP", "name" => "Gibraltar Pound", "symbol" => "£"],
            "GRD" => ["code" => "GRD", "name" => "Greek Drachma", "symbol" => "₯, Δρχ, Δρ"],
            "GTQ" => ["code" => "GTQ", "name" => "Guatemalan Quetzal", "symbol" => "Q"],
            "GNF" => ["code" => "GNF", "name" => "Guinean Franc", "symbol" => "FG"],
            "GYD" => ["code" => "GYD", "name" => "Guyanaese Dollar", "symbol" => "$"],
            "HTG" => ["code" => "HTG", "name" => "Haitian Gourde", "symbol" => "G"],
            "HNL" => ["code" => "HNL", "name" => "Honduran Lempira", "symbol" => "L"],
            "HKD" => ["code" => "HKD", "name" => "Hong Kong Dollar", "symbol" => "$"],
            "HUF" => ["code" => "HUF", "name" => "Hungarian Forint", "symbol" => "Ft"],
            "ISK" => ["code" => "ISK", "name" => "Icelandic KrÃ³na", "symbol" => "kr"],
            "INR" => ["code" => "INR", "name" => "Indian Rupee", "symbol" => "₹"],
            "IDR" => ["code" => "IDR", "name" => "Indonesian Rupiah", "symbol" => "Rp"],
            "IRR" => ["code" => "IRR", "name" => "Iranian Rial", "symbol" => "﷼"],
            "IQD" => ["code" => "IQD", "name" => "Iraqi Dinar", "symbol" => "د.ع"],
            "ILS" => ["code" => "ILS", "name" => "Israeli New Sheqel", "symbol" => "₪"],
            "ITL" => ["code" => "ITL", "name" => "Italian Lira", "symbol" => "L,£"],
            "JMD" => ["code" => "JMD", "name" => "Jamaican Dollar", "symbol" => "J$"],
            "JPY" => ["code" => "JPY", "name" => "Japanese Yen", "symbol" => "¥"],
            "JOD" => ["code" => "JOD", "name" => "Jordanian Dinar", "symbol" => "ا.د"],
            "KZT" => ["code" => "KZT", "name" => "Kazakhstani Tenge", "symbol" => "лв"],
            "KES" => ["code" => "KES", "name" => "Kenyan Shilling", "symbol" => "KSh"],
            "KWD" => ["code" => "KWD", "name" => "Kuwaiti Dinar", "symbol" => "ك.د"],
            "KGS" => ["code" => "KGS", "name" => "Kyrgystani Som", "symbol" => "лв"],
            "LAK" => ["code" => "LAK", "name" => "Laotian Kip", "symbol" => "₭"],
            "LVL" => ["code" => "LVL", "name" => "Latvian Lats", "symbol" => "Ls"],
            "LBP" => ["code" => "LBP", "name" => "Lebanese Pound", "symbol" => "£"],
            "LSL" => ["code" => "LSL", "name" => "Lesotho Loti", "symbol" => "L"],
            "LRD" => ["code" => "LRD", "name" => "Liberian Dollar", "symbol" => "$"],
            "LYD" => ["code" => "LYD", "name" => "Libyan Dinar", "symbol" => "د.ل"],
            "LTL" => ["code" => "LTL", "name" => "Lithuanian Litas", "symbol" => "Lt"],
            "MOP" => ["code" => "MOP", "name" => "Macanese Pataca", "symbol" => "$"],
            "MKD" => ["code" => "MKD", "name" => "Macedonian Denar", "symbol" => "ден"],
            "MGA" => ["code" => "MGA", "name" => "Malagasy Ariary", "symbol" => "Ar"],
            "MWK" => ["code" => "MWK", "name" => "Malawian Kwacha", "symbol" => "MK"],
            "MYR" => ["code" => "MYR", "name" => "Malaysian Ringgit", "symbol" => "RM"],
            "MVR" => ["code" => "MVR", "name" => "Maldivian Rufiyaa", "symbol" => "Rf"],
            "MRO" => ["code" => "MRO", "name" => "Mauritanian Ouguiya", "symbol" => "MRU"],
            "MUR" => ["code" => "MUR", "name" => "Mauritian Rupee", "symbol" => "Rs"],
            "MXN" => ["code" => "MXN", "name" => "Mexican Peso", "symbol" => "$"],
            "MDL" => ["code" => "MDL", "name" => "Moldovan Leu", "symbol" => "L"],
            "MNT" => ["code" => "MNT", "name" => "Mongolian Tugrik", "symbol" => "₮"],
            "MAD" => ["code" => "MAD", "name" => "Moroccan Dirham", "symbol" => "MAD"],
            "MZM" => ["code" => "MZM", "name" => "Mozambican Metical", "symbol" => "MT"],
            "MMK" => ["code" => "MMK", "name" => "Myanmar Kyat", "symbol" => "K"],
            "NAD" => ["code" => "NAD", "name" => "Namibian Dollar", "symbol" => "$"],
            "NPR" => ["code" => "NPR", "name" => "Nepalese Rupee", "symbol" => "Rs"],
            "ANG" => ["code" => "ANG", "name" => "Netherlands Antillean Guilder", "symbol" => "ƒ"],
            "TWD" => ["code" => "TWD", "name" => "New Taiwan Dollar", "symbol" => "$"],
            "NZD" => ["code" => "NZD", "name" => "New Zealand Dollar", "symbol" => "$"],
            "NIO" => ["code" => "NIO", "name" => "Nicaraguan CÃ³rdoba", "symbol" => "C$"],
            "NGN" => ["code" => "NGN", "name" => "Nigerian Naira", "symbol" => "₦"],
            "KPW" => ["code" => "KPW", "name" => "North Korean Won", "symbol" => "₩"],
            "NOK" => ["code" => "NOK", "name" => "Norwegian Krone", "symbol" => "kr"],
            "OMR" => ["code" => "OMR", "name" => "Omani Rial", "symbol" => ".ع.ر"],
            "PKR" => ["code" => "PKR", "name" => "Pakistani Rupee", "symbol" => "Rs"],
            "PAB" => ["code" => "PAB", "name" => "Panamanian Balboa", "symbol" => "B/."],
            "PGK" => ["code" => "PGK", "name" => "Papua New Guinean Kina", "symbol" => "K"],
            "PYG" => ["code" => "PYG", "name" => "Paraguayan Guarani", "symbol" => "₲"],
            "PEN" => ["code" => "PEN", "name" => "Peruvian Nuevo Sol", "symbol" => "S/."],
            "PHP" => ["code" => "PHP", "name" => "Philippine Peso", "symbol" => "₱"],
            "PLN" => ["code" => "PLN", "name" => "Polish Zloty", "symbol" => "zł"],
            "QAR" => ["code" => "QAR", "name" => "Qatari Rial", "symbol" => "ق.ر"],
            "RON" => ["code" => "RON", "name" => "Romanian Leu", "symbol" => "lei"],
            "RUB" => ["code" => "RUB", "name" => "Russian Ruble", "symbol" => "₽"],
            "RWF" => ["code" => "RWF", "name" => "Rwandan Franc", "symbol" => "FRw"],
            "SVC" => ["code" => "SVC", "name" => "Salvadoran ColÃ³n", "symbol" => "₡"],
            "WST" => ["code" => "WST", "name" => "Samoan Tala", "symbol" => "SAT"],
            "SAR" => ["code" => "SAR", "name" => "Saudi Riyal", "symbol" => "﷼"],
            "RSD" => ["code" => "RSD", "name" => "Serbian Dinar", "symbol" => "din"],
            "SCR" => ["code" => "SCR", "name" => "Seychellois Rupee", "symbol" => "SRe"],
            "SLL" => ["code" => "SLL", "name" => "Sierra Leonean Leone", "symbol" => "Le"],
            "SGD" => ["code" => "SGD", "name" => "Singapore Dollar", "symbol" => "$"],
            "SKK" => ["code" => "SKK", "name" => "Slovak Koruna", "symbol" => "Sk"],
            "SBD" => ["code" => "SBD", "name" => "Solomon Islands Dollar", "symbol" => "Si$"],
            "SOS" => ["code" => "SOS", "name" => "Somali Shilling", "symbol" => "Sh.so."],
            "ZAR" => ["code" => "ZAR", "name" => "South African Rand", "symbol" => "R"],
            "KRW" => ["code" => "KRW", "name" => "South Korean Won", "symbol" => "₩"],
            "XDR" => ["code" => "XDR", "name" => "Special Drawing Rights", "symbol" => "SDR"],
            "LKR" => ["code" => "LKR", "name" => "Sri Lankan Rupee", "symbol" => "Rs"],
            "SHP" => ["code" => "SHP", "name" => "St. Helena Pound", "symbol" => "£"],
            "SDG" => ["code" => "SDG", "name" => "Sudanese Pound", "symbol" => ".س.ج"],
            "SRD" => ["code" => "SRD", "name" => "Surinamese Dollar", "symbol" => "$"],
            "SZL" => ["code" => "SZL", "name" => "Swazi Lilangeni", "symbol" => "E"],
            "SEK" => ["code" => "SEK", "name" => "Swedish Krona", "symbol" => "kr"],
            "CHF" => ["code" => "CHF", "name" => "Swiss Franc", "symbol" => "CHf"],
            "SYP" => ["code" => "SYP", "name" => "Syrian Pound", "symbol" => "LS"],
            "STD" => ["code" => "STD", "name" => "São Tomé and Príncipe Dobra", "symbol" => "Db"],
            "TJS" => ["code" => "TJS", "name" => "Tajikistani Somoni", "symbol" => "SM"],
            "TZS" => ["code" => "TZS", "name" => "Tanzanian Shilling", "symbol" => "TSh"],
            "THB" => ["code" => "THB", "name" => "Thai Baht", "symbol" => "฿"],
            "TOP" => ["code" => "TOP", "name" => "Tongan pa'anga", "symbol" => "$"],
            "TTD" => ["code" => "TTD", "name" => "Trinidad & Tobago Dollar", "symbol" => "$"],
            "TND" => ["code" => "TND", "name" => "Tunisian Dinar", "symbol" => "ت.د"],
            "TRY" => ["code" => "TRY", "name" => "Turkish Lira", "symbol" => "₺"],
            "TMT" => ["code" => "TMT", "name" => "Turkmenistani Manat", "symbol" => "T"],
            "UGX" => ["code" => "UGX", "name" => "Ugandan Shilling", "symbol" => "USh"],
            "UAH" => ["code" => "UAH", "name" => "Ukrainian Hryvnia", "symbol" => "₴"],
            "AED" => ["code" => "AED", "name" => "United Arab Emirates Dirham", "symbol" => "إ.د"],
            "UYU" => ["code" => "UYU", "name" => "Uruguayan Peso", "symbol" => "$"],
            "USD" => ["code" => "USD", "name" => "US Dollar", "symbol" => "$"],
            "UZS" => ["code" => "UZS", "name" => "Uzbekistan Som", "symbol" => "лв"],
            "VUV" => ["code" => "VUV", "name" => "Vanuatu Vatu", "symbol" => "VT"],
            "VEF" => ["code" => "VEF", "name" => "Venezuelan BolÃvar", "symbol" => "Bs"],
            "VND" => ["code" => "VND", "name" => "Vietnamese Dong", "symbol" => "₫"],
            "YER" => ["code" => "YER", "name" => "Yemeni Rial", "symbol" => "﷼"],
            "ZMK" => ["code" => "ZMK", "name" => "Zambian Kwacha", "symbol" => "ZK"]
        ];

        if ($code) {
            if ($key) {
                return $list[strtoupper($code)][$key] ?? strtoupper($code);
            } else {
                return $list[strtoupper($code)] ?? null;
            }
        }
        return $list;
    }

    public static function details($currency)
    {
        return self::get($currency);
    }

    public static function symbol($currency)
    {
        return self::get($currency, 'symbol');
    }

    public static function name($currency)
    {
        return self::get($currency, 'name');
    }
}
