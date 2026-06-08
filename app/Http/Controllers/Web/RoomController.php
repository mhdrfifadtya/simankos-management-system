<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\RoomRequest;
use App\Models\Facility;
use App\Models\Room;
use App\Models\RoomPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with(['primaryPhoto', 'activeRental.tenant', 'facilities']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $rooms = $query->paginate(12);

        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        $facilities = Facility::orderBy('name')->get();
        return view('rooms.create', compact('facilities'));
    }

    public function store(RoomRequest $request)
    {
        $room = Room::create($request->safe()->only([
            'room_number', 'type', 'price',
            'area', 'floor', 'status', 'description',
        ]));

        $room->facilities()->sync($request->input('facility_ids', []));

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $this->savePhoto($photo);
                RoomPhoto::create([
                    'room_id'    => $room->id,
                    'photo_path' => $path,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return redirect()->route('rooms.show', $room)
            ->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function show(Room $room)
    {
        $room->load(['photos', 'activeRental.tenant', 'facilities']);
        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $room->load(['photos', 'facilities']);
        $facilities = Facility::orderBy('name')->get();
        return view('rooms.edit', compact('room', 'facilities'));
    }

    public function update(RoomRequest $request, Room $room)
    {
        $room->update($request->safe()->only([
            'room_number', 'type', 'price',
            'area', 'floor', 'status', 'description',
        ]));

        $room->facilities()->sync($request->input('facility_ids', []));

        if ($request->hasFile('photos')) {
            $hasPrimary = RoomPhoto::query()
                ->where('room_id', '=', $room->id)
                ->where('is_primary', true)
                ->exists();

            foreach ($request->file('photos') as $index => $photo) {
                $path = $this->savePhoto($photo);
                $isPrimary = !$hasPrimary && $index === 0;
                RoomPhoto::create([
                    'room_id'    => $room->id,
                    'photo_path' => $path,
                    'is_primary' => $isPrimary,
                ]);
                if ($isPrimary) {
                    $hasPrimary = true;
                }
            }
        }

        return redirect()->route('rooms.show', $room)
            ->with('success', 'Kamar berhasil diperbarui.');
    }

    public function destroy(Room $room)
    {
        if ($room->status === 'terisi') {
            return back()->with('error', 'Kamar sedang terisi, tidak bisa dihapus.');
        }
        $room->facilities()->detach();
        foreach ($room->photos as $photo) {
            $this->deletePhotoFile($photo->photo_path);
        }
        Room::destroy($room->id);
        return redirect()->route('rooms.index')
            ->with('success', 'Kamar berhasil dihapus.');
    }

    public function setPrimaryPhoto(RoomPhoto $photo)
    {
        RoomPhoto::query()->where('room_id', '=', $photo->room_id)->update(['is_primary' => false]);
        $photo->update(['is_primary' => true]);
        return back()->with('success', 'Foto utama berhasil diubah.');
    }

    public function deletePhoto(RoomPhoto $photo)
    {
        $this->deletePhotoFile($photo->photo_path);
        RoomPhoto::destroy($photo->id);

        $remaining = RoomPhoto::query()->where('room_id', '=', $photo->room_id)->first();
        if ($remaining && !$remaining->is_primary) {
            $remaining->update(['is_primary' => true]);
        }

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    private function savePhoto(UploadedFile $file): string
    {
        $name = time() . '_' . uniqid() . '.' . strtolower($file->getClientOriginalExtension());
        $file->move(public_path('uploads/rooms'), $name);
        return "rooms/{$name}";
    }

    private function deletePhotoFile(?string $path): void
    {
        if ($path) {
            $full = public_path("uploads/{$path}");
            if (file_exists($full)) {
                @unlink($full);
            }
        }
    }
}
