<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $eventType === 'new' ? 'New Request' : 'Status Updated' }}</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f4f5f7; margin:0; padding:30px;">
    <table align="center" width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.05);">
        <tr>
            <td style="background:#662c87;color:#fff;padding:20px 30px;">
                <h2 style="margin:0;font-size:18px;">
                    @if($eventType === 'new')
                        New Customization Request Received
                    @else
                        Customization Request Status Updated
                    @endif
                </h2>
            </td>
        </tr>
        <tr>
            <td style="padding:25px 30px;color:#333;font-size:14px;line-height:1.6;">
                @if($eventType === 'new')
                    <p>A new customization request has just been submitted.</p>
                @else
                    <p>The status of a customization request has been updated.</p>
                    @if($oldStatus && $newStatus)
                        <p>
                            <strong>Status:</strong>
                            {{ $oldStatus }} → <strong style="color:#662c87;">{{ $newStatus }}</strong>
                        </p>
                    @endif
                @endif

                <table cellpadding="6" cellspacing="0" style="width:100%;border-collapse:collapse;margin-top:15px;">
                    <tr><td style="border-bottom:1px solid #eee;width:35%;color:#777;">Reference</td><td style="border-bottom:1px solid #eee;"><strong>{{ $request->ref_number }}</strong></td></tr>
                    <tr><td style="border-bottom:1px solid #eee;color:#777;">Customer</td><td style="border-bottom:1px solid #eee;">{{ $request->first_name }} {{ $request->last_name }}</td></tr>
                    <tr><td style="border-bottom:1px solid #eee;color:#777;">Email</td><td style="border-bottom:1px solid #eee;">{{ $request->email }}</td></tr>
                    <tr><td style="border-bottom:1px solid #eee;color:#777;">Phone</td><td style="border-bottom:1px solid #eee;">{{ $request->phone }}</td></tr>
                    <tr><td style="border-bottom:1px solid #eee;color:#777;">Company</td><td style="border-bottom:1px solid #eee;">{{ $request->company_name }}</td></tr>
                    <tr><td style="color:#777;">Submitted</td><td>{{ $request->created_at->format('M d, Y H:i') }}</td></tr>
                </table>

                @if($request->request_description)
                    <p style="margin-top:15px;"><strong>Description:</strong><br>{{ $request->request_description }}</p>
                @endif
            </td>
        </tr>
        <tr>
            <td style="background:#fafafa;padding:15px 30px;color:#888;font-size:12px;text-align:center;">
                This is an automated notification from {{ config('app.name') }}.
            </td>
        </tr>
    </table>
</body>
</html>
