<?php


namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->where('email', 'admin@gmail.com')->delete();
        DB::table('users')->where('email', 'customer@gmail.com')->delete();

        DB::table('users')->insert([
            'first_name' => 'Yuan De',
            'last_name' => 'Liu',
            'email' => 'admin@gmail.com',
            'phone' => '',
            'password' => bcrypt('123456'),
            'type' => 'admin',
        ]);
        DB::table('users')->insert([
            'first_name' => 'Yuan De',
            'last_name' => 'Liu',
            'email' => 'customer@gmail.com',
            'phone' => '',
            'password' => bcrypt('123456'),
            'type' => 'user',
        ]);
    }
}
