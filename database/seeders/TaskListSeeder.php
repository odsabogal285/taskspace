<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('task_lists')->delete();

        \DB::table('task_lists')->insert( array (
            0 => array(
                'id' => 1,
                'name' => 'General',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null
            ),
        ));
    }
}
