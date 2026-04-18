<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Customization Portal — Work Overview</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 30px 40px; color: #222; font-size: 12.5px; line-height: 1.55; }

        /* Cover */
        .cover { text-align: center; padding: 50px 20px 30px; }
        .cover img.logo { height: 60px; margin-bottom: 20px; }
        .cover h1 { color: #662c87; font-size: 28px; margin: 0 0 8px; letter-spacing: 0.5px; }
        .cover .sub { color: #666; font-size: 13px; margin-bottom: 6px; }
        .cover hr { border: none; border-top: 2px solid #662c87; width: 60px; margin: 12px auto; }
        .cover .meta { color: #999; font-size: 11px; margin-top: 25px; }

        .budget-box {
            display: inline-block;
            background: #f9f3fc;
            border: 2px solid #662c87;
            border-radius: 8px;
            padding: 14px 30px;
            margin-top: 20px;
        }
        .budget-box .budget-label { color: #666; font-size: 11px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 4px; }
        .budget-box .budget-amount { color: #662c87; font-size: 32px; font-weight: 700; margin: 0; }

        h2 {
            color: #662c87; font-size: 17px; margin-top: 22px; margin-bottom: 10px;
            padding-bottom: 5px; border-bottom: 2px solid #662c87;
        }

        ul { margin: 6px 0 12px 20px; padding: 0; }
        li { margin-bottom: 5px; }
        li strong { color: #333; }

        .section { page-break-inside: avoid; margin-bottom: 14px; }

        /* Added scope — highlighted box */
        .added-scope {
            background: #f9f3fc;
            border: 2px solid #662c87;
            border-radius: 8px;
            padding: 18px 22px;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .added-scope h2 {
            color: #662c87;
            border-bottom: none;
            padding-bottom: 0;
            margin-top: 0;
            margin-bottom: 12px;
        }
        .added-scope .badge {
            display: inline-block;
            background: #662c87;
            color: #fff;
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 3px;
            margin-left: 10px;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }
        .added-scope ul li { margin-bottom: 8px; }
        .added-scope ul li strong { color: #662c87; }

        .intro {
            background: #f4f5f7;
            padding: 14px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            color: #444;
            border-left: 4px solid #662c87;
        }

        .footer {
            margin-top: 30px; padding-top: 15px;
            border-top: 1px solid #eee;
            color: #888; font-size: 10px; text-align: center;
        }

        .page-break { page-break-before: always; }

        /* Screenshots */
        .screenshot-gallery h2 { page-break-before: always; }
        .shot {
            margin: 15px 0 25px;
            text-align: center;
            page-break-inside: avoid;
        }
        .shot img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .shot .caption {
            margin-top: 8px;
            font-size: 11px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>

    {{-- ============ COVER ============ --}}
    <div class="cover">
        <img class="logo" src="{{ public_path('common/img/goWOLO_logo_Black.png') }}" alt="GoWolo">
        <h1>Customization Portal</h1>
        <hr>
        <div class="sub">Work Overview &amp; Scope Summary</div>
        <div class="sub" style="margin-top:2px;">Prepared for Client Review</div>

        <div class="budget-box">
            <div class="budget-label">Project Budget</div>
            <div class="budget-amount">$1,020 USD</div>
        </div>

        <div class="meta">Generated: {{ now()->format('F d, Y') }}</div>
    </div>

    {{-- ============ INTRO ============ --}}
    <div class="intro">
        The GoWolo Customization Portal is a dedicated web application that handles the full
        customization request workflow — from submission to delivery — with built-in chat, payments,
        PDF documents, and email notifications. It integrates seamlessly with the main dashboard.
    </div>

    {{-- ============ CORE WORK ============ --}}
    <h2>1. Authentication &amp; Access</h2>
    <ul>
        <li>Direct customer login with existing GoWolo account credentials</li>
        <li>Separate staff login for the internal team</li>
        <li>One-click SSO from the main dashboard (signed token, 5-min expiry)</li>
        <li>Password change from profile dropdown</li>
        <li>12 fine-grained permissions for staff, assigned via checkbox grid</li>
    </ul>

    <h2>2. Customization Request Form</h2>
    <ul>
        <li>Single-page request form with all required fields and file uploads</li>
        <li>Logo, Web Icon, App Background, Landing Page, and Other checkboxes</li>
        <li>Primary / Secondary color inputs</li>
        <li>Drag-and-drop thumbnail uploads with live previews</li>
        <li>Optional 17-question questionary (shown on demand)</li>
        <li>Real-time inline validation on every field</li>
        <li>Welcome image with "Proceed to Customization" button on first visit</li>
    </ul>

    <h2>3. Request Workflow (Staff Side)</h2>
    <ul>
        <li>Request list with filters: status, payment, date range, text search</li>
        <li>3-dot action dropdown per row (View / Edit / Chat / Logs / Status / Assign)</li>
        <li>Modal-based Change Status and Assign Technician with amount entry</li>
        <li>6-stage status flow: Pending → Assigned → In Review → Sent for Review → Approved by Team → Approved → Completed</li>
        <li>Full request edit view (permission-gated) with all main fields and questionary inline</li>
        <li>Activity log with a clear Field / Old / New diff table for every change</li>
    </ul>

    <h2>4. Chat &amp; File Sharing</h2>
    <ul>
        <li>Real-time chat between customer and assigned staff (5-second polling)</li>
        <li>Rich text editor: bold, italic, underline, bullet &amp; numbered lists</li>
        <li>Reply-to-any-message with quoted preview</li>
        <li>Image inline preview + PDF / document download</li>
        <li>User avatars (real profile picture or initials fallback)</li>
        <li>Sending spinner so users know their message is in transit</li>
    </ul>

    <h2>5. Payment &amp; Documents</h2>
    <ul>
        <li>Admin sets the price when assigning — customer sees "Pay Now" instantly</li>
        <li>Pay Now redirects to the existing payment gateway (netwostore)</li>
        <li>Downloadable <strong>Quotation PDF</strong> (once price is set)</li>
        <li>Downloadable <strong>Invoice PDF</strong> (after payment)</li>
        <li>Both PDFs use the brand logo and monochrome professional theme</li>
    </ul>

    <h2>6. Email Notifications</h2>
    <ul>
        <li>New-request email to admin when a request is submitted</li>
        <li>Status-change email to customer with old → new transition highlighted</li>
        <li>Professional branded email template</li>
    </ul>

    <h2>7. Data Migration &amp; Security</h2>
    <ul>
        <li>One-command migration pulls all historic requests, chats, files, and answers from the old system</li>
        <li>Public URLs use unpredictable ULIDs instead of sequential IDs</li>
        <li>Every sensitive action recorded to the activity audit log</li>
        <li>Row-level authorization — customers only see their own requests, staff only their assignments</li>
    </ul>

    <div class="page-break"></div>

    {{-- ============ ADDED SCOPE OF WORK ============ --}}
    <div class="added-scope">
        <h2>Added Scope of Work <span class="badge">NEW</span></h2>
        <p style="margin-top:0;color:#555;">
            The following features were added beyond the original scope at the client's request
            to enhance the portal's capabilities:
        </p>
        <ul>
            <li>
                <strong>Bug Report System</strong> — customers can submit bug reports with screenshots.
                Staff can review, assign status (In Review / Duplicated / Rejected / Approved) and
                send written feedback visible to the reporting user.
            </li>
            <li>
                <strong>Bug Report Analytics</strong> — dashboard with summary cards (Total, Approved,
                Duplicated, Rejected, In Review) plus a pie chart of the Top 5 reporters, plus filters
                by user, status, and search.
            </li>
            <li>
                <strong>Client Approval Flow</strong> — when the team marks work as "Approved by Team",
                the customer sees a prominent "Approve the Work" button on the request detail page
                (with Bootstrap confirmation modal). Customer approval notifies staff and advances the
                request to the final Approved state.
            </li>
            <li>
                <strong>Multiple Request Types</strong> — request form now supports six types:
                Customization, Graphic Design, Web Development, Software Development, App Development,
                and Gift &amp; Monetization Session. The form adapts dynamically — Customization shows
                the full design form; other types show a simplified description field.
            </li>
            <li>
                <strong>Quotation &amp; Invoice PDFs</strong> — one-click downloadable PDF documents.
                Quotation appears as soon as the admin sets a price (marked "Pending Payment"); after
                successful payment, an Invoice PDF is generated (marked "Paid"). Both use the GoWolo
                logo and a clean monochrome professional theme.
            </li>

        </ul>
    </div>

    {{-- ============ SCREENSHOTS ============ --}}
    <div class="screenshot-gallery">
        <h2>Screenshots</h2>

        @php
            $screenshots = [
                ['file' => 'file1.PNG', 'caption' => 'Messages Inbox — paginated list with light brown highlight for unread items'],
                ['file' => 'file2.PNG', 'caption' => 'Notifications Inbox — separate page for request notifications'],
                ['file' => 'file3.PNG', 'caption' => 'Bug Report Analytics — summary cards, Top 5 reporters pie chart, filters'],
                ['file' => 'file6.PNG', 'caption' => 'Generated Quotation PDF with GoWolo branding'],
            ];
        @endphp

        @foreach($screenshots as $s)
            @php $path = public_path($s['file']); @endphp
            @if(file_exists($path))
                <div class="shot">
                    <img src="{{ $path }}" alt="{{ $s['caption'] }}">
                    <div class="caption">{{ $s['caption'] }}</div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="footer">
        GoWolo Customization Portal — Work Overview<br>
        Generated on {{ now()->format('F d, Y') }} for client review.
    </div>

</body>
</html>
