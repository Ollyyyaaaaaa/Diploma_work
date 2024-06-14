<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Payway;
use App\Models\PaywayCurrency;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Currency::query()->insert([
            [
                'name' => 'USD',
                'is_active' => false,
                'is_crypto' => false,
                'precision' => 2
            ],
            [
                'name' => 'EUR',
                'is_active' => true,
                'is_crypto' => false,
                'precision' => 2
            ],
            [
                'name' => 'UAH',
                'is_active' => true,
                'is_crypto' => false,
                'precision' => 2
            ],
            [
                'name' => 'TRX',
                'is_active' => true,
                'is_crypto' => true,
                'precision' => 6
            ],
            [
                'name' => 'USDT',
                'is_active' => true,
                'is_crypto' => true,
                'precision' => 2
            ],
            [
                'name' => 'ETH',
                'is_active' => false,
                'is_crypto' => true,
                'precision' => 8
            ],
            [
                'name' => 'BTC',
                'is_active' => false,
                'is_crypto' => true,
                'precision' => 8
            ],
            [
                'name' => 'LTC',
                'is_active' => true,
                'is_crypto' => true,
                'precision' => 8
            ]
        ]);

        Payway::query()->insert([
            [
                'name' => 'iban',
                'is_active' => false,
                'limit' => 100000
            ],
            [
                'name' => 'card',
                'is_active' => true,
                'limit' => 100000
            ],
            [
                'name' => 'trc20',
                'is_active' => true,
                'limit' => 1000000
            ],
            [
                'name' => 'erc20',
                'is_active' => false,
                'limit' => 1000
            ],
            [
                'name' => 'btc',
                'is_active' => false,
                'limit' => 1000
            ],
            [
                'name' => 'ltc',
                'is_active' => true,
                'limit' => 1000
            ]
        ]);

        PaywayCurrency::query()->insert([
            [
                'currency_id' => 1,
                'payway_id' => 1,
                'is_active' => false,
                'max' => 1000,
                'min' => 1,
                'fee' => 0.1
            ],
            [
                'currency_id' => 1,
                'payway_id' => 2,
                'is_active' => false,
                'max' => 1000,
                'min' => 1,
                'fee' => 0.1
            ],
            [
                'currency_id' => 2,
                'payway_id' => 1,
                'is_active' => false,
                'max' => 1000,
                'min' => 1,
                'fee' => 0.1
            ],
            [
                'currency_id' => 2,
                'payway_id' => 2,
                'is_active' => true,
                'max' => 1000,
                'min' => 1,
                'fee' => 0.1
            ],
            [
                'currency_id' => 3,
                'payway_id' => 1,
                'is_active' => false,
                'max' => 1000,
                'min' => 10,
                'fee' => 0.1
            ],
            [
                'currency_id' => 3,
                'payway_id' => 2,
                'is_active' => true,
                'max' => 1000,
                'min' => 10,
                'fee' => 0.1
            ],
            [
                'currency_id' => 4,
                'payway_id' => 3,
                'is_active' => true,
                'max' => 100000,
                'min' => 1,
                'fee' => 0.1
            ],
            [
                'currency_id' => 5,
                'payway_id' => 3,
                'is_active' => true,
                'max' => 1000,
                'min' => 0.1,
                'fee' => 0.1
            ],
            [
                'currency_id' => 5,
                'payway_id' => 4,
                'is_active' => false,
                'max' => 1000,
                'min' => 0.1,
                'fee' => 0.1
            ],
            [
                'currency_id' => 6,
                'payway_id' => 4,
                'is_active' => false,
                'max' => 100,
                'min' => 0.01,
                'fee' => 0
            ],
            [
                'currency_id' => 7,
                'payway_id' => 5,
                'is_active' => false,
                'max' => 100,
                'min' => 0.01,
                'fee' => 0
            ],
            [
                'currency_id' => 8,
                'payway_id' => 6,
                'is_active' => true,
                'max' => 100,
                'min' => 0.01,
                'fee' => 0
            ]
        ]);
    }
}
