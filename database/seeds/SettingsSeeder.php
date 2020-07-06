<?php

use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $insert = [
			'param' => 'link',
			'value' => 'https://google.com'
		];
		
		DB::table('settings')->insert($insert);
    }
}
