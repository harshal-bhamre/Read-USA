<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parameter;

class ParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Parameter::create([
            'parameter_key' => 'MAIL_MAILER',
            'parameter_value' => 'smtp',
        ]);

        Parameter::create([
            'parameter_key' => 'MAIL_HOST',
            'parameter_value' => 'sandbox.smtp.mailtrap.io',
        ]);

        Parameter::create([
            'parameter_key' => 'MAIL_PORT',
            'parameter_value' => '2525',
        ]);

        Parameter::create([
            'parameter_key' => 'MAIL_USERNAME',
            'parameter_value' => '44ec034bc11765',
        ]);

        Parameter::create([
            'parameter_key' => 'MAIL_PASSWORD',
            'parameter_value' => 'e885d29df01d54',
        ]);

        Parameter::create([
            'parameter_key' => 'MAIL_ENCRYPTION',
            'parameter_value' => 'tls',
        ]);

        Parameter::create([
            'parameter_key' => 'MAIL_FROM_ADDRESS',
            'parameter_value' => 'sumeetn@gmail.com',
        ]);
    }
}
