<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query();

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($relatedType = $request->input('related_type')) {
            $query->where('related_type', $relatedType);
        }

        if ($userId = $request->input('user_id')) {
            $query->where('performed_by', $userId);
        }

        $activities = $query->orderBy('occurred_at', 'desc')->paginate(20)->withQueryString();
        $users = User::all();

        return view('activities.index', compact('activities', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'         => 'required|string',
            'subject'      => 'required|string|max:255',
            'description'  => 'required|string',
            'related_type' => 'required|string|in:lead,deal,customer,ticket',
            'related_id'   => 'required|string',
        ]);

        Activity::create([
            'type'              => $validated['type'],
            'subject'           => $validated['subject'],
            'description'       => $validated['description'],
            'related_type'      => $validated['related_type'],
            'related_id'        => $validated['related_id'],
            'performed_by'      => auth()->id(),
            'performed_by_name' => auth()->user()->name,
            'occurred_at'       => now(),
        ]);

        return back()->with('success', 'Activity logged!');
    }
}
