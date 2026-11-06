<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        User::factory()->create([
            'name' => 'テスト会員',
            'email' => 'member@example.com',
        ]);

        $categories = Category::factory()->count(5)->create();

        Article::factory()->count(30)->recycle($categories)->create();
        Article::factory()->count(3)->draft()->recycle($categories)->create();
        Article::factory()->count(2)->scheduled()->recycle($categories)->create();

        Announcement::factory()->count(10)->create();
    }
}
