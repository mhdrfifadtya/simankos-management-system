<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::withCount('rooms')->orderBy('name')->get();
        return view('facilities.index', compact('facilities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:facilities,name'],
        ], [
            'name.required' => 'Nama fasilitas wajib diisi.',
            'name.unique'   => 'Fasilitas dengan nama ini sudah ada.',
        ]);

        Facility::create(['name' => $request->name]);

        return back()->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function update(Request $request, Facility $facility)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:facilities,name,' . $facility->id],
        ], [
            'name.required' => 'Nama fasilitas wajib diisi.',
            'name.unique'   => 'Fasilitas dengan nama ini sudah ada.',
        ]);

        $facility->update(['name' => $request->name]);

        return back()->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(Facility $facility)
    {
        $facility->rooms()->detach();
        $facility->delete();

        return back()->with('success', 'Fasilitas berhasil dihapus.');
    }
}
