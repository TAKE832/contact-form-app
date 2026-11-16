<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Tag;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ContactSeeder::class,
            TagSeeder::class,
        ]);

        $contacts = Contact::all();

        $tagIds = Tag::pluck('id')->toArray();

        $ids = fake()->randomElements(
            $tagIds,
            rand(1, 3)
        );

        foreach ($contacts as $contact) {
            $contact->tags()->attach($ids);
        }
    }
}
