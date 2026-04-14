<?php

namespace App\Http\Controllers;

use App\Models\CustomizationRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

/**
 * Generate downloadable PDF documents (quotation before payment, invoice after).
 * Works for both portal staff and SSO users — authorization is checked per role.
 */
class DocumentController extends Controller
{
    /**
     * GET /documents/quotation/{cuid}
     * Downloads a quotation PDF — available once the admin sets a price
     * (pay_type=2 and pay_amount > 0), regardless of payment status.
     */
    public function quotation(string $cuid)
    {
        $request = CustomizationRequest::where('cuid', $cuid)->firstOrFail();

        $this->authorizeAccess($request);

        abort_unless(
            $request->pay_type == 2 && $request->pay_amount > 0,
            404,
            'No quotation available for this request.'
        );

        $pdf = Pdf::loadView('pdf.quotation', ['request' => $request]);
        return $pdf->download('Quotation-' . $request->ref_number . '.pdf');
    }

    /**
     * GET /documents/invoice/{cuid}
     * Downloads an invoice PDF — only available after payment (pay_status=1).
     */
    public function invoice(string $cuid)
    {
        $request = CustomizationRequest::where('cuid', $cuid)->firstOrFail();

        $this->authorizeAccess($request);

        abort_unless(
            $request->pay_status == 1,
            404,
            'No invoice available — this request has not been paid yet.'
        );

        $pdf = Pdf::loadView('pdf.invoice', ['request' => $request]);
        return $pdf->download('Invoice-' . $request->ref_number . '.pdf');
    }

    /**
     * Either a portal staff member (owner or with view_all_requests permission)
     * or the SSO user who created the request can download.
     */
    private function authorizeAccess(CustomizationRequest $request): void
    {
        if (Auth::guard('portal')->check()) {
            $user = Auth::guard('portal')->user();
            if ($user->hasPermissionTo('view_all_requests')) return;
            if ($request->assigned_tech_id1 == $user->id || $request->assigned_tech_id2 == $user->id) return;
            abort(403, 'You are not authorized to access this document.');
        }

        if (session()->has('auth_user')) {
            if ($request->user_id == session('auth_user.user_id')) return;
            abort(403, 'You are not authorized to access this document.');
        }

        abort(403);
    }
}
