<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customization_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ref_number')->unique(); // REQmmYYid

            // Origin tracking for sync from dashboardv2
            $table->unsignedBigInteger('origin_cust_req_id')->nullable(); // old cust_req_id from dashboardv2
            $table->unsignedBigInteger('origin_question_id')->nullable(); // old customization_questions.id

            // User info from main app (via SSO)
            $table->unsignedBigInteger('user_id'); // dashboardv2 users.id
            $table->string('user_email');
            $table->string('user_name');

            // Contact details
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('sec_phone')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_address')->nullable();

            // Community / domain info
            $table->string('community_name')->nullable();
            $table->string('community_handle_name')->nullable();
            $table->string('community_domain_name')->nullable();

            // Requirements flags
            $table->boolean('req_logo')->default(false);
            $table->boolean('req_icon')->default(false);
            $table->boolean('req_app_background')->default(false);
            $table->boolean('req_landing_page')->default(false);
            $table->boolean('req_others')->default(false);
            $table->string('req_primary_color')->nullable();
            $table->string('req_sec_color')->nullable();
            $table->boolean('req_donation')->default(false);
            $table->text('request_description')->nullable();
            $table->text('request_donate_description')->nullable();
            $table->boolean('additional_features')->default(false);

            // Status: 0=new, 1=in_progress, 2=completed
            $table->tinyInteger('status')->default(0);

            // Payment
            $table->tinyInteger('pay_type')->default(1); // 1=free, 2=paid
            $table->decimal('pay_amount', 10, 2)->default(0);
            $table->tinyInteger('pay_status')->default(0); // 0=unpaid, 1=paid
            $table->string('pay_id')->nullable(); // Stripe/PayPal transaction ID
            $table->timestamp('paid_at')->nullable();

            // Technician assignment
            $table->unsignedBigInteger('assigned_tech_id1')->nullable();
            $table->string('assigned_tech_name1')->nullable();
            $table->unsignedBigInteger('assigned_tech_id2')->nullable();
            $table->string('assigned_tech_name2')->nullable();
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->string('supervisor_name')->nullable();

            // Timeline
            $table->timestamp('tech_receive_date')->nullable();
            $table->timestamp('tech_process_date')->nullable();
            $table->timestamp('date_complete')->nullable();
            $table->integer('num_of_days')->nullable();

            // Notes
            $table->text('technician_comments')->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable();

            // User notification
            $table->boolean('user_alert')->default(false);

            // Login credentials submitted by user
            $table->string('login_email')->nullable();
            $table->string('login_password')->nullable(); // stored as-is (user provided)

            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('status');
            $table->index('assigned_tech_id1');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customization_requests');
    }
};
