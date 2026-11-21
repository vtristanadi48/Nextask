<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\View\View;

class AdminKelasController extends Controller
{
    private const TABS = ['guru', 'siswa', 'kelas', 'jurusan', 'mapel'];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'tab' => ['nullable', 'string', Rule::in(self::TABS)],
            'search' => ['nullable', 'string', 'max:100'],
        ]);
        $tab = $filters['tab'] ?? 'guru';
        $search = trim($filters['search'] ?? '');
        $fields = $this->formFieldsFor($tab);

        return view('admin.kelas', [
            'tab' => $tab,
            'tabs' => [
                'guru' => 'Guru',
                'siswa' => 'Siswa',
                'kelas' => 'Kelas',
                'jurusan' => 'Jurusan',
                'mapel' => 'Mata Pelajaran',
            ],
            'tabLabel' => [
                'guru' => 'Guru',
                'siswa' => 'Siswa',
                'kelas' => 'Kelas',
                'jurusan' => 'Jurusan',
                'mapel' => 'Mata Pelajaran',
            ][$tab],
            'search' => $search,
            'records' => $this->recordsFor($tab, $search),
            'formFields' => $fields,
        ]);
    }

    public function store(Request $request, string $tab): RedirectResponse
    {
        abort_unless(in_array($tab, self::TABS, true), 404);

        $data = $this->validatedData($request, $tab);

        DB::transaction(function () use ($data, $tab): void {
            match ($tab) {
                'guru' => $this->createAccount($data, 'guru'),
                'siswa' => $this->createAccount($data, 'siswa'),
                'kelas' => Kelas::query()->create($data),
                'jurusan' => Jurusan::query()->create($data),
                'mapel' => MataPelajaran::query()->create($data),
            };
        });

        return $this->redirectToTab($tab)->with('status', 'Data berhasil ditambahkan.');
    }

    public function update(Request $request, string $tab, int $id): RedirectResponse
    {
        abort_unless(in_array($tab, self::TABS, true), 404);

        $record = $this->findRecord($tab, $id);
        $data = $this->validatedData($request, $tab, $record);

        DB::transaction(function () use ($data, $record, $tab): void {
            match ($tab) {
                'guru' => $this->updateAccount($record, $data, 'guru'),
                'siswa' => $this->updateAccount($record, $data, 'siswa'),
                default => $record->update($data),
            };
        });

        return $this->redirectToTab($tab)->with('status', 'Data berhasil diperbarui.');
    }

    public function destroy(string $tab, int $id): RedirectResponse
    {
        abort_unless(in_array($tab, self::TABS, true), 404);

        $deleted = DB::transaction(function () use ($id, $tab): bool {
            $record = $this->findRecord($tab, $id);

            if ($record instanceof Jurusan) {
                $record = Jurusan::query()->lockForUpdate()->findOrFail($id);

                if ($record->kelas()->exists()) {
                    return false;
                }
            }

            $record->delete();

            return true;
        });

        if (! $deleted) {
            return $this->redirectToTab($tab)
                ->withErrors(['delete' => 'Jurusan tidak dapat dihapus karena masih digunakan oleh kelas.']);
        }

        return $this->redirectToTab($tab)->with('status', 'Data berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, string $tab, ?EloquentModel $record = null): array
    {
        $data = $request->validate($this->rulesFor($tab, $record), [
            'required' => 'Kolom :attribute wajib diisi.',
            'email' => 'Masukkan alamat email yang valid.',
            'unique' => 'Nilai :attribute sudah digunakan.',
            'exists' => 'Pilihan :attribute tidak valid.',
            'min' => 'Kata sandi harus memiliki minimal :min karakter.',
            'digits' => ':attribute harus terdiri dari :digits digit.',
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.digits' => 'NISN harus terdiri dari 10 digit angka.',
            'nisn.unique' => 'NISN ini sudah terdaftar pada siswa lain.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.digits' => 'NIP harus terdiri dari 18 digit angka.',
            'nip.unique' => 'NIP ini sudah terdaftar pada guru lain.',
        ]);

        return $data;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rulesFor(string $tab, ?EloquentModel $record = null): array
    {
        $userEmailRule = Rule::unique('users', 'email');
        $profileUniqueRule = null;

        if ($record instanceof User) {
            $userEmailRule->ignore($record);
            $profile = $tab === 'guru' ? $record->guruProfile : $record->siswaProfile;
            $profileUniqueRule = $tab === 'guru'
                ? Rule::unique('guru_profiles', 'nip')
                : Rule::unique('siswa_profiles', 'nisn');

            if ($profile !== null) {
                $profileUniqueRule->ignore($profile);
            }
        }

        return match ($tab) {
            'guru' => [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', $userEmailRule],
                'password' => $record === null
                    ? ['required', 'string', 'min:8']
                    : ['nullable', 'string', 'min:8'],
                'nip' => ['required', 'digits:18', $profileUniqueRule ?? Rule::unique('guru_profiles', 'nip')],
                'no_hp' => ['nullable', 'string', 'max:30'],
            ],
            'siswa' => [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', $userEmailRule],
                'password' => $record === null
                    ? ['required', 'string', 'min:8']
                    : ['nullable', 'string', 'min:8'],
                'nisn' => ['required', 'digits:10', $profileUniqueRule ?? Rule::unique('siswa_profiles', 'nisn')],
            ],
            'kelas' => [
                'nama_kelas' => ['required', 'string', 'max:255', $this->uniqueRule('kelas', 'nama_kelas', $record)],
                'tingkat' => ['required', 'string'],
            ],
            'jurusan' => [
                'kode_jurusan' => ['required', 'string', 'max:30', $this->uniqueRule('jurusans', 'kode_jurusan', $record)],
                'nama_jurusan' => ['required', 'string', 'max:255'],
            ],
            'mapel' => [
                'kode_mapel' => ['required', 'string', 'max:30', $this->uniqueRule('mata_pelajarans', 'kode_mapel', $record)],
                'nama_mapel' => ['required', 'string', 'max:255'],
            ],
        };
    }

    private function uniqueRule(string $table, string $column, ?EloquentModel $record): Unique
    {
        $rule = Rule::unique($table, $column);

        if ($record !== null) {
            $rule->ignore($record);
        }

        return $rule;
    }

    private function createAccount(array $data, string $roleName): void
    {
        $role = Role::query()->where('name', $roleName)->firstOrFail();
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role_id' => $role->getKey(),
        ]);

        if ($roleName === 'guru') {
            $user->guruProfile()->create(Arr::only($data, ['nip', 'no_hp']));

            return;
        }

        $user->siswaProfile()->create(Arr::only($data, ['nisn']));
    }

    private function updateAccount(EloquentModel $record, array $data, string $roleName): void
    {
        if (! $record instanceof User) {
            abort(404);
        }

        $record->fill(Arr::only($data, ['name', 'email']));

        if (filled($data['password'] ?? null)) {
            $record->password = $data['password'];
        }

        $record->save();

        if ($roleName === 'guru') {
            $record->guruProfile()->updateOrCreate([], Arr::only($data, ['nip', 'no_hp']));

            return;
        }

        $record->siswaProfile()->updateOrCreate([], Arr::only($data, ['nisn']));
    }

    private function findRecord(string $tab, int $id): EloquentModel
    {
        return match ($tab) {
            'guru' => User::query()
                ->whereHas('role', fn (Builder $query): Builder => $query->where('name', 'guru'))
                ->with('guruProfile')
                ->findOrFail($id),
            'siswa' => User::query()
                ->whereHas('role', fn (Builder $query): Builder => $query->where('name', 'siswa'))
                ->with('siswaProfile')
                ->findOrFail($id),
            'kelas' => Kelas::query()->findOrFail($id),
            'jurusan' => Jurusan::query()->findOrFail($id),
            'mapel' => MataPelajaran::query()->findOrFail($id),
            default => abort(404),
        };
    }

    private function recordsFor(string $tab, string $search): LengthAwarePaginator
    {
        $query = match ($tab) {
            'guru' => User::query()
                ->whereHas('role', fn (Builder $query): Builder => $query->where('name', 'guru'))
                ->with(['guruProfile.waliKelas', 'guruProfile.kelasMapels.kelas', 'guruProfile.kelasMapels.mataPelajaran']),
            'siswa' => User::query()
                ->whereHas('role', fn (Builder $query): Builder => $query->where('name', 'siswa'))
                ->with(['siswaProfile.kelas']),
            'kelas' => Kelas::query()->with(['jurusan', 'waliKelas.user'])->withCount('siswaProfiles'),
            'jurusan' => Jurusan::query()->withCount('kelas'),
            'mapel' => MataPelajaran::query()->withCount('guruKelasMapels'),
        };

        if ($search !== '') {
            $pattern = '%'.$search.'%';
            $query->where(function (Builder $query) use ($pattern, $tab): void {
                match ($tab) {
                    'guru' => $query
                        ->where('name', 'like', $pattern)
                        ->orWhere('email', 'like', $pattern)
                        ->orWhereHas('guruProfile', fn (Builder $profile): Builder => $profile
                            ->where('nip', 'like', $pattern)
                            ->orWhere('no_hp', 'like', $pattern)),
                    'siswa' => $query
                        ->where('name', 'like', $pattern)
                        ->orWhere('email', 'like', $pattern)
                        ->orWhereHas('siswaProfile', fn (Builder $profile): Builder => $profile
                            ->where('nisn', 'like', $pattern)
                            ->orWhereHas('kelas', fn (Builder $kelas): Builder => $kelas->where('nama_kelas', 'like', $pattern))),
                    'kelas' => $query
                        ->where('nama_kelas', 'like', $pattern)
                        ->orWhere('tingkat', 'like', $pattern)
                        ->orWhereHas('jurusan', fn (Builder $jurusan): Builder => $jurusan
                            ->where('nama_jurusan', 'like', $pattern)
                            ->orWhere('kode_jurusan', 'like', $pattern)),
                    'jurusan' => $query
                        ->where('nama_jurusan', 'like', $pattern)
                        ->orWhere('kode_jurusan', 'like', $pattern),
                    'mapel' => $query
                        ->where('nama_mapel', 'like', $pattern)
                        ->orWhere('kode_mapel', 'like', $pattern),
                };
            });
        }

        return $query->orderByDesc($query->getModel()->getQualifiedKeyName())
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * @return array<int, array{name: string, label: string, type: string, required?: bool, options?: array<int|string, string>, placeholder?: string}>
     */
    private function formFieldsFor(string $tab): array
    {
        return match ($tab) {
            'guru' => [
                ['name' => 'name', 'label' => 'Nama Lengkap', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['name' => 'nip', 'label' => 'NIP', 'type' => 'text', 'required' => true, 'placeholder' => '18 digit NIP'],
                ['name' => 'no_hp', 'label' => 'No. HP', 'type' => 'tel'],
                ['name' => 'password', 'label' => 'Kata Sandi', 'type' => 'password', 'required' => true],
            ],
            'siswa' => [
                ['name' => 'name', 'label' => 'Nama Lengkap', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['name' => 'nisn', 'label' => 'NISN', 'type' => 'text', 'required' => true, 'placeholder' => '10 digit'],
                ['name' => 'password', 'label' => 'Kata Sandi', 'type' => 'password', 'required' => true],
            ],
            'kelas' => [
                ['name' => 'nama_kelas', 'label' => 'Nama Kelas', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: X RPL 1'],
                ['name' => 'tingkat', 'label' => 'Tingkat', 'type' => 'select', 'required' => true, 'options' => ['X' => 'X', 'XI' => 'XI', 'XII' => 'XII']],
            ],
            'jurusan' => [
                ['name' => 'kode_jurusan', 'label' => 'Kode Jurusan', 'type' => 'text', 'required' => true],
                ['name' => 'nama_jurusan', 'label' => 'Nama Jurusan', 'type' => 'text', 'required' => true],
            ],
            'mapel' => [
                ['name' => 'kode_mapel', 'label' => 'Kode Mata Pelajaran', 'type' => 'text', 'required' => true],
                ['name' => 'nama_mapel', 'label' => 'Nama Mata Pelajaran', 'type' => 'text', 'required' => true],
            ],
        };
    }

    private function redirectToTab(string $tab): RedirectResponse
    {
        return redirect()->route('admin.kelas', ['tab' => $tab]);
    }
}
