<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Testimonial;
use App\Models\Milestone;
use App\Models\SiteFeature;
use App\Models\Tax;

class CmsSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if ($company) {
            $company->update([
                'about' => "We are a premium hospitality brand committed to delivering exceptional guest experiences across India. Our integrated management platform ensures every detail of your stay is handled with precision, from the moment you book online to the moment you check out. We combine modern technology with genuine warmth to create stays that guests will cherish forever.",
                'tagline' => 'Premium hotel management and direct booking engine. Manage reservations, rooms, guests, and operations from one powerful platform.',
                'founding_year' => '2018',
                'facebook_url' => 'https://facebook.com/mehmaan',
                'twitter_url' => 'https://twitter.com/mehmaan',
                'instagram_url' => 'https://instagram.com/mehmaan',
                'linkedin_url' => 'https://linkedin.com/company/mehmaan',
            ]);
        }

        Testimonial::insert([
            ['name' => 'Rajesh Kumar', 'role' => 'Business Traveler', 'quote' => 'An absolutely wonderful experience. The room was spotless, the staff was incredibly attentive, and the dining was superb. Will definitely return!', 'rating' => 5, 'status' => 'active', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Priya Sharma', 'role' => 'Family Vacation', 'quote' => 'The online booking was so smooth and the direct rates saved us money. The hotel exceeded all our expectations. Perfect for our family vacation.', 'rating' => 5, 'status' => 'active', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Amit Mehta', 'role' => 'Leisure Guest', 'quote' => 'From check-in to check-out, everything was flawless. The spa was world-class and the restaurant served the best food we have had in years.', 'rating' => 5, 'status' => 'active', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Milestone::insert([
            ['year' => '2018', 'title' => 'Founded with a Vision', 'description' => 'Launched our integrated hotel management platform to simplify operations and direct bookings.', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['year' => '2020', 'title' => 'Rapid Growth', 'description' => 'Expanded to multiple managed properties across major cities in India.', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['year' => '2023', 'title' => 'Digital Transformation', 'description' => 'Introduced AI-powered operations, mobile check-ins, and smart room technology.', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['year' => 'Today', 'title' => 'Leading Hospitality Tech', 'description' => 'Continuing to innovate with a growing network of premium hotels and satisfied guests.', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);

        SiteFeature::insert([
            ['icon' => 'fa-bed', 'title' => 'Premium Rooms', 'description' => 'Beautifully designed rooms with modern amenities, comfortable beds, and stunning views.', 'section' => 'home_features', 'status' => 'active', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['icon' => 'fa-utensils', 'title' => 'Fine Dining', 'description' => 'World-class restaurants serving local and international cuisine by expert chefs.', 'section' => 'home_features', 'status' => 'active', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['icon' => 'fa-spa', 'title' => 'Spa & Wellness', 'description' => 'Rejuvenate your body and soul with our premium spa and wellness facilities.', 'section' => 'home_features', 'status' => 'active', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['icon' => 'fa-concierge-bell', 'title' => '24/7 Concierge', 'description' => 'Our dedicated concierge team is always available to make your stay seamless.', 'section' => 'home_features', 'status' => 'active', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],

            ['icon' => 'fa-heart', 'title' => 'Genuine Hospitality', 'description' => 'Every guest is family. We go above and beyond to make each stay personal and memorable.', 'section' => 'about_values', 'status' => 'active', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['icon' => 'fa-gem', 'title' => 'Excellence', 'description' => 'We set the highest standards for cleanliness, service quality, and guest satisfaction.', 'section' => 'about_values', 'status' => 'active', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['icon' => 'fa-lightbulb', 'title' => 'Innovation', 'description' => 'Leveraging technology to simplify operations and enhance the guest experience.', 'section' => 'about_values', 'status' => 'active', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['icon' => 'fa-leaf', 'title' => 'Sustainability', 'description' => 'Committed to eco-friendly practices, reducing waste, and supporting local communities.', 'section' => 'about_values', 'status' => 'active', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);

        if (!Tax::where('is_default', true)->exists()) {
            Tax::create([
                'name' => 'GST',
                'slug' => 'gst',
                'rate' => 12.00,
                'type' => 'percentage',
                'is_default' => true,
                'status' => 'active',
            ]);
        }
    }
}
