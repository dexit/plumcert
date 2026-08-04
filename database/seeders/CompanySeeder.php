<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::updateOrCreate(
            ['vat_number' => 'GB123456789'],
            [
                'name' => 'Utilities Combined Ltd',
                'gas_safe_registration' => '123456',
                'address' => '1 Example Street',
                'postcode' => 'EX1 1EX',
            ]
        );
    }
}
