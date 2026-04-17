<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Customization Portal — Feature Documentation</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 30px 40px; color: #222; font-size: 12px; line-height: 1.5; }

        /* Cover */
        .cover { text-align: center; padding: 80px 20px 60px; }
        .cover img { height: 70px; margin-bottom: 30px; }
        .cover h1 { color: #662c87; font-size: 32px; margin: 0 0 10px; letter-spacing: 1px; }
        .cover .sub { color: #666; font-size: 14px; margin-bottom: 6px; }
        .cover .meta { color: #999; font-size: 11px; margin-top: 40px; }
        .cover hr { border: none; border-top: 2px solid #662c87; width: 60px; margin: 15px auto; }

        h2 { color: #662c87; font-size: 18px; margin-top: 28px; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 2px solid #662c87; }
        h3 { color: #333; font-size: 14px; margin-top: 14px; margin-bottom: 6px; }

        ul { margin: 6px 0 10px 18px; padding: 0; }
        li { margin-bottom: 4px; }

        .section { page-break-inside: avoid; margin-bottom: 14px; }
        .feature-box {
            background: #faf7fc;
            border-left: 3px solid #662c87;
            padding: 10px 14px;
            margin: 8px 0;
            border-radius: 3px;
        }
        .feature-box strong { color: #662c87; }

        .tech-stack {
            background: #f4f5f7;
            padding: 12px 16px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .tech-stack span {
            display: inline-block;
            background: #fff;
            border: 1px solid #e0e0e0;
            padding: 3px 10px;
            margin: 3px;
            border-radius: 12px;
            font-size: 11px;
        }

        .intro { background: #f9f3fc; padding: 14px 20px; border-radius: 6px; margin-bottom: 20px; color: #444; }

        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #eee; color: #888; font-size: 10px; text-align: center; }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>

    {{-- ============ COVER PAGE ============ --}}
    <div class="cover">
        <img src="{{ public_path('common/img/goWOLO_logo_Black.png') }}" alt="GoWolo">
        <h1>Customization Portal</h1>
        <hr>
        <div class="sub">Feature Documentation & Scope Summary</div>
        <div class="sub" style="margin-top:4px;">Prepared for Contractor Review</div>
        <div class="meta">Generated: {{ now()->format('F d, Y') }}</div>
    </div>

    <div class="page-break"></div>

    {{-- ============ OVERVIEW ============ --}}
    <h2>1. Overview</h2>
    <div class="intro">
        The Customization Portal is a standalone Laravel 11 application that handles customization requests
        (logo, icon, app background, landing page, and other design/development work) with full workflow,
        role-based access, real-time chat, PDF documents, email notifications, and payment integration with
        the main GoWolo dashboard. Built to replace the legacy customization flow embedded in dashboardv2
        while staying fully synchronized with it for authentication, user data, and payments.
    </div>

    <h3>Tech Stack</h3>
    <div class="tech-stack">
        <span>Laravel 11</span><span>PHP 8.3</span><span>MySQL</span><span>Bootstrap 4.5</span>
        <span>jQuery</span><span>Summernote</span><span>Spatie Permission</span>
        <span>Spatie Activitylog</span><span>barryvdh/laravel-dompdf</span>
        <span>Bunny CDN Storage</span><span>SSO (HMAC)</span>
    </div>

    <h3>Architecture Highlights</h3>
    <ul>
        <li><strong>Two database connections</strong> — its own <em>customization</em> DB for portal data, plus read access to the legacy <em>gowolov2</em> DB for user credentials and site information.</li>
        <li><strong>Dual authentication</strong> — direct login for end-users (credentials verified against the main dashboard DB) and a separate portal login for internal staff, plus SSO token flow from dashboardv2.</li>
        <li><strong>Permission-based authorization</strong> — no roles. Each action checks a specific permission. Admin super-user bypass for emails starting with <em>admin@gowolo*</em>.</li>
        <li><strong>Public URLs use ULIDs (cuid)</strong> — 26-char sortable IDs instead of sequential integers, so URLs can't be scraped by incrementing numbers.</li>
        <li><strong>Bunny CDN integration</strong> — all chat files, request uploads, and documents stored privately on Bunny with signed-URL access.</li>
    </ul>

    {{-- ============ AUTHENTICATION ============ --}}
    <h2>2. Authentication &amp; Access</h2>
    <div class="feature-box">
        <strong>Direct Member Login</strong> (<em>/login</em>)<br>
        End-users log in with their existing GoWolo dashboard credentials.
        The portal validates against the main <em>dashboard_db.users</em> table — no separate user table needed.
        Same member design as dashboardv2 (Inter font, frosted glass card, purple gradient background, password toggle).
    </div>
    <div class="feature-box">
        <strong>Portal Staff Login</strong> (<em>/portal/login</em>)<br>
        Internal team (admins, supervisors, technicians) log in against the portal's own <em>portal_users</em> table
        with Spatie Permission integration.
    </div>
    <div class="feature-box">
        <strong>SSO from Dashboardv2</strong> (<em>/auth/sso?token=...</em>)<br>
        Users can click a link in the main dashboard and land inside the portal without re-logging in.
        Uses HMAC-SHA256 signed tokens with a 5-minute TTL.
    </div>
    <div class="feature-box">
        <strong>Profile &amp; Password Change</strong><br>
        Staff can change their password from the top-right dropdown. Validates current password,
        requires minimum 8 characters plus confirmation. Activity logged.
    </div>

    {{-- ============ USER MANAGEMENT ============ --}}
    <h2>3. User &amp; Permission Management</h2>
    <ul>
        <li>Add, edit, deactivate, and delete portal staff users.</li>
        <li><strong>12 fine-grained permissions</strong> rendered as a visual checkbox grid with large clickable cards:
            <ul>
                <li>view_all_requests</li>
                <li>view_assigned_requests</li>
                <li>assign_technician</li>
                <li>update_request_status</li>
                <li>edit_request</li>
                <li>manage_portal_users</li>
                <li>view_reports</li>
                <li>manage_settings</li>
                <li>view_payments</li>
                <li>send_chat</li>
                <li>view_messages</li>
                <li>view_notifications</li>
            </ul>
        </li>
        <li>Permissions are <strong>self-seeded</strong> — visiting the Add/Edit user page auto-creates any missing standard permissions in the database.</li>
        <li>Super-admin bypass — any user whose email starts with <em>admin@gowolo*</em> gets all permissions regardless of the checkboxes (enforced at the model level via an override of <em>hasPermissionTo</em>).</li>
    </ul>

    <div class="page-break"></div>

    {{-- ============ REQUEST FORM ============ --}}
    <h2>4. Customer Request Form</h2>
    <div class="feature-box">
        <strong>Multi-Type Request Form</strong><br>
        A dropdown at the top lets users choose: <em>Customization</em>, <em>Graphic Design</em>,
        <em>Web Development</em>, <em>Software Development</em>, or <em>App Development</em>.
        When <em>Customization</em> is selected the full form appears (checkboxes, colors, uploads, questionary).
        For any other type, the form simplifies to a single project-description textarea.
    </div>

    <h3>Customization Flow (Main Form)</h3>
    <ul>
        <li>Personal info (pre-filled from SSO session): first name, last name, email, phone, secondary phone</li>
        <li>Community details: name, handle, optional domain</li>
        <li>"I'm requesting for" checkboxes: Logo, Web Icon, App Background Image, Landing Page, Others</li>
        <li>Primary &amp; Secondary color pickers</li>
        <li>Request description textarea (shown when "Others" is checked)</li>
        <li><strong>File uploads</strong> with dashed drop-box UI and image previews:
            <ul>
                <li>Entity Logo (200x60)</li>
                <li>Web Icon (60x60)</li>
                <li>App Login Background (375x800)</li>
                <li>Supporting document (single)</li>
                <li>Additional files (multiple)</li>
            </ul>
        </li>
        <li><strong>Additional Features Yes/No toggle</strong> — when Yes, the Send button changes to
            "Add Additional Features" and a second phase with a <strong>17-question questionary</strong>
            (matches the legacy dashboardv2 questionnaire verbatim) appears.</li>
        <li><strong>Real-time validation</strong> — required-field errors appear inline below each field as users type or tab through.</li>
    </ul>

    {{-- ============ REQUEST MANAGEMENT ============ --}}
    <h2>5. Request Management (Staff Side)</h2>
    <h3>Request List</h3>
    <ul>
        <li>Paginated list with advanced filters: status, payment type, payment status, date range, and free-text search (by ref #, name, email, company).</li>
        <li><strong>3-dot action dropdown</strong> per row — one-click access to: View Details, Edit Request,
            Chat, View Logs, Change Status, Assign Technician, Download Quotation, Download Invoice.</li>
        <li><strong>Change Status modal</strong> (Bootstrap 4 large) with status dropdown (6 states) and comments field,
            submitted via AJAX without leaving the page.</li>
        <li><strong>Assign Technician modal</strong> with Tech 1 (required), Tech 2, Supervisor, plus a
            <strong>Payment section</strong> where the admin selects Free / Paid and enters the amount.
            When amount is set, the customer immediately sees the Pay Now button.</li>
    </ul>

    <h3>Status System (6 Stages)</h3>
    <ul>
        <li><strong>0 Pending</strong> — submitted, awaiting assignment</li>
        <li><strong>1 Assigned</strong> — technician assigned, not started</li>
        <li><strong>2 In Review</strong> — technician working on it</li>
        <li><strong>3 Sent for Review</strong> — sent back to customer</li>
        <li><strong>4 Approved</strong> — approved by customer</li>
        <li><strong>5 Completed</strong> — finalized and delivered</li>
    </ul>
    <p>Restricted users (technicians without view_all_requests permission) can only set In Review / Sent for Review.
    Full admins can set all six stages. Completing a request automatically calculates business days elapsed.</p>

    <h3>Edit Request</h3>
    <ul>
        <li>Permission-gated (<em>edit_request</em>) — shows for admins, hidden for technicians.</li>
        <li>Full edit view with all main fields <strong>and the 17-question questionary inline on the same page</strong>
            (no tabs, no separate pages).</li>
        <li>Questions are persisted via <strong>upsert</strong> — changes to answers are tracked.</li>
        <li>Every changed field is recorded to the activity log with a field-by-field diff that's
            rendered as a <strong>three-column table</strong> (Field / Old Value / New Value) in the Logs view.</li>
        <li>Customers can also edit their own requests while status is Pending or Assigned — the edit option disappears once a technician starts working.</li>
    </ul>

    <h3>Activity Logs</h3>
    <ul>
        <li>Every action is tracked via spatie/laravel-activitylog: <em>request_created</em>, <em>technician_assigned</em>,
            <em>status_changed</em>, <em>request_edited</em>, <em>chat_sent</em>.</li>
        <li>Logs view shows the actor, timestamp, and a readable diff for changes.</li>
    </ul>

    <div class="page-break"></div>

    {{-- ============ CHAT ============ --}}
    <h2>6. Real-Time Chat</h2>
    <div class="feature-box">
        <strong>Customer ↔ Staff Messaging</strong><br>
        One-to-one clone of the dashboardv2 customization chat design. Each request has its own thread
        that both the customer and the assigned staff can access.
    </div>
    <ul>
        <li><strong>5-second polling</strong> on both sides — new messages appear automatically without refresh.</li>
        <li><strong>Summernote rich editor</strong> — bold, italic, underline, bullet &amp; numbered lists, font family.</li>
        <li><strong>Reply to any message</strong> — a small Reply pill appears beside each bubble; clicking it shows a quoted preview above the editor and links the new message to the original.</li>
        <li><strong>File attachments</strong>:
            <ul>
                <li>Images — displayed inline with click-to-enlarge modal</li>
                <li>PDFs — purple download badge with file icon</li>
                <li>Other docs — generic download badge</li>
            </ul>
        </li>
        <li><strong>User avatars</strong> — real profile pictures from the dashboard_db users table, or ui-avatars.com initials fallback (purple for staff, navy for customers).</li>
        <li><strong>Send button spinner</strong> — button shows a spinning icon and "Sending…" text during the AJAX call so users know the message is in transit.</li>
        <li><strong>Read/unread tracking</strong> — flags on each message distinguish read-by-user and read-by-staff.</li>
        <li><strong>Bunny CDN storage</strong> for all chat files with automatic fallback to local disk if Bunny isn't configured.</li>
    </ul>

    {{-- ============ PAYMENT ============ --}}
    <h2>7. Payment Flow (Pay Now)</h2>
    <ul>
        <li>Admin opens the Assign Technician modal, sets <strong>Pay Type = Paid</strong>, enters the amount, and assigns.</li>
        <li>Customer's dashboard immediately shows a <strong>purple Pay Now pill</strong> with the amount next to that request.</li>
        <li>Clicking Pay Now opens dashboardv2's existing netwostore payment endpoint in a new tab with the exact URL format:<br>
            <code style="font-size:11px;">{make_payment_url}?uid={base64(email)}&amp;type=custom&amp;id={request_id}</code></li>
        <li>Payment status progression: <em>Awaiting Price → Amount Due (Pay Now button) → Payment Done</em>.</li>
        <li>On the request detail page, the Payment card shows the amount prominently and includes a Pay Now button plus Download Quotation button.</li>
        <li>After payment, a Download Invoice button replaces Download Quotation.</li>
    </ul>

    {{-- ============ PDF DOCS ============ --}}
    <h2>8. PDF Documents</h2>
    <div class="feature-box">
        <strong>Downloadable Quotation &amp; Invoice</strong><br>
        Both generated on demand via barryvdh/laravel-dompdf. Pure monochrome theme with embedded GoWolo logo.
    </div>
    <ul>
        <li><strong>Quotation PDF</strong> — available as soon as the admin sets a price. Shows
            quotation number, reference number, valid-until date (30 days), bill-to, line item with
            requirements, subtotal, total. Marked "PENDING PAYMENT".</li>
        <li><strong>Invoice PDF</strong> — available after successful payment. Shows invoice number,
            transaction ID, paid date, bill-to, line items, total. Marked "PAID" and includes a thank-you note.</li>
        <li>Role-aware access control — portal staff need view_all_requests or must be assigned to the request;
            customers can only download documents for their own requests.</li>
        <li>Download buttons appear in three places: user dashboard action dropdown, user request detail Payment card, and admin request list action dropdown.</li>
    </ul>

    {{-- ============ NOTIFICATIONS ============ --}}
    <h2>9. In-App Notifications</h2>
    <h3>Navbar Bell &amp; Message Icons</h3>
    <ul>
        <li>Two separate icons in the top navbar:
            <ul>
                <li><strong>Message icon</strong> — dropdown showing the latest 5 unread chat notifications with Reply quick-view.</li>
                <li><strong>Bell icon</strong> — right-side offcanvas panel for request-related notifications (new request, status changes).</li>
            </ul>
        </li>
        <li><strong>Red badge count</strong> on each icon showing unread count, hidden when zero.</li>
        <li><strong>10-second polling</strong> keeps counts and lists up to date.</li>
        <li>Each notification card has a Dismiss button, an action link (Review Now / View Messages), and a close (×) button.</li>
        <li><strong>Clear All</strong> button per panel.</li>
    </ul>

    <h3>Messages &amp; Notifications Inbox Pages</h3>
    <ul>
        <li>Full-page inbox at <em>/inbox/messages</em> and <em>/inbox/notifications</em>.</li>
        <li>Paginated (20 per page) — shows all items, read and unread, including dismissed ones.</li>
        <li><strong>Unread items</strong> have a light brown <em>(#fdf6ec)</em> background with a red dot and bold title; read items are white with lighter text.</li>
        <li>Clicking an item marks it read and redirects to its action URL.</li>
        <li>Permission-gated for staff (<em>view_messages</em> / <em>view_notifications</em>); customers always see their own inbox.</li>
        <li>Sidebar entries with unread count badges.</li>
    </ul>

    <h3>Email Notifications</h3>
    <ul>
        <li><strong>New request</strong> → email to configured admin address with customer details, community name, description.</li>
        <li><strong>Status update</strong> → email to the customer greeting them by first name with a purple callout showing the old-to-new status transition.</li>
        <li>Uses a single blade template that branches on event type — consistent styling across both.</li>
    </ul>

    <div class="page-break"></div>

    {{-- ============ BUG REPORTS ============ --}}
    <h2>10. Bug Reports</h2>
    <ul>
        <li>Customers submit bug reports with subject, description, and optional screenshot upload.</li>
        <li>Staff browse the reports list with unread count badge in the sidebar.</li>
        <li>Opening a report marks it as read.</li>
    </ul>

    {{-- ============ DATA MIGRATION ============ --}}
    <h2>11. Legacy Data Migration</h2>
    <div class="feature-box">
        <strong>One-Shot Migration Command</strong><br>
        <code style="font-size:11px;">php artisan customization:migrate-legacy</code>
    </div>
    <p>
        Pulls all existing customization data from the legacy dashboardv2 schema into the new portal tables:
    </p>
    <ul>
        <li><strong>Requests</strong> — mapped field by field, including legacy status codes (0/1/2 → 0/2/5), payment info, assignment, technician info.</li>
        <li><strong>Questionnaire answers</strong> from the legacy <em>customization_questions</em> table — 17 questions + 4 requirements.</li>
        <li><strong>Chat messages</strong> from <em>customization_chats</em> — with automatic sender-type detection (user_id = customer → 'user', otherwise 'portal_user') and sender_name lookup from the legacy users table.</li>
        <li><strong>File attachments</strong> from <em>customization_files</em> — preserving size, extension, and local path.</li>
        <li><strong>Dedup by origin_cust_req_id</strong> — safe to re-run, won't duplicate.</li>
        <li><strong>--dry-run flag</strong> for safe preview.</li>
        <li><strong>--fresh flag</strong> to truncate destination first.</li>
        <li>Progress bar + final summary (migrated / skipped / errors).</li>
    </ul>

    {{-- ============ UI/UX ============ --}}
    <h2>12. UI / UX Design</h2>
    <ul>
        <li><strong>Atlantis admin theme</strong> customized with purple brand color <em>#662c87</em>.</li>
        <li><strong>Full-width layout</strong> with a floating rounded-card sidebar.</li>
        <li><strong>GoWolo logo</strong> in the navbar header (white on purple).</li>
        <li>Sidebar menus are permission-aware — users only see menus they can use.</li>
        <li><strong>Mobile responsive</strong> layouts throughout.</li>
        <li>Real-time form validation with inline error messages.</li>
        <li>Clickable file references with markdown link syntax.</li>
        <li>Consistent button styles — purple pill primary, outlined secondary.</li>
        <li>Smooth transitions and loading states on all async actions.</li>
    </ul>

    {{-- ============ INTEGRATIONS ============ --}}
    <h2>13. Integrations</h2>
    <ul>
        <li><strong>dashboardv2 SSO</strong> — HMAC-signed tokens, 5-min TTL, verified on arrival.</li>
        <li><strong>dashboardv2 user table</strong> — read-only access via a secondary DB connection for login and user lookups.</li>
        <li><strong>dashboardv2 payment gateway</strong> — Pay Now button redirects to netwostore payment URL.</li>
        <li><strong>Bunny CDN</strong> — private storage zone for chat files, request uploads, and custom file downloads. Auto-fallback to local disk if not configured.</li>
        <li><strong>SMTP email</strong> — Laravel Mail with configurable driver (log, smtp, mailgun, etc.).</li>
    </ul>

    {{-- ============ SECURITY ============ --}}
    <h2>14. Security &amp; Authorization</h2>
    <ul>
        <li><strong>Permission-based access</strong> — every controller method checks a specific permission.</li>
        <li><strong>Public URLs use cuid (ULID)</strong> — 26-char unguessable identifiers, no sequential integer IDs exposed.</li>
        <li><strong>CSRF protection</strong> on all forms.</li>
        <li><strong>Session hijacking protection</strong> — session regeneration on login and logout.</li>
        <li><strong>Row-level authorization</strong> — customers can only see their own requests, technicians only their assigned requests.</li>
        <li><strong>File upload validation</strong> — MIME type, size, and extension checks on every upload.</li>
        <li><strong>Activity audit log</strong> — every sensitive action (status changes, assignments, edits) recorded with actor, timestamp, and diff.</li>
        <li><strong>Password hashing</strong> — bcrypt with 12 rounds (Laravel default).</li>
    </ul>

    <div class="footer">
        GoWolo Customization Portal — Feature Documentation<br>
        Generated on {{ now()->format('F d, Y') }} for contractor review.
    </div>

</body>
</html>
