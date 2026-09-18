<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarketingCampaign;
use App\Models\MarketingCampaignLog;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Company;

class MarketingCampaignDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $hotel = Hotel::first();
        if (!$hotel) {
            $company = Company::first();
            $hotel = Hotel::create([
                'company_id' => $company?->id,
                'name' => 'Demo Hotel',
                'slug' => 'demo-hotel',
                'email' => 'demo@hotel.test',
                'phone' => '0000000000',
                'address' => 'Demo Address',
                'city' => 'Demo City',
                'state' => 'Demo State',
                'country' => 'Demo Country',
                'zipcode' => '000000',
                'status' => 'active',
            ]);
            $this->command->info('No hotel found — created Demo Hotel for campaign seeding.');
        }
        $hotelId = $hotel->id;

        // Fetch some guests to use as targets
        $guests = Guest::take(10)->get();
        if ($guests->isEmpty()) {
            // create sample guests
            $sampleGuests = [];
            for ($i = 1; $i <= 10; $i++) {
                $g = Guest::create([
                    'first_name' => 'Demo',
                    'last_name' => 'Guest'.$i,
                    'slug' => 'demo-guest-'.$i,
                    'email' => "demo.guest{$i}@example.test",
                    'phone' => '700000000'.($i % 10),
                    'status' => 'active',
                ]);
                $sampleGuests[] = $g;
            }
            $guests = collect($sampleGuests);
            $this->command->info('No guests found — created 10 demo guests for campaign seeding.');
        }

        // 1. Sent Email Campaign
        $c1 = MarketingCampaign::create([
            'hotel_id' => $hotelId,
            'name' => 'Summer Luxury Suite Promotion',
            'channel' => 'email',
            'subject' => 'Experience Elegance: Exclusive Summer Luxury Suite Offers! ☀️🏨',
            'content' => "Dear Guest,\n\nEscape the heat and indulge in luxury this season! Book a stay in our Premium Suite Room and enjoy exclusive privileges:\n- Complimentary fine-dining breakfast\n- 20% discount on spa sessions\n- Free late check-out\n\nClick the link below to unlock your luxury stay today!\n\nBest Regards,\nMehmaan ERP Hotels",
            'target_audience' => 'all_guests',
            'scheduled_at' => null,
            'sent_at' => now()->subDays(2),
            'status' => 'sent',
            'total_recipients' => $guests->count(),
            'successful_deliveries' => $guests->count() - 1, // Simulate 1 failure
        ]);

        // Seed logs for the first campaign
        $index = 0;
        foreach ($guests as $guest) {
            $status = $index === 0 ? 'failed' : 'sent';
            $error = $index === 0 ? 'Invalid recipient email domain' : null;

            MarketingCampaignLog::create([
                'campaign_id' => $c1->id,
                'guest_id' => $guest->id,
                'recipient_contact' => $guest->email ?? 'no-email@test.com',
                'status' => $status,
                'error_message' => $error,
                'sent_at' => now()->subDays(2),
            ]);
            $index++;
        }

        // 2. Draft SMS Campaign
        MarketingCampaign::create([
            'hotel_id' => $hotelId,
            'name' => 'Weekend Restaurant Discount SMS',
            'channel' => 'sms',
            'subject' => null,
            'content' => "Craving delicious food? 🍽️ Dine at our multi-cuisine restaurant this Saturday or Sunday and get flat 15% off! Show this SMS to your server. TC apply.",
            'target_audience' => 'recent_guests',
            'scheduled_at' => null,
            'sent_at' => null,
            'status' => 'draft',
            'total_recipients' => 0,
            'successful_deliveries' => 0,
        ]);

        // 3. Scheduled WhatsApp Campaign
        MarketingCampaign::create([
            'hotel_id' => $hotelId,
            'name' => 'Premium Loyalty Tier Welcome WhatsApp',
            'channel' => 'whatsapp',
            'subject' => null,
            'content' => "Dear Member,\n\nWelcome to our Platinum Loyalty Club! 🌟\n\nEnjoy complimentary room upgrades, exclusive dining coupons, and custom welcome hampers on all check-ins. We look forward to hosting you soon!",
            'target_audience' => 'loyalty_members',
            'scheduled_at' => now()->addDays(7),
            'sent_at' => null,
            'status' => 'scheduled',
            'total_recipients' => 0,
            'successful_deliveries' => 0,
        ]);

        $this->command->info('Marketing campaigns dummy data seeded successfully.');
    }
}
