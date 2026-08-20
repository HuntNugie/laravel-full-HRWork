<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            // Bank Persero
            [
                'bank_code' => 2,
                'name' => 'Bank Rakyat Indonesia',
                'short_name' => 'BRI',
            ],
            [
                'bank_code' => 8,
                'name' => 'Bank Mandiri',
                'short_name' => 'Mandiri',
            ],
            [
                'bank_code' => 9,
                'name' => 'Bank Negara Indonesia',
                'short_name' => 'BNI',
            ],
            [
                'bank_code' => 200,
                'name' => 'Bank Tabungan Negara',
                'short_name' => 'BTN',
            ],

            // Bank Swasta Nasional
            [
                'bank_code' => 11,
                'name' => 'Bank Danamon Indonesia',
                'short_name' => 'Danamon',
            ],
            [
                'bank_code' => 13,
                'name' => 'Bank Permata',
                'short_name' => 'Permata',
            ],
            [
                'bank_code' => 14,
                'name' => 'Bank Central Asia',
                'short_name' => 'BCA',
            ],
            [
                'bank_code' => 19,
                'name' => 'Bank Pan Indonesia',
                'short_name' => 'Panin',
            ],
            [
                'bank_code' => 22,
                'name' => 'Bank CIMB Niaga',
                'short_name' => 'CIMB Niaga',
            ],
            [
                'bank_code' => 28,
                'name' => 'Bank OCBC Indonesia',
                'short_name' => 'OCBC',
            ],
            [
                'bank_code' => 37,
                'name' => 'Bank Artha Graha Internasional',
                'short_name' => 'Artha Graha',
            ],
            [
                'bank_code' => 76,
                'name' => 'Bank Bumi Arta',
                'short_name' => 'Bumi Arta',
            ],
            [
                'bank_code' => 97,
                'name' => 'Bank Mayapada Internasional',
                'short_name' => 'Mayapada',
            ],
            [
                'bank_code' => 147,
                'name' => 'Bank Muamalat Indonesia',
                'short_name' => 'Muamalat',
            ],
            [
                'bank_code' => 151,
                'name' => 'Bank Mestika Dharma',
                'short_name' => 'Mestika',
            ],
            [
                'bank_code' => 153,
                'name' => 'Bank Sinarmas',
                'short_name' => 'Sinarmas',
            ],
            [
                'bank_code' => 157,
                'name' => 'Bank Maspion Indonesia',
                'short_name' => 'Maspion',
            ],
            [
                'bank_code' => 161,
                'name' => 'Bank Ganesha',
                'short_name' => 'Ganesha',
            ],
            [
                'bank_code' => 167,
                'name' => 'Bank QNB Indonesia',
                'short_name' => 'QNB',
            ],
            [
                'bank_code' => 212,
                'name' => 'Bank Woori Saudara Indonesia',
                'short_name' => 'Woori Saudara',
            ],
            [
                'bank_code' => 426,
                'name' => 'Bank Mega',
                'short_name' => 'Mega',
            ],
            [
                'bank_code' => 441,
                'name' => 'Bank KB Bukopin',
                'short_name' => 'KB Bukopin',
            ],
            [
                'bank_code' => 459,
                'name' => 'Krom Bank Indonesia',
                'short_name' => 'Krom Bank',
            ],
            [
                'bank_code' => 472,
                'name' => 'Bank Jasa Jakarta',
                'short_name' => 'BJJ',
            ],
            [
                'bank_code' => 484,
                'name' => 'Bank KEB Hana Indonesia',
                'short_name' => 'Hana Bank',
            ],
            [
                'bank_code' => 485,
                'name' => 'Bank MNC Internasional',
                'short_name' => 'MNC Bank',
            ],
            [
                'bank_code' => 490,
                'name' => 'Bank Neo Commerce',
                'short_name' => 'BNC',
            ],
            [
                'bank_code' => 494,
                'name' => 'Bank Raya Indonesia',
                'short_name' => 'Bank Raya',
            ],
            [
                'bank_code' => 498,
                'name' => 'Bank SBI Indonesia',
                'short_name' => 'SBI Indonesia',
            ],
            [
                'bank_code' => 503,
                'name' => 'Bank Nationalnobu',
                'short_name' => 'Nobu',
            ],
            [
                'bank_code' => 513,
                'name' => 'Bank Ina Perdana',
                'short_name' => 'Bank INA',
            ],
            [
                'bank_code' => 520,
                'name' => 'Prima Master Bank',
                'short_name' => 'Prima Master',
            ],
            [
                'bank_code' => 523,
                'name' => 'Bank Sahabat Sampoerna',
                'short_name' => 'Sampoerna',
            ],
            [
                'bank_code' => 526,
                'name' => 'Bank Oke Indonesia',
                'short_name' => 'Bank Oke',
            ],
            [
                'bank_code' => 531,
                'name' => 'Bank Amar Indonesia',
                'short_name' => 'Amar Bank',
            ],
            [
                'bank_code' => 548,
                'name' => 'Bank Multiarta Sentosa',
                'short_name' => 'MAS',
            ],
            [
                'bank_code' => 555,
                'name' => 'Bank Index Selindo',
                'short_name' => 'Bank Index',
            ],
            [
                'bank_code' => 562,
                'name' => 'Superbank Indonesia',
                'short_name' => 'Superbank',
            ],
            [
                'bank_code' => 564,
                'name' => 'Bank Mandiri Taspen',
                'short_name' => 'Bank Mantap',
            ],
            [
                'bank_code' => 566,
                'name' => 'Bank Victoria International',
                'short_name' => 'Bank Victoria',
            ],
            [
                'bank_code' => 567,
                'name' => 'Allo Bank Indonesia',
                'short_name' => 'Allo Bank',
            ],

            // Bank Digital
            [
                'bank_code' => 501,
                'name' => 'Bank Digital BCA',
                'short_name' => 'BCA Digital',
            ],
            [
                'bank_code' => 535,
                'name' => 'Bank Seabank Indonesia',
                'short_name' => 'SeaBank',
            ],
            [
                'bank_code' => 542,
                'name' => 'Bank Jago',
                'short_name' => 'Jago',
            ],
            [
                'bank_code' => 553,
                'name' => 'Bank Hibank Indonesia',
                'short_name' => 'hibank',
            ],

            // Bank Syariah
            [
                'bank_code' => 405,
                'name' => 'Bank Victoria Syariah',
                'short_name' => 'Victoria Syariah',
            ],
            [
                'bank_code' => 425,
                'name' => 'Bank BJB Syariah',
                'short_name' => 'BJB Syariah',
            ],
            [
                'bank_code' => 451,
                'name' => 'Bank Syariah Indonesia',
                'short_name' => 'BSI',
            ],
            [
                'bank_code' => 506,
                'name' => 'Bank Mega Syariah',
                'short_name' => 'Mega Syariah',
            ],
            [
                'bank_code' => 517,
                'name' => 'Bank Panin Dubai Syariah',
                'short_name' => 'Panin Dubai Syariah',
            ],
            [
                'bank_code' => 521,
                'name' => 'Bank KB Bukopin Syariah',
                'short_name' => 'KB Bukopin Syariah',
            ],
            [
                'bank_code' => 536,
                'name' => 'Bank BCA Syariah',
                'short_name' => 'BCA Syariah',
            ],
            [
                'bank_code' => 547,
                'name' => 'Bank BTPN Syariah',
                'short_name' => 'BTPN Syariah',
            ],
            [
                'bank_code' => 947,
                'name' => 'Bank Aladin Syariah',
                'short_name' => 'Aladin Syariah',
            ],

            // Bank Pembangunan Daerah (BPD)
            [
                'bank_code' => 110,
                'name' => 'Bank BJB',
                'short_name' => 'BJB',
            ],
            [
                'bank_code' => 111,
                'name' => 'Bank DKI',
                'short_name' => 'Bank DKI',
            ],
            [
                'bank_code' => 112,
                'name' => 'Bank BPD DIY',
                'short_name' => 'Bank DIY',
            ],
            [
                'bank_code' => 113,
                'name' => 'Bank Jawa Tengah',
                'short_name' => 'Bank Jateng',
            ],
            [
                'bank_code' => 114,
                'name' => 'Bank Jawa Timur',
                'short_name' => 'Bank Jatim',
            ],
            [
                'bank_code' => 115,
                'name' => 'Bank Jambi',
                'short_name' => 'Bank Jambi',
            ],
            [
                'bank_code' => 116,
                'name' => 'Bank Aceh Syariah',
                'short_name' => 'Bank Aceh',
            ],
            [
                'bank_code' => 117,
                'name' => 'Bank Sumut',
                'short_name' => 'Bank Sumut',
            ],
            [
                'bank_code' => 118,
                'name' => 'Bank Nagari',
                'short_name' => 'Bank Nagari',
            ],
            [
                'bank_code' => 119,
                'name' => 'Bank Riau Kepri',
                'short_name' => 'Bank Riau Kepri',
            ],
            [
                'bank_code' => 120,
                'name' => 'Bank Sumsel Babel',
                'short_name' => 'Sumsel Babel',
            ],
            [
                'bank_code' => 121,
                'name' => 'Bank Lampung',
                'short_name' => 'Bank Lampung',
            ],
            [
                'bank_code' => 122,
                'name' => 'Bank Kalimantan Selatan',
                'short_name' => 'Bank Kalsel',
            ],
            [
                'bank_code' => 123,
                'name' => 'Bank Kalimantan Barat',
                'short_name' => 'Bank Kalbar',
            ],
            [
                'bank_code' => 124,
                'name' => 'Bank Kaltimtara',
                'short_name' => 'Bank Kaltimtara',
            ],
            [
                'bank_code' => 125,
                'name' => 'Bank Kalimantan Tengah',
                'short_name' => 'Bank Kalteng',
            ],
            [
                'bank_code' => 126,
                'name' => 'Bank Sulselbar',
                'short_name' => 'Sulselbar',
            ],
            [
                'bank_code' => 127,
                'name' => 'Bank SulutGo',
                'short_name' => 'SulutGo',
            ],
            [
                'bank_code' => 128,
                'name' => 'Bank NTB Syariah',
                'short_name' => 'Bank NTB',
            ],
            [
                'bank_code' => 129,
                'name' => 'Bank BPD Bali',
                'short_name' => 'Bank Bali',
            ],
            [
                'bank_code' => 130,
                'name' => 'Bank NTT',
                'short_name' => 'Bank NTT',
            ],
            [
                'bank_code' => 131,
                'name' => 'Bank Maluku Malut',
                'short_name' => 'Bank Maluku',
            ],
            [
                'bank_code' => 132,
                'name' => 'Bank Papua',
                'short_name' => 'Bank Papua',
            ],
            [
                'bank_code' => 133,
                'name' => 'Bank Bengkulu',
                'short_name' => 'Bank Bengkulu',
            ],
            [
                'bank_code' => 134,
                'name' => 'Bank Sulteng',
                'short_name' => 'Bank Sulteng',
            ],
            [
                'bank_code' => 135,
                'name' => 'Bank Sultra',
                'short_name' => 'Bank Sultra',
            ],
            [
                'bank_code' => 137,
                'name' => 'Bank Banten',
                'short_name' => 'Bank Banten',
            ],

            // Bank Asing
            [
                'bank_code' => 31,
                'name' => 'Citibank N.A.',
                'short_name' => 'Citibank',
            ],
            [
                'bank_code' => 32,
                'name' => 'JPMorgan Chase Bank',
                'short_name' => 'JPMorgan',
            ],
            [
                'bank_code' => 33,
                'name' => 'Bank of America',
                'short_name' => 'Bank of America',
            ],
            [
                'bank_code' => 42,
                'name' => 'MUFG Bank',
                'short_name' => 'MUFG',
            ],
            [
                'bank_code' => 45,
                'name' => 'Bank Sumitomo Mitsui Indonesia',
                'short_name' => 'SMBC',
            ],
            [
                'bank_code' => 46,
                'name' => 'Bank DBS Indonesia',
                'short_name' => 'DBS',
            ],
            [
                'bank_code' => 47,
                'name' => 'Bank Resona Perdania',
                'short_name' => 'Resona',
            ],
            [
                'bank_code' => 48,
                'name' => 'Bank Mizuho Indonesia',
                'short_name' => 'Mizuho',
            ],
            [
                'bank_code' => 50,
                'name' => 'Standard Chartered Bank',
                'short_name' => 'Standard Chartered',
            ],
            [
                'bank_code' => 57,
                'name' => 'Bank BNP Paribas Indonesia',
                'short_name' => 'BNP Paribas',
            ],
            [
                'bank_code' => 67,
                'name' => 'Deutsche Bank AG',
                'short_name' => 'Deutsche Bank',
            ],
            [
                'bank_code' => 69,
                'name' => 'Bank of China',
                'short_name' => 'Bank of China',
            ],
            [
                'bank_code' => 68,
                'name' => 'Bank Woori Indonesia',
                'short_name' => 'Woori',
            ],
            [
                'bank_code' => 164,
                'name' => 'Bank ICBC Indonesia',
                'short_name' => 'ICBC',
            ],
            [
                'bank_code' => 949,
                'name' => 'Bank CTBC Indonesia',
                'short_name' => 'CTBC',
            ],
            [
                'bank_code' => 950,
                'name' => 'Bank Commonwealth',
                'short_name' => 'Commonwealth',
            ],
        ];

        foreach($banks as $bank){
            Bank::create($bank);
        }
    }
}
