<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testCustomer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567890',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        Address::factory()->default()->create([
            'customer_id' => $testCustomer->id,
        ]);

        Address::factory()->create([
            'customer_id' => $testCustomer->id,
        ]);

        $this->command->info('Creating customers...');
        $bar = $this->command->getOutput()->createProgressBar(50);

        for ($i = 0; $i < 50; $i++) {
            $customer = Customer::factory()->create();

            Address::factory()->default()->create([
                'customer_id' => $customer->id,
            ]);

            if (rand(0, 100) > 50) {
                Address::factory()->create([
                    'customer_id' => $customer->id,
                ]);
            }

            // Correction ici : on récupère jusqu'à 3 produits uniques d'un coup
            $reviewCount = rand(0, 3);
            if ($reviewCount > 0) {
                $randomProducts = Product::inRandomOrder()->limit($reviewCount)->get();

                foreach ($randomProducts as $product) {
                    Review::factory()->create([
                        'customer_id' => $customer->id,
                        'product_id' => $product->id,
                    ]);
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Customer created successfully.');
    }
}