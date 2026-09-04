<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class TicketApiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Ticket::query();

        if ($user->isCustomer()) {
            $query->where('customer_id', (string) $user->id);
        }

        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    public function messages(string $id)
    {
        $messages = TicketMessage::where('ticket_id', $id)->orderBy('created_at', 'asc')->get();
        return response()->json($messages);
    }
}
