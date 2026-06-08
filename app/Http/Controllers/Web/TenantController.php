<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\TenantRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::with(['user', 'activeRental.room']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('whatsapp_number', 'like', "%{$search}%");
            });
        }

        $tenants = $query->latest()->paginate(15);
        return view('tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('tenants.create');
    }

    public function store(TenantRequest $request)
    {
        // Buat akun user login
        $user = User::create([
            'name'            => $request->full_name,
            'email'           => $request->email,
            'password'        => Hash::make($request->filled('password') ? $request->password : 'Penyewa@123'),
            'role'            => 'penghuni',
            'phone'           => $request->whatsapp_number,
            'whatsapp_number' => $request->whatsapp_number,
            'status'          => 'aktif',
        ]);
        $user->assignRole('penghuni');

        // Data tenant — hanya kolom yang ada di tabel tenants
        $tenantData = [
            'user_id'             => $user->id,
            'full_name'           => $request->full_name,
            'nik'                 => $request->nik,
            'whatsapp_number'     => $request->whatsapp_number,
            'address'             => $request->address,
            'emergency_contact'   => $request->emergency_contact,
            'emergency_phone'     => $request->emergency_phone,
            'notify_via_whatsapp' => $request->boolean('notify_via_whatsapp'),
            'notify_via_email'    => $request->boolean('notify_via_email'),
            'notes'               => $request->notes,
            'status'              => 'aktif',
        ];

        if ($request->hasFile('id_card_photo')) {
            $tenantData['id_card_photo'] = $this->saveFile($request->file('id_card_photo'), 'tenants/ktp');
        }

        if ($request->hasFile('profile_photo')) {
            $tenantData['profile_photo'] = $this->saveFile($request->file('profile_photo'), 'tenants/photo');
        }

        $tenant = Tenant::create($tenantData);

        return redirect()->route('tenants.show', $tenant)
            ->with('success', 'Penyewa berhasil ditambahkan. Password default: Penyewa@123');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['user', 'rentals.room', 'invoices', 'payments']);
        return view('tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        return view('tenants.edit', compact('tenant'));
    }

    public function update(TenantRequest $request, Tenant $tenant)
    {
        $data = [
            'full_name'           => $request->full_name,
            'nik'                 => $request->nik,
            'whatsapp_number'     => $request->whatsapp_number,
            'address'             => $request->address,
            'emergency_contact'   => $request->emergency_contact,
            'emergency_phone'     => $request->emergency_phone,
            'notify_via_whatsapp' => $request->boolean('notify_via_whatsapp'),
            'notify_via_email'    => $request->boolean('notify_via_email'),
            'notes'               => $request->notes,
        ];

        if ($request->hasFile('id_card_photo')) {
            $this->deleteFile($tenant->id_card_photo);
            $data['id_card_photo'] = $this->saveFile($request->file('id_card_photo'), 'tenants/ktp');
        }

        if ($request->hasFile('profile_photo')) {
            $this->deleteFile($tenant->profile_photo);
            $data['profile_photo'] = $this->saveFile($request->file('profile_photo'), 'tenants/photo');
        }

        $tenant->update($data);

        $tenant->user?->update([
            'name'  => $data['full_name'],
            'email' => $request->email,
        ]);

        return redirect()->route('tenants.show', $tenant)
            ->with('success', 'Data penyewa berhasil diperbarui.');
    }

    public function deactivate(Tenant $tenant)
    {
        $tenant->update(['status' => 'nonaktif']);
        $tenant->user?->update(['status' => 'nonaktif']);
        return back()->with('success', 'Penyewa berhasil dinonaktifkan.');
    }

    public function destroy(Tenant $tenant)
    {
        if ($tenant->activeRental) {
            return back()->with('error', 'Penyewa tidak dapat dihapus karena memiliki kontrak aktif. Akhiri kontrak terlebih dahulu.');
        }

        $this->deleteFile($tenant->id_card_photo);
        $this->deleteFile($tenant->profile_photo);

        $tenant->user?->delete();
        $tenant->delete();

        return redirect()->route('tenants.index')
            ->with('success', 'Penyewa berhasil dihapus dari sistem.');
    }

    private function saveFile(UploadedFile $file, string $folder): string
    {
        $name = time() . '_' . uniqid() . '.' . strtolower($file->getClientOriginalExtension());
        $file->move(public_path("uploads/{$folder}"), $name);
        return "{$folder}/{$name}";
    }

    private function deleteFile(?string $relativePath): void
    {
        if ($relativePath) {
            $full = public_path("uploads/{$relativePath}");
            if (file_exists($full)) {
                @unlink($full);
            }
        }
    }
}
