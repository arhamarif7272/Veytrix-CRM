<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['group' => 'general', 'key' => 'company_name',    'value' => 'Veytrix',              'type' => 'text',   'label' => 'Company Name'],
            ['group' => 'general', 'key' => 'company_email',   'value' => 'support@veytrix.com',  'type' => 'email',  'label' => 'Company Email'],
            ['group' => 'general', 'key' => 'company_phone',   'value' => '+1 (555) 000-0000',    'type' => 'text',   'label' => 'Company Phone'],
            ['group' => 'general', 'key' => 'company_address', 'value' => '123 Business Ave, City','type' => 'text',  'label' => 'Company Address'],
            ['group' => 'general', 'key' => 'currency',        'value' => 'USD',                  'type' => 'text',   'label' => 'Currency'],
            ['group' => 'general', 'key' => 'currency_symbol', 'value' => '$',                    'type' => 'text',   'label' => 'Currency Symbol'],
            ['group' => 'general', 'key' => 'timezone',        'value' => 'UTC',                  'type' => 'text',   'label' => 'Timezone'],
            ['group' => 'general', 'key' => 'date_format',     'value' => 'd M Y',                'type' => 'text',   'label' => 'Date Format'],
            ['group' => 'general', 'key' => 'tax_rate',        'value' => '10',                   'type' => 'number', 'label' => 'Default Tax Rate (%)'],

            // Quotation/Invoice
            ['group' => 'invoice', 'key' => 'invoice_prefix',    'value' => 'INV-',  'type' => 'text', 'label' => 'Invoice Prefix'],
            ['group' => 'invoice', 'key' => 'quotation_prefix',  'value' => 'QUO-',  'type' => 'text', 'label' => 'Quotation Prefix'],
            ['group' => 'invoice', 'key' => 'invoice_terms',     'value' => 'Payment due within 30 days.', 'type' => 'textarea', 'label' => 'Default Invoice Terms'],
            ['group' => 'invoice', 'key' => 'quotation_terms',   'value' => 'This quotation is valid for 30 days.', 'type' => 'textarea', 'label' => 'Default Quotation Terms'],

            // Notifications
            ['group' => 'notifications', 'key' => 'lead_assigned_email',     'value' => '1', 'type' => 'boolean', 'label' => 'Email on Lead Assigned'],
            ['group' => 'notifications', 'key' => 'ticket_update_email',     'value' => '1', 'type' => 'boolean', 'label' => 'Email on Ticket Update'],
            ['group' => 'notifications', 'key' => 'quotation_sent_email',    'value' => '1', 'type' => 'boolean', 'label' => 'Email on Quotation Sent'],
            ['group' => 'notifications', 'key' => 'invoice_sent_email',      'value' => '1', 'type' => 'boolean', 'label' => 'Email on Invoice Sent'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Settings seeded: ' . count($settings) . ' settings created/updated.');
    }
}
