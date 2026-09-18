<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\MessageTemplate;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            ['name' => 'English', 'code' => 'en', 'native_name' => 'English', 'direction' => 'ltr', 'is_default' => true, 'status' => 'active'],
            ['name' => 'Hindi', 'code' => 'hi', 'native_name' => 'हिन्दी', 'direction' => 'ltr', 'is_default' => false, 'status' => 'active'],
            ['name' => 'Arabic', 'code' => 'ar', 'native_name' => 'العربية', 'direction' => 'rtl', 'is_default' => false, 'status' => 'active'],
        ];

        foreach ($languages as $lang) {
            Language::updateOrCreate(['code' => $lang['code']], $lang);
        }

        $templates = [
            ['name' => 'Booking Confirmation', 'channel' => 'email', 'event' => 'reservation_confirmed', 'subject' => 'Booking Confirmed - {{reservation_number}}', 'body' => "Dear {{guest_name}},\n\nYour booking at {{hotel_name}} has been confirmed.\n\nReservation: {{reservation_number}}\nCheck-in: {{check_in_date}}\nCheck-out: {{check_out_date}}\nRoom: {{room_number}}\n\nWe look forward to welcoming you!\n\nBest regards,\n{{hotel_name}}", 'variables' => ['guest_name', 'reservation_number', 'hotel_name', 'check_in_date', 'check_out_date', 'room_number']],
            ['name' => 'Check-in Reminder', 'channel' => 'sms', 'event' => 'check_in_reminder', 'subject' => null, 'body' => 'Hi {{guest_name}}, your check-in at {{hotel_name}} is tomorrow ({{check_in_date}}). Room: {{room_number}}. See you soon!', 'variables' => ['guest_name', 'hotel_name', 'check_in_date', 'room_number']],
            ['name' => 'OTP Verification', 'channel' => 'sms', 'event' => 'otp_verification', 'subject' => null, 'body' => 'Your verification OTP is {{otp}}. Valid for 10 minutes. Do not share this with anyone.', 'variables' => ['otp']],
            ['name' => 'Check-out Invoice', 'channel' => 'email', 'event' => 'check_out_invoice', 'subject' => 'Invoice for your stay - {{reservation_number}}', 'body' => "Dear {{guest_name}},\n\nThank you for staying with us. Please find your invoice details below.\n\nReservation: {{reservation_number}}\nRoom: {{room_number}}\nCheck-in: {{check_in_date}}\nCheck-out: {{check_out_date}}\nTotal: {{amount}}\n\nWe hope to see you again!\n\nBest regards,\n{{hotel_name}}", 'variables' => ['guest_name', 'reservation_number', 'room_number', 'check_in_date', 'check_out_date', 'amount']],
            ['name' => 'Booking Confirmation WA', 'channel' => 'whatsapp', 'event' => 'reservation_confirmed_wa', 'subject' => null, 'body' => 'Hi {{guest_name}}! Your booking at {{hotel_name}} is confirmed. Reservation: {{reservation_number}}, Check-in: {{check_in_date}}, Room: {{room_number}}. See you soon!', 'variables' => ['guest_name', 'hotel_name', 'reservation_number', 'check_in_date', 'room_number']],
            ['name' => 'Welcome Guest', 'channel' => 'email', 'event' => 'welcome_guest', 'subject' => 'Welcome to {{hotel_name}}!', 'body' => "Dear {{guest_name}},\n\nWelcome to {{hotel_name}}! We're delighted to have you with us.\n\nIf you need anything during your stay, please don't hesitate to reach out.\n\nEnjoy your stay!\n\nBest regards,\n{{hotel_name}} Team", 'variables' => ['guest_name', 'hotel_name']],
        ];

        foreach ($templates as $tpl) {
            MessageTemplate::updateOrCreate(['event' => $tpl['event']], $tpl);
        }
    }
}
