<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Offer;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->appNotifications()->latest('id')->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /** Tandai dibaca lalu buka tujuan notifikasi */
    public function open(Request $request, UserNotification $notification)
    {
        abort_unless($notification->user_id === $request->user()->id, 403);
        $notification->update(['is_read' => true]);

        return redirect()->to($notification->targetUrl());
    }

    public function readAll(Request $request)
    {
        $request->user()->appNotifications()->where('is_read', false)->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /** Angka badge navbar (dipolling oleh main.js) */
    public function counts(Request $request)
    {
        $uid = $request->user()->id;

        return response()->json([
            'success' => true,
            'messages_count' => Message::where('receiver_id', $uid)->where('is_read', false)->count(),
            'offers_count' => Offer::where('receiver_id', $uid)->where('status', Offer::PENDING)->count(),
            'notifications_count' => UserNotification::where('user_id', $uid)->where('is_read', false)->count(),
        ]);
    }
}
