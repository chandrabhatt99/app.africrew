<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTicketController extends Controller
{
    public function index(): View
    {
        $tickets = SupportTicket::with('user')->latest()->get();
        return view('admin.tickets.index', compact('tickets'));
    }

    public function respond(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'admin_response' => ['required', 'string'],
            'status' => ['required', 'in:open,in_progress,resolved,closed'],
        ]);

        $ticket->update([
            'admin_response' => $data['admin_response'],
            'status' => $data['status'],
        ]);

        return back()->with('success', "Ticket #{$ticket->ticket_number} updated successfully!");
    }
}
