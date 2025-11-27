<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigurationTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('configurations')->truncate();

        DB::statement("INSERT INTO configurations (id,`key`,`type`,display_name,value,is_visible,status,created_at,updated_at,deleted_at) VALUES
	 (1,'name','text','Application Name','Software Name',1,1,'2023-09-27 10:05:57','2023-09-27 10:05:57',NULL),
	 (2,'address_configuration','text','HQ Address','TMSS Bhaban (5th Floor),West Kazipara, Mirpur-10,Dhaka-1216.',1,1,'2023-11-27 09:24:46','2023-11-27 09:24:46',NULL),
	 (3,'phone_configuration','text','Contact Us','01725785256',1,1,'2023-11-27 09:25:16','2023-11-27 12:04:58',NULL)");
    }
}
