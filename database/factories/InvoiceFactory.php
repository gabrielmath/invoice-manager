<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $documentList = [
            '68546277000158',
            '73497330000108',
            '27333869000104',
            '70326617000187',
            '78412637000182',
            '64693385000100',
            '46774948000103',
            '28250760000176',
            '84913742000106',
            '17774036000125',
        ];

        shuffle($documentList);

        $totalUsers = $this->getTotalUsers();

        return [
            'user_id'              => ($totalUsers > 0 ? rand(1, $totalUsers) : User::factory()),
            'value'                => rand(100, 10000),
            'sender_document'      => $documentList[0],
            'sender_name'          => $this->faker->company,
            'transporter_document' => $documentList[1],
            'transporter_name'     => $this->faker->company,
        ];
    }

    private function getTotalUsers(): int
    {
        return User::all()->count();
    }
}
