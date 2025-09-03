<?php
// database/seeders/ExhibitorSeeder.php

namespace Database\Seeders;

use App\Models\Exhibitor;
use Illuminate\Database\Seeder;

class ExhibitorSeeder extends Seeder
{
    public function run(): void
    {
        $exhibitors = [
            [
                'name' => 'Tech Innovators Inc.',
                'description' => 'Leading software development company specializing in web and mobile applications. We create innovative solutions for businesses worldwide.',
                'contact_email' => 'hr@techinnovators.com',
                'phone' => '+60123456789',
                'website' => 'https://techinnovators.com',
                'industry' => 'Technology',
                'address' => 'Kuala Lumpur, Malaysia',
                'status' => 'approved',
                'booth_preferences' => ['premium', 'corner']
            ],
            [
                'name' => 'Global Finance Corp',
                'description' => 'International financial services company offering comprehensive banking and investment solutions to corporate and individual clients.',
                'contact_email' => 'careers@globalfinance.com',
                'phone' => '+60198765432',
                'website' => 'https://globalfinance.com',
                'industry' => 'Finance',
                'address' => 'Petaling Jaya, Malaysia',
                'status' => 'approved',
                'booth_preferences' => ['standard']
            ],
        ];

        foreach ($exhibitors as $exhibitor) {
            Exhibitor::create($exhibitor);
        }
    }
}