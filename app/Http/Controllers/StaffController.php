<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class StaffController extends Controller
{
    // 🔹 Tampilkan semua staff (kecuali admin)
    public function index(Request $request)
    {
        $q = $request->input('q');
        $query = User::query();

        // 🔸 Jangan tampilkan akun yang sedang login
        $query->where('id_users', '!=', auth()->id());

        // 🔸 Jika ada pencarian
        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%$q%")
                    ->orWhere('username', 'like', "%$q%");
            });
        }

        $users = $query->orderBy('id_users', 'desc')->get();

        return view('staff.staff', compact('users', 'q'));
    }

    // 🔹 Halaman tambah staff
    public function create()
    {
        return view('staff.staff_create');
    }

    // 🔹 Simpan staff baru
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'nama_lengkap' => 'required|string|max:100',
            'role' => 'required|in:kasir,admin',
            'email' => 'nullable|email|max:100',
            'telepon' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nama_lengkap' => $request->nama_lengkap,
            'role' => $request->role,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'status' => $request->status,
        ]);

        return redirect()->route('staff.index')->with('success', 'Staff baru berhasil ditambah!');
    }

    // 🔹 Halaman edit staff
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('staff.staff_edit', compact('user'));
    }

    // 🔹 Ubah status aktif/nonaktif staff
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Jangan ubah admin
        if ($user->role === 'admin') {
            return redirect()->route('staff.index')->with('error', 'Status akun admin tidak dapat diubah.');
        }

        // Toggle status
        $user->status = $user->status === 'aktif' ? 'nonaktif' : 'aktif';
        $user->save();

        return redirect()->route('staff.index')->with('success', 'Status staff berhasil diperbarui!');
    }

    // 🔹 Update staff
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            // PERBAIKAN KRITIS: Menambahkan parameter keempat 'id_users'
            // Format: unique:tabel,kolom,nilai_id_yang_diabaikan,kolom_id
            'username' => 'required|unique:users,username,' . $id . ',id_users', 
            'nama_lengkap' => 'required|string|max:100',
            'role' => 'required|in:kasir,admin',
            'email' => 'nullable|email|max:100',
            'telepon' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $data = $request->only(['username', 'nama_lengkap', 'role', 'email', 'telepon', 'status']);

        // Jika password diisi, update juga password-nya
        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('staff.index')->with('success', 'Data staff berhasil diperbarui!');
    }

    // 🔹 Hapus staff
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->role == 'admin') {
            return redirect()->route('staff.index')->with('error', 'Akun admin tidak dapat dihapus.');
        }

        $user->delete();
        return redirect()->route('staff.index')->with('success', 'Staff berhasil dihapus!');
    }
}