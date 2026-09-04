<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Quotation;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Find a sales rep and support agent for assignments
        $salesRep = User::where('role', User::ROLE_SALES_EXECUTIVE)->first() ?? User::first();
        $supportAgent = User::where('role', User::ROLE_SUPPORT_AGENT)->first() ?? User::first();
        $admin = User::where('role', User::ROLE_ADMIN)->first() ?? User::first();

        // 2. Create Customer User Account
        $customerUser = User::updateOrCreate(
            ['email' => 'customer@crm360.com'],
            [
                'name'     => 'Alex Mercer',
                'password' => Hash::make('Customer@123'),
                'role'     => User::ROLE_CUSTOMER,
                'status'   => User::STATUS_ACTIVE,
                'phone'    => '+1 (555) 234-5678',
            ]
        );

        // 3. Create Corresponding Customer Company / Account Record
        $customer = Customer::updateOrCreate(
            ['email' => 'customer@crm360.com'],
            [
                'name'           => 'Alex Mercer',
                'company'        => 'Apex Global Technologies',
                'phone'          => '+1 (555) 234-5678',
                'website'        => 'https://apexglobal.tech',
                'industry'       => 'Enterprise Cloud Solutions',
                'address'        => '742 Evergreen Terrace, Suite 500',
                'city'           => 'San Francisco',
                'country'        => 'United States',
                'status'         => Customer::STATUS_ACTIVE,
                'source'         => 'referral',
                'assigned_to'    => (string) $salesRep->id,
                'created_by'     => (string) $admin->id,
                'annual_revenue' => 1850000,
                'employee_count' => 120,
                'notes'          => 'Tier-1 enterprise account evaluating annual CRM & API automation services.',
            ]
        );

        $customerId = (string) $customer->id;

        // 4. Contacts
        Contact::where('customer_id', $customerId)->delete();
        Contact::create([
            'customer_id' => $customerId,
            'name'        => 'Alex Mercer',
            'email'       => 'customer@crm360.com',
            'phone'       => '+1 (555) 234-5678',
            'position'    => 'Chief Technology Officer',
            'department'  => 'Executive Leadership',
            'is_primary'  => true,
        ]);
        Contact::create([
            'customer_id' => $customerId,
            'name'        => 'Elena Rostova',
            'email'       => 'elena.rostova@apexglobal.tech',
            'phone'       => '+1 (555) 987-6543',
            'position'    => 'Director of Procurement',
            'department'  => 'Finance & Legal',
            'is_primary'  => false,
        ]);

        // 5. Active Deal
        $stageProposal = DealStage::where('name', 'Proposal')->first() ?? DealStage::first();
        Deal::where('customer_id', $customerId)->delete();
        $deal = Deal::create([
            'title'               => 'Apex Global - Multi-Year Enterprise Platform License',
            'customer_id'         => $customerId,
            'assigned_to'         => (string) $salesRep->id,
            'created_by'          => (string) $salesRep->id,
            'stage_id'            => (string) $stageProposal->id,
            'value'               => 45000,
            'currency'            => 'USD',
            'probability'         => 75,
            'expected_close_date' => now()->addDays(20),
            'status'              => Deal::STATUS_OPEN,
            'notes'               => 'Includes CRM enterprise suite, dedicated technical support SLA, and API webhooks integration.',
        ]);

        // 6. Quotations
        Quotation::where('customer_id', $customerId)->delete();

        // Quotation 1: Accepted
        $quote1 = Quotation::create([
            'number'          => 'QT-2026-0001',
            'customer_id'     => $customerId,
            'deal_id'         => (string) $deal->id,
            'created_by'      => (string) $salesRep->id,
            'status'          => Quotation::STATUS_ACCEPTED,
            'items'           => [
                [
                    'name'        => 'CRM360 Enterprise License (50 Seats)',
                    'description' => 'Dedicated cloud tenant with MongoDB backing and role authorization',
                    'quantity'    => 1,
                    'unit_price'  => 12000.00,
                    'total'       => 12000.00,
                ],
                [
                    'name'        => 'Custom Integration & Onboarding Service',
                    'description' => 'API webhook configuration and legacy CRM data migration',
                    'quantity'    => 1,
                    'unit_price'  => 3000.00,
                    'total'       => 3000.00,
                ],
            ],
            'subtotal'        => 15000.00,
            'tax_rate'        => 10.0,
            'tax_amount'      => 1500.00,
            'discount_type'   => 'fixed',
            'discount_value'  => 500.00,
            'discount_amount' => 500.00,
            'total'           => 16000.00,
            'currency'        => 'USD',
            'valid_until'     => now()->addDays(10),
            'sent_at'         => now()->subDays(5),
            'notes'           => 'Special introductory bundle with premium onboarding support.',
            'terms'           => 'Net 30 payment terms upon contract signing.',
        ]);

        // Quotation 2: Sent / Active proposal
        $quote2 = Quotation::create([
            'number'          => 'QT-2026-0002',
            'customer_id'     => $customerId,
            'deal_id'         => (string) $deal->id,
            'created_by'      => (string) $salesRep->id,
            'status'          => Quotation::STATUS_SENT,
            'items'           => [
                [
                    'name'        => '24/7 Dedicated Technical Support SLA (Annual)',
                    'description' => 'Guaranteed 1-hour response time with designated support engineer',
                    'quantity'    => 1,
                    'unit_price'  => 6000.00,
                    'total'       => 6000.00,
                ],
            ],
            'subtotal'        => 6000.00,
            'tax_rate'        => 10.0,
            'tax_amount'      => 600.00,
            'discount_type'   => 'percentage',
            'discount_value'  => 5.0,
            'discount_amount' => 300.00,
            'total'           => 6300.00,
            'currency'        => 'USD',
            'valid_until'     => now()->addDays(14),
            'sent_at'         => now()->subDays(1),
            'notes'           => 'Add-on SLA package requested by Alex Mercer.',
            'terms'           => 'Valid for 14 days.',
        ]);

        // 7. Invoices & Payments
        Invoice::where('customer_id', $customerId)->delete();

        // Invoice 1: Paid in full
        $inv1 = Invoice::create([
            'number'            => 'INV-2026-0001',
            'customer_id'       => $customerId,
            'quotation_id'      => (string) $quote1->id,
            'deal_id'           => (string) $deal->id,
            'created_by'        => (string) $salesRep->id,
            'status'            => Invoice::STATUS_PAID,
            'items'             => $quote1->items,
            'subtotal'          => $quote1->subtotal,
            'tax_rate'          => $quote1->tax_rate,
            'tax_amount'        => $quote1->tax_amount,
            'discount_type'     => $quote1->discount_type,
            'discount_value'    => $quote1->discount_value,
            'discount_amount'   => $quote1->discount_amount,
            'total'             => $quote1->total,
            'amount_paid'       => $quote1->total,
            'amount_due'        => 0.00,
            'currency'          => 'USD',
            'due_date'          => now()->addDays(15),
            'paid_at'           => now()->subDays(2),
            'payment_method'    => 'bank_transfer',
            'payment_reference' => 'WIRE-APEX-94821',
            'notes'             => 'Full payment received via corporate bank wire transfer.',
            'sent_at'           => now()->subDays(5),
        ]);

        $quote1->update(['invoice_id' => (string) $inv1->id]);

        // Invoice 2: Sent & Unpaid ($4,400 balance due)
        $inv2 = Invoice::create([
            'number'            => 'INV-2026-0002',
            'customer_id'       => $customerId,
            'deal_id'           => (string) $deal->id,
            'created_by'        => (string) $salesRep->id,
            'status'            => Invoice::STATUS_SENT,
            'items'             => [
                [
                    'name'        => 'Custom API Integration Consulting (40 Hours)',
                    'description' => 'Senior CRM engineering and webhook middleware integration',
                    'quantity'    => 40,
                    'unit_price'  => 100.00,
                    'total'       => 4000.00,
                ],
            ],
            'subtotal'          => 4000.00,
            'tax_rate'          => 10.0,
            'tax_amount'        => 400.00,
            'discount_type'     => 'fixed',
            'discount_value'    => 0,
            'discount_amount'   => 0,
            'total'             => 4400.00,
            'amount_paid'       => 0.00,
            'amount_due'        => 4400.00,
            'currency'          => 'USD',
            'due_date'          => now()->addDays(20),
            'notes'             => 'Phase 1 development billing invoice.',
            'terms'             => 'Due within 30 days of receipt.',
            'sent_at'           => now()->subDays(2),
        ]);

        // Invoice 3: Partially Paid ($1,200 balance due)
        $inv3 = Invoice::create([
            'number'            => 'INV-2026-0003',
            'customer_id'       => $customerId,
            'created_by'        => (string) $salesRep->id,
            'status'            => Invoice::STATUS_PARTIAL,
            'items'             => [
                [
                    'name'        => 'User Training & Certification Bootcamp',
                    'description' => 'Full-day interactive training session for 15 sales reps',
                    'quantity'    => 1,
                    'unit_price'  => 2200.00,
                    'total'       => 2200.00,
                ],
            ],
            'subtotal'          => 2200.00,
            'tax_rate'          => 0,
            'tax_amount'        => 0,
            'discount_type'     => 'fixed',
            'discount_value'    => 0,
            'discount_amount'   => 0,
            'total'             => 2200.00,
            'amount_paid'       => 1000.00,
            'amount_due'        => 1200.00,
            'currency'          => 'USD',
            'due_date'          => now()->addDays(12),
            'paid_at'           => now()->subDays(1),
            'payment_method'    => 'credit_card',
            'payment_reference' => 'CH_STRIPE_98241',
            'notes'             => 'Initial 50% deposit received. Remaining balance due prior to session date.',
            'sent_at'           => now()->subDays(3),
        ]);

        // 8. Support Tickets with Threaded Conversations
        Ticket::where('customer_id', $customerId)->delete();

        // Ticket 1: Active In-Progress Ticket with real conversation thread
        $ticket1 = Ticket::create([
            'ticket_number'     => 'TIK-2026-0001',
            'title'             => 'API Webhook Signature Verification and Latency',
            'description'       => 'We are configuring webhook endpoints to listen for invoice.paid events. The HMAC SHA256 header hash fails validation intermittently.',
            'customer_id'       => $customerId,
            'created_by'        => (string) $customerUser->id,
            'assigned_to'       => (string) $supportAgent->id,
            'priority'          => Ticket::PRIORITY_HIGH,
            'status'            => Ticket::STATUS_IN_PROGRESS,
            'category'          => Ticket::CATEGORY_TECH,
            'first_response_at' => now()->subHours(4),
            'due_date'          => now()->addDays(2),
        ]);

        TicketMessage::where('ticket_id', (string) $ticket1->id)->delete();

        // Message 1 from customer
        TicketMessage::create([
            'ticket_id'   => (string) $ticket1->id,
            'sender_id'   => (string) $customerUser->id,
            'sender_role' => 'customer',
            'message'     => "Hello Support Team,\n\nWe are integrating your webhook events into our ERP system. When receiving the POST callback for 'invoice.paid', our hash verification function fails about 15% of the time. Could you check if the payload serialization order differs or if newline characters are included in the HMAC computation?\n\nThanks,\nAlex Mercer - CTO",
            'created_at'  => now()->subHours(5),
        ]);

        // Message 2 from support agent
        TicketMessage::create([
            'ticket_id'   => (string) $ticket1->id,
            'sender_id'   => (string) $supportAgent->id,
            'sender_role' => 'support_agent',
            'message'     => "Hi Alex,\n\nThank you for reaching out! In CRM360 v2.0, the webhook signature is computed using the raw request body before JSON decoding (`hash_hmac('sha256', \$rawBody, \$secret)`). If your middleware parses JSON before computing the hash, whitespace differences can invalidate the signature.\n\nCould you try validating directly against the raw stream before middleware parsing?\n\nBest regards,\nEmma - Senior Support Engineer",
            'created_at'  => now()->subHours(4),
        ]);

        // Message 3 response from customer
        TicketMessage::create([
            'ticket_id'   => (string) $ticket1->id,
            'sender_id'   => (string) $customerUser->id,
            'sender_role' => 'customer',
            'message'     => "Hi Emma,\n\nThat makes total sense! We tested with `file_get_contents('php://input')` before the JSON middleware and all signatures are matching 100% now. We will monitor through end of day before closing this ticket.\n\nAppreciate the swift help!",
            'created_at'  => now()->subHours(1),
        ]);

        // Ticket 2: Resolved Billing Ticket
        $ticket2 = Ticket::create([
            'ticket_number'     => 'TIK-2026-0002',
            'title'             => 'VAT and Tax Identification on European Invoice',
            'description'       => 'Please ensure our EU VAT ID is displayed on the invoice header for tax deduction purposes.',
            'customer_id'       => $customerId,
            'created_by'        => (string) $customerUser->id,
            'assigned_to'       => (string) $supportAgent->id,
            'priority'          => Ticket::PRIORITY_MEDIUM,
            'status'            => Ticket::STATUS_RESOLVED,
            'category'          => Ticket::CATEGORY_BILLING,
            'first_response_at' => now()->subDays(2),
            'resolved_at'       => now()->subDays(1),
        ]);

        TicketMessage::where('ticket_id', (string) $ticket2->id)->delete();
        TicketMessage::create([
            'ticket_id'   => (string) $ticket2->id,
            'sender_id'   => (string) $customerUser->id,
            'sender_role' => 'customer',
            'message'     => "Could you please add our corporate Tax / VAT ID 'US-94829104' to Invoice #INV-2026-0001?\n\nElena Rostova",
            'created_at'  => now()->subDays(2),
        ]);
        TicketMessage::create([
            'ticket_id'   => (string) $ticket2->id,
            'sender_id'   => (string) $supportAgent->id,
            'sender_role' => 'support_agent',
            'message'     => "Hello Elena,\n\nI have updated your account profile with Tax ID US-94829104 and regenerated PDF invoice #INV-2026-0001. You can now download the updated copy directly from your customer portal!\n\nBest regards,\nEmma",
            'created_at'  => now()->subDays(1),
        ]);

        // 9. Historical 6-Month Paid Invoices for Line/Bar Chart Trends
        $histRevenue = [
            ['month' => 5, 'amount' => 18500.00, 'num' => 'INV-HIST-0005'],
            ['month' => 4, 'amount' => 24200.00, 'num' => 'INV-HIST-0004'],
            ['month' => 3, 'amount' => 31000.00, 'num' => 'INV-HIST-0003'],
            ['month' => 2, 'amount' => 28400.00, 'num' => 'INV-HIST-0002'],
            ['month' => 1, 'amount' => 36800.00, 'num' => 'INV-HIST-0001'],
        ];

        foreach ($histRevenue as $hr) {
            $invDate = now()->subMonths($hr['month'])->subDays(5);
            Invoice::updateOrCreate(
                ['number' => $hr['num']],
                [
                    'customer_id'       => $customerId,
                    'created_by'        => (string) $salesRep->id,
                    'status'            => Invoice::STATUS_PAID,
                    'items'             => [
                        [
                            'name'        => 'Enterprise CRM Cloud Subscription',
                            'description' => 'Monthly recurring service agreement',
                            'quantity'    => 1,
                            'unit_price'  => $hr['amount'],
                            'total'       => $hr['amount'],
                        ],
                    ],
                    'subtotal'          => $hr['amount'],
                    'tax_rate'          => 0,
                    'tax_amount'        => 0,
                    'total'             => $hr['amount'],
                    'amount_paid'       => $hr['amount'],
                    'amount_due'        => 0.00,
                    'currency'          => 'USD',
                    'due_date'          => $invDate->copy()->addDays(15),
                    'paid_at'           => $invDate->copy()->addDays(3),
                    'created_at'        => $invDate,
                    'updated_at'        => $invDate,
                ]
            );
        }

        // 10. Sample Leads across Various Stages & Months
        $leadSamples = [
            ['title' => 'Cloud Migration Lead', 'company' => 'Nexus Dynamics', 'status' => Lead::STATUS_NEW, 'source' => 'website', 'priority' => 'high', 'months_ago' => 0],
            ['title' => 'CRM Automation Evaluation', 'company' => 'Vertex Industrial', 'status' => Lead::STATUS_CONTACTED, 'source' => 'referral', 'priority' => 'medium', 'months_ago' => 1],
            ['title' => 'Sales ERP Integration', 'company' => 'Omni Logistics', 'status' => Lead::STATUS_QUALIFIED, 'source' => 'social_media', 'priority' => 'high', 'months_ago' => 2],
            ['title' => 'Enterprise API Gateway', 'company' => 'Acro Global Corp', 'status' => Lead::STATUS_CONVERTED, 'source' => 'website', 'priority' => 'high', 'months_ago' => 3],
            ['title' => 'Legacy CRM Replacement', 'company' => 'Titan Marine Services', 'status' => Lead::STATUS_CONVERTED, 'source' => 'referral', 'priority' => 'high', 'months_ago' => 4],
            ['title' => 'Retail POS Bridge', 'company' => 'Beacon Retail Ltd', 'status' => Lead::STATUS_LOST, 'source' => 'cold_call', 'priority' => 'low', 'months_ago' => 5],
            ['title' => 'B2B Client Portal', 'company' => 'Helios Solar Tech', 'status' => Lead::STATUS_QUALIFIED, 'source' => 'event', 'priority' => 'medium', 'months_ago' => 1],
            ['title' => 'Supply Chain Dashboard', 'company' => 'Zephyr Express', 'status' => Lead::STATUS_CONTACTED, 'source' => 'website', 'priority' => 'high', 'months_ago' => 0],
        ];

        foreach ($leadSamples as $ls) {
            $createdTime = now()->subMonths($ls['months_ago'])->subDays(rand(2, 20));
            Lead::updateOrCreate(
                ['title' => $ls['title']],
                [
                    'first_name'     => 'Demo',
                    'last_name'      => 'Contact',
                    'email'          => strtolower(str_replace(' ', '', $ls['company'])) . '@example.com',
                    'company'        => $ls['company'],
                    'status'         => $ls['status'],
                    'source'         => $ls['source'],
                    'priority'       => $ls['priority'],
                    'assigned_to'    => (string) $salesRep->id,
                    'created_by'     => (string) $salesRep->id,
                    'value_estimate' => rand(15000, 75000),
                    'created_at'     => $createdTime,
                    'converted_at'   => $ls['status'] === Lead::STATUS_CONVERTED ? $createdTime->copy()->addDays(10) : null,
                ]
            );
        }

        // 11. Sample Deals across Stages
        $allStages = DealStage::orderBy('order')->get();
        if ($allStages->count() > 0) {
            $dealSamples = [
                ['title' => 'Omni Logistics Global Rollout', 'value' => 62000, 'stage' => 'Proposal', 'status' => 'open'],
                ['title' => 'Vertex Workflow Engine', 'value' => 38000, 'stage' => 'Negotiation', 'status' => 'open'],
                ['title' => 'Helios Solar Portal Contract', 'value' => 24000, 'stage' => 'Prospecting', 'status' => 'open'],
                ['title' => 'Acro Global License Expansion', 'value' => 52000, 'stage' => 'Closed Won', 'status' => 'won'],
            ];
            foreach ($dealSamples as $ds) {
                $stageObj = $allStages->firstWhere('name', $ds['stage']) ?? $allStages->first();
                Deal::updateOrCreate(
                    ['title' => $ds['title']],
                    [
                        'customer_id'         => $customerId,
                        'assigned_to'         => (string) $salesRep->id,
                        'created_by'          => (string) $salesRep->id,
                        'stage_id'            => (string) $stageObj->id,
                        'value'               => $ds['value'],
                        'currency'            => 'USD',
                        'probability'         => $stageObj->win_probability ?? 50,
                        'status'              => $ds['status'],
                        'expected_close_date' => now()->addDays(25),
                        'actual_close_date'   => $ds['status'] === 'won' ? now()->subDays(10) : null,
                        'created_at'          => now()->subDays(rand(10, 45)),
                    ]
                );
            }
        }

        // 12. Additional Sample Tickets for Support Analytics
        $ticketSamples = [
            ['title' => 'Database Sync Latency Under Peak Load', 'priority' => 'critical', 'status' => 'open', 'category' => 'technical'],
            ['title' => 'Feature Request: Custom Field Mapping in Webhooks', 'priority' => 'low', 'status' => 'in_progress', 'category' => 'feature_request'],
            ['title' => 'Single Sign-On (SSO) SAML 2.0 Identity Provider Config', 'priority' => 'high', 'status' => 'open', 'category' => 'technical'],
            ['title' => 'Q2 Billing Statement Discrepancy', 'priority' => 'medium', 'status' => 'resolved', 'category' => 'billing'],
        ];
        foreach ($ticketSamples as $idx => $ts) {
            Ticket::updateOrCreate(
                ['title' => $ts['title']],
                [
                    'ticket_number' => 'TIK-2026-000' . ($idx + 3),
                    'description'   => 'Sample enterprise support case for monitoring and resolution tracking.',
                    'customer_id'   => $customerId,
                    'created_by'    => (string) $customerUser->id,
                    'assigned_to'   => (string) $supportAgent->id,
                    'priority'      => $ts['priority'],
                    'status'        => $ts['status'],
                    'category'      => $ts['category'],
                    'resolved_at'   => $ts['status'] === 'resolved' ? now()->subDays(3) : null,
                    'created_at'    => now()->subDays(rand(2, 20)),
                ]
            );
        }

        $this->command->info('✅ Customer portal demo data seeded successfully!');
        $this->command->table(
            ['Role', 'Email', 'Password', 'Account Name', 'Company'],
            [
                ['Customer', 'customer@crm360.com', 'Customer@123', 'Alex Mercer', 'Apex Global Technologies']
            ]
        );
        $this->command->info("  - 1 Customer User & 1 Company Profile");
        $this->command->info("  - 2 Key Contacts (CTO & Procurement)");
        $this->command->info("  - 1 Active Deal ($45,000)");
        $this->command->info("  - 2 Quotations (QT-2026-0001 accepted, QT-2026-0002 sent)");
        $this->command->info("  - 3 Invoices (INV-2026-0001 paid, INV-2026-0002 sent, INV-2026-0003 partial)");
        $this->command->info("  - 2 Support Tickets with conversation messages (TIK-2026-0001 in-progress, TIK-2026-0002 resolved)");
    }
}
