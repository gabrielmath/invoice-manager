<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user1 = User::query()->find(1);
        $user2 = User::query()->find(2);

        Invoice::factory(5)->create(['user_id' => $user1->id]);
        Invoice::factory(5)->create(['user_id' => $user2->id]);

        Invoice::factory(40)->create();
    }
}
