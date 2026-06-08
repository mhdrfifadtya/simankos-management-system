<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->user()->tenant;
        if (!$tenant) {
            return response()->json(['data' => [], 'unread_count' => 0]);
        }

        $notifications = NotificationLog::query()
            ->where('tenant_id', $tenant->id)
            ->latest()
            ->paginate(20);

        $unreadCount = NotificationLog::query()
            ->where('tenant_id', $tenant->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'data' => $notifications->items(),
            'pagination' => [
                'total'        => $notifications->total(),
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
            ],
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead(Request $request, NotificationLog $notification)
    {
        $tenant = $request->user()->tenant;
        if (!$tenant || $notification->tenant_id !== $tenant->id) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan.'], 404);
        }

        $notification->update(['is_read' => true]);
        return response()->json(['message' => 'Notifikasi ditandai sudah dibaca.']);
    }

    public function markAllRead(Request $request)
    {
        $tenant = $request->user()->tenant;
        if (!$tenant) {
            return response()->json(['message' => 'Data penyewa tidak ditemukan.'], 404);
        }

        NotificationLog::query()
            ->where('tenant_id', $tenant->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'Semua notifikasi ditandai sudah dibaca.']);
    }
}
