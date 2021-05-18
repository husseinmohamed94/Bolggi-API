<?php

use App\Models\Tag;
use Illuminate\Database\Seeder;
class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tag::create(['name' => 'Flowers']);
        Tag::create(['name' => 'Nature']);
        Tag::create(['name' => 'Electronic']);
        Tag::create(['name' => 'life']);
        Tag::create(['name' => 'style']);
        Tag::create(['name' => 'food']);
        Tag::create(['name' => 'Travel']);

    }
}
