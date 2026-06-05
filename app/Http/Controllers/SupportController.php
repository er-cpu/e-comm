<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\User;
use App\Notifications\NewSupportMessage;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:inquiry,complaint,feedback,other',
        ]);

        $message = SupportMessage::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'type' => $request->type,
            'status' => 'open',
        ]);

        User::where('role', 'admin')->each(fn($admin) => $admin->notify(new NewSupportMessage($message)));

        return redirect()->route('contact')->with('success', 'Your message has been sent. We will respond within 24 hours.');
    }

    public function show(SupportMessage $message)
    {
        abort_if($message->user_id !== auth()->id(), 403);

        return view('pages.support-ticket', compact('message'));
    }
}