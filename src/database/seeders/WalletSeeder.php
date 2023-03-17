<?php

namespace Database\Seeders;

use App\Models\Wallet;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Wallet::create([
            'name' => 'Dompet Abi',
        ]);

        Wallet::create([
            'name' => 'Dompet Umma',
        ]);

        Wallet::create([
            'name' => 'Dompet Salwa',
        ]);
    }
}
