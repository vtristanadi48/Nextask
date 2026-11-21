@extends('layouts.dashboard')

@section('title', 'Pusat Master Data Kelas - Nextask')

@section('content')
    @php
        $updateUrlTemplate = route('admin.kelas.update', ['tab' => $tab, 'id' => '__record__']);
        $storeUrl = route('admin.kelas.store', ['tab' => $tab]);
    @endphp

    <section class="mx-auto flex max-w-7xl flex-col gap-6">
        <header class="rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-6 shadow-sm sm:p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Master Data</p>
            <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div class="flex max-w-3xl flex-col gap-2">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Pusat Master Data Kelas</h1>
                    <p class="text-sm leading-relaxed text-slate-600 sm:text-base">
                        Kelola data guru, siswa, kelas, jurusan, dan mata pelajaran dalam satu tempat.
                    </p>
                </div>
                <a class="inline-flex w-fit items-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700"
                    href="{{ route('admin.dashboard') }}">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m15 18-6-6 6-6M9 12h12"/>
                    </svg>
                    Dashboard
                </a>
            </div>
        </header>

        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
                <p class="font-semibold">Periksa kembali data yang dimasukkan.</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <nav class="flex gap-2 overflow-x-auto rounded-2xl border border-slate-200 bg-white p-2 shadow-sm" aria-label="Kategori master data">
            @foreach ($tabs as $tabKey => $label)
                <a
                    class="shrink-0 rounded-xl px-4 py-2.5 text-sm font-semibold transition {{ $tab === $tabKey ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    href="{{ route('admin.kelas', ['tab' => $tabKey]) }}"
                    @if ($tab === $tabKey) aria-current="page" @endif
                >
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="data-list-title">
            <header class="flex flex-col gap-4 border-b border-slate-100 p-5 sm:p-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-col gap-1">
                    <h2 class="text-lg font-bold text-slate-900" id="data-list-title">List Data {{ $tabLabel }}</h2>
                    <p class="text-sm text-slate-500">Kelola dan perbarui data {{ strtolower($tabLabel) }} Nextask.</p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <form class="flex gap-2" method="GET" action="{{ route('admin.kelas') }}" role="search">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <label class="sr-only" for="master-data-search">Cari {{ strtolower($tabLabel) }}</label>
                        <input
                            class="min-w-0 flex-1 rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 sm:w-64"
                            id="master-data-search"
                            type="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari {{ strtolower($tabLabel) }}..."
                        >
                        <button class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600" type="submit">
                            Cari
                        </button>
                    </form>

                    <button
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700"
                        type="button"
                        data-open-form
                    >
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        Tambah Data
                    </button>
                </div>
            </header>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            @if ($tab === 'guru')
                                <th class="px-5 py-3 sm:px-6">Nama Guru</th>
                                <th class="px-4 py-3">NIP</th>
                                <th class="px-4 py-3">No. HP</th>
                            @elseif ($tab === 'siswa')
                                <th class="px-5 py-3 sm:px-6">Nama Siswa</th>
                                <th class="px-4 py-3">NISN</th>
                                <th class="px-4 py-3">Kelas</th>
                            @elseif ($tab === 'kelas')
                                <th class="px-5 py-3 sm:px-6">Nama Kelas</th>
                                <th class="px-4 py-3">Jurusan</th>
                                <th class="px-4 py-3">Wali Kelas</th>
                                <th class="px-4 py-3">Siswa</th>
                            @elseif ($tab === 'jurusan')
                                <th class="px-5 py-3 sm:px-6">Kode Jurusan</th>
                                <th class="px-4 py-3">Nama Jurusan</th>
                                <th class="px-4 py-3">Jumlah Kelas</th>
                            @else
                                <th class="px-5 py-3 sm:px-6">Kode Mapel</th>
                                <th class="px-4 py-3">Nama Mata Pelajaran</th>
                                <th class="px-4 py-3">Penugasan</th>
                            @endif
                            <th class="px-5 py-3 text-right sm:px-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($records as $record)
                            @php
                                $recordId = $record->getKey();
                                $editData = match ($tab) {
                                    'guru' => [
                                        'name' => $record->name,
                                        'email' => $record->email,
                                        'nip' => $record->guruProfile?->nip,
                                        'no_hp' => $record->guruProfile?->no_hp,
                                        'password' => '',
                                    ],
                                    'siswa' => [
                                        'name' => $record->name,
                                        'email' => $record->email,
                                        'nisn' => $record->siswaProfile?->nisn,
                                        'password' => '',
                                    ],
                                    'kelas' => [
                                        'nama_kelas' => $record->nama_kelas,
                                        'tingkat' => $record->tingkat,
                                    ],
                                    'jurusan' => [
                                        'kode_jurusan' => $record->kode_jurusan,
                                        'nama_jurusan' => $record->nama_jurusan,
                                    ],
                                    'mapel' => [
                                        'kode_mapel' => $record->kode_mapel,
                                        'nama_mapel' => $record->nama_mapel,
                                    ],
                                };
                            @endphp
                            <tr class="align-middle transition-colors hover:bg-slate-50/70">
                                @if ($tab === 'guru')
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-center gap-3">
                                            <span class="grid size-10 shrink-0 place-items-center rounded-full bg-blue-50 text-sm font-bold uppercase text-blue-700" aria-hidden="true">
                                                {{ str($record->name)->trim()->explode(' ')->filter()->take(2)->map(fn (string $part): string => mb_substr($part, 0, 1))->implode('') }}
                                            </span>
                                            <span class="flex min-w-0 flex-col gap-1">
                                                <span class="truncate font-semibold text-slate-800">{{ $record->name }}</span>
                                                <span class="truncate text-xs text-slate-500">{{ $record->email }}</span>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">{{ $record->guruProfile?->nip ?: '—' }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $record->guruProfile?->no_hp ?: '—' }}</td>
                                @elseif ($tab === 'siswa')
                                    <td class="px-5 py-4 sm:px-6">
                                        <span class="flex flex-col gap-1">
                                            <span class="font-semibold text-slate-800">{{ $record->name }}</span>
                                            <span class="text-xs text-slate-500">{{ $record->email }}</span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">{{ $record->siswaProfile?->nisn ?: '—' }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $record->siswaProfile?->kelas?->nama_kelas ?: 'Belum ditempatkan' }}</td>
                                @elseif ($tab === 'kelas')
                                    <td class="px-5 py-4 sm:px-6">
                                        <span class="flex flex-col gap-1">
                                            <span class="font-semibold text-slate-800">{{ $record->nama_kelas }}</span>
                                            <span class="text-xs text-slate-500">Tingkat {{ $record->tingkat }}</span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-slate-500">{{ $record->jurusan?->nama_jurusan ?: 'Belum diatur' }}</td>
                                    <td class="px-4 py-4 text-slate-500">{{ $record->waliKelas?->user?->name ?: 'Belum diatur' }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $record->siswa_profiles_count }}</td>
                                @elseif ($tab === 'jurusan')
                                    <td class="px-5 py-4 font-semibold text-slate-800 sm:px-6">{{ $record->kode_jurusan }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $record->nama_jurusan }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $record->kelas_count }}</td>
                                @else
                                    <td class="px-5 py-4 font-semibold text-slate-800 sm:px-6">{{ $record->kode_mapel }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $record->nama_mapel }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $record->guru_kelas_mapels_count }}</td>
                                @endif

                                <td class="px-5 py-4 sm:px-6">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($tab === 'guru')
                                            @php
                                                $profile = $record->guruProfile;
                                                $detailData = [
                                                    'name' => $record->name,
                                                    'email' => $record->email,
                                                    'nip' => $profile?->nip ?: '—',
                                                    'no_hp' => $profile?->no_hp ?: '—',
                                                    'wali_kelas' => $profile?->waliKelas?->pluck('nama_kelas')->implode(', ') ?: null,
                                                    'assignments' => $profile?->kelasMapels
                                                        ?->map(fn ($assignment): array => [
                                                            'kelas' => $assignment->kelas?->nama_kelas,
                                                            'mapel' => $assignment->mataPelajaran?->nama_mapel,
                                                        ])
                                                        ->values()
                                                        ->all() ?? [],
                                                ];
                                            @endphp
                                            <button
                                                class="rounded-lg border border-blue-100 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                                                type="button"
                                                data-open-detail
                                                data-record="{{ \Illuminate\Support\Js::encode($detailData) }}"
                                            >
                                                Lihat Detail
                                            </button>
                                        @endif
                                        <button
                                            class="grid size-9 place-items-center rounded-lg border border-slate-200 text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                                            type="button"
                                            data-open-form
                                            data-record="{{ \Illuminate\Support\Js::encode($editData) }}"
                                            data-record-id="{{ $recordId }}"
                                            aria-label="Edit {{ $tabLabel }} {{ $record->name ?? $record->nama_kelas ?? $record->nama_jurusan ?? $record->nama_mapel }}"
                                            title="Edit"
                                        >
                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="m15 5 4 4M4 20l4-.8L19 8a2.1 2.1 0 0 0-3-3L5 16l-1 4Z"/>
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.kelas.destroy', ['tab' => $tab, 'id' => $recordId]) }}" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="grid size-9 place-items-center rounded-lg border border-rose-100 text-rose-600 transition hover:bg-rose-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-600"
                                                type="submit"
                                                aria-label="Hapus {{ $tabLabel }} {{ $record->name ?? $record->nama_kelas ?? $record->nama_jurusan ?? $record->nama_mapel }}"
                                                title="Hapus"
                                            >
                                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M4 7h16M10 11v6m4-6v6M6 7l1 14h10l1-14M9 7V4h6v3"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-12 text-center" colspan="{{ $tab === 'kelas' ? 5 : 4 }}">
                                    <span class="mx-auto grid size-12 place-items-center rounded-2xl bg-slate-100 text-slate-500" aria-hidden="true">
                                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h4"/>
                                        </svg>
                                    </span>
                                    <p class="mt-3 font-semibold text-slate-800">Belum ada data {{ strtolower($tabLabel) }}</p>
                                    <p class="mt-1 text-sm text-slate-500">Tambahkan data pertama untuk kategori ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($records->hasPages())
                <footer class="border-t border-slate-100 px-5 py-4 sm:px-6">
                    {{ $records->links() }}
                </footer>
            @endif
        </section>
    </section>

    <dialog class="m-auto w-[min(100%-2rem,40rem)] rounded-2xl border-0 p-0 shadow-2xl backdrop:bg-slate-950/40" id="record-form-dialog" aria-labelledby="record-form-title">
        <form
            class="flex max-h-[90vh] flex-col"
            method="POST"
            action="{{ $storeUrl }}"
            id="record-form"
            data-store-url="{{ $storeUrl }}"
            data-update-url="{{ $updateUrlTemplate }}"
            data-tab-label="{{ $tabLabel }}"
            data-failed-edit-id="{{ old('editing_id', '') }}"
            data-old-values="{{ json_encode(old(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
        >
            @csrf
            <input type="hidden" name="_method" id="record-form-method" value="">
            <input type="hidden" name="editing_id" id="record-form-editing-id" value="{{ old('editing_id') }}">
            <header class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-blue-700">{{ $tabLabel }}</p>
                    <h2 class="text-lg font-bold text-slate-900" id="record-form-title">Tambah Data {{ $tabLabel }}</h2>
                </div>
                <button class="grid size-9 place-items-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900" type="button" data-close-dialog aria-label="Tutup">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <path d="m6 6 12 12M18 6 6 18"/>
                    </svg>
                </button>
            </header>
            <div class="grid gap-4 overflow-y-auto px-5 py-5 sm:grid-cols-2 sm:px-6">
                @foreach ($formFields as $field)
                    @php
                        $isFullWidth = $field['type'] === 'password' || $field['name'] === 'nama_kelas';
                        $isPassword = $field['type'] === 'password';
                    @endphp
                    <div class="flex flex-col gap-1.5 {{ $isFullWidth ? 'sm:col-span-2' : '' }}">
                        <label class="text-sm font-semibold text-slate-700" for="field-{{ $field['name'] }}">
                            {{ $field['label'] }}
                            @if ($field['required'] ?? false)
                                <span class="text-rose-600" data-required-marker>*</span>
                            @endif
                        </label>
                        @if ($field['type'] === 'select')
                            <select
                                class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                id="field-{{ $field['name'] }}"
                                name="{{ $field['name'] }}"
                                @if ($field['required'] ?? false) required @endif
                            >
                                <option value="">Pilih {{ strtolower($field['label']) }}</option>
                                @foreach ($field['options'] ?? [] as $optionValue => $optionLabel)
                                    <option value="{{ $optionValue }}" @selected((string) old($field['name']) === (string) $optionValue)>{{ $optionLabel }}</option>
                                @endforeach
                            </select>
                        @else
                            <input
                                class="rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                id="field-{{ $field['name'] }}"
                                type="{{ $field['type'] }}"
                                name="{{ $field['name'] }}"
                                value="{{ $isPassword ? '' : old($field['name']) }}"
                                placeholder="{{ $field['placeholder'] ?? 'Masukkan '.$field['label'] }}"
                                @if ($field['name'] === 'nisn') inputmode="numeric" maxlength="10" @endif
                                @if ($field['name'] === 'nip') inputmode="numeric" minlength="18" maxlength="18" @endif
                                @if ($field['required'] ?? false) required @endif
                                @if ($isPassword) autocomplete="new-password" @endif
                            >
                        @endif
                        @error($field['name'])
                            <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>
            <footer class="flex flex-col-reverse gap-2 border-t border-slate-100 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                <button class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" type="button" data-close-dialog>
                    Batal
                </button>
                <button class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700" type="submit">
                    Simpan Data
                </button>
            </footer>
        </form>
    </dialog>

    @if ($tab === 'guru')
        <dialog class="m-auto w-[min(100%-2rem,42rem)] rounded-2xl border-0 p-0 shadow-2xl backdrop:bg-slate-950/40" id="teacher-detail-dialog" aria-labelledby="teacher-detail-title">
            <div class="flex max-h-[90vh] flex-col">
                <header class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex flex-col gap-1">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-blue-700">Profil Guru</p>
                        <h2 class="text-lg font-bold text-slate-900" id="teacher-detail-title" data-detail-name>Detail Guru</h2>
                    </div>
                    <button class="grid size-9 place-items-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900" type="button" data-close-dialog aria-label="Tutup">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                            <path d="m6 6 12 12M18 6 6 18"/>
                        </svg>
                    </button>
                </header>
                <div class="flex flex-col gap-5 overflow-y-auto px-5 py-5 sm:px-6">
                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div class="flex flex-col gap-1">
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Nama</dt>
                            <dd class="font-semibold text-slate-800" data-detail-field="name"></dd>
                        </div>
                        <div class="flex flex-col gap-1">
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Email</dt>
                            <dd class="break-all font-semibold text-slate-800" data-detail-field="email"></dd>
                        </div>
                        <div class="flex flex-col gap-1">
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">NIP</dt>
                            <dd class="font-semibold text-slate-800" data-detail-field="nip"></dd>
                        </div>
                        <div class="flex flex-col gap-1">
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">No. HP</dt>
                            <dd class="font-semibold text-slate-800" data-detail-field="no_hp"></dd>
                        </div>
                    </dl>
                    <section class="flex flex-col gap-2">
                        <h3 class="text-sm font-bold text-slate-900">Status Wali Kelas</h3>
                        <p class="rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-700" data-detail-homeroom></p>
                    </section>
                    <section class="flex flex-col gap-2">
                        <h3 class="text-sm font-bold text-slate-900">Kelas & Mata Pelajaran yang Diajar</h3>
                        <div class="flex flex-col gap-2" data-detail-assignments></div>
                    </section>
                </div>
                <footer class="flex justify-end border-t border-slate-100 px-5 py-4 sm:px-6">
                    <button class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" type="button" data-close-dialog>
                        Tutup
                    </button>
                </footer>
            </div>
        </dialog>
    @endif

    <script>
        (() => {
            const formDialog = document.querySelector('#record-form-dialog');
            const form = document.querySelector('#record-form');
            const formTitle = document.querySelector('#record-form-title');
            const methodField = document.querySelector('#record-form-method');
            const editingId = document.querySelector('#record-form-editing-id');
            const updateUrlTemplate = form.dataset.updateUrl;
            const tabLabel = form.dataset.tabLabel;
            const openForm = (button) => {
                const recordId = button.dataset.recordId || '';
                const record = button.dataset.record ? JSON.parse(button.dataset.record) : {};
                const editing = recordId !== '';

                form.action = editing ? updateUrlTemplate.replace('__record__', recordId) : form.dataset.storeUrl;
                methodField.value = editing ? 'PUT' : '';
                methodField.disabled = !editing;
                editingId.value = editing ? recordId : '';
                formTitle.textContent = `${editing ? 'Edit' : 'Tambah'} Data ${tabLabel}`;

                form.querySelectorAll('[name]').forEach((field) => {
                    if (field.name === '_token' || field.name === '_method' || field.name === 'editing_id') {
                        return;
                    }

                    field.value = record[field.name] ?? '';

                    if (field.name === 'password') {
                        field.required = !editing;
                    }
                });

                form.querySelectorAll('[data-required-marker]').forEach((marker) => {
                    marker.hidden = editing && marker.closest('label').htmlFor === 'field-password';
                });

                formDialog.showModal();
            };

            document.querySelectorAll('[data-open-form]').forEach((button) => {
                button.addEventListener('click', () => openForm(button));
            });

            document.querySelectorAll('[data-close-dialog]').forEach((button) => {
                button.addEventListener('click', () => button.closest('dialog').close());
            });

            document.querySelectorAll('dialog').forEach((dialog) => {
                dialog.addEventListener('click', (event) => {
                    if (event.target === dialog) {
                        dialog.close();
                    }
                });
            });

            const detailDialog = document.querySelector('#teacher-detail-dialog');
            if (detailDialog) {
                document.querySelectorAll('[data-open-detail]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const detail = JSON.parse(button.dataset.record);
                        detailDialog.querySelector('[data-detail-name]').textContent = detail.name;

                        Object.entries(detail).forEach(([key, value]) => {
                            const field = detailDialog.querySelector(`[data-detail-field="${key}"]`);
                            if (field) {
                                field.textContent = value;
                            }
                        });

                        detailDialog.querySelector('[data-detail-homeroom]').textContent = detail.wali_kelas
                            ? `Wali kelas ${detail.wali_kelas}`
                            : 'Bukan wali kelas';

                        const assignments = detailDialog.querySelector('[data-detail-assignments]');
                        assignments.replaceChildren();

                        if (detail.assignments.length === 0) {
                            const emptyMessage = document.createElement('p');
                            emptyMessage.className = 'rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-500';
                            emptyMessage.textContent = 'Belum ada penugasan kelas';
                            assignments.append(emptyMessage);
                        } else {
                            detail.assignments.forEach((assignment) => {
                                const row = document.createElement('p');
                                row.className = 'rounded-xl border border-slate-100 px-4 py-3 text-sm text-slate-700';
                                row.textContent = `${assignment.kelas || 'Kelas belum tersedia'} · ${assignment.mapel || 'Mapel belum tersedia'}`;
                                assignments.append(row);
                            });
                        }

                        detailDialog.showModal();
                    });
                });
            }

            const oldValues = JSON.parse(form.dataset.oldValues || '{}');

            if (Object.keys(oldValues).length > 0) {
                const failedEditId = form.dataset.failedEditId;

                if (failedEditId) {
                    const failedEditButton = [...document.querySelectorAll('[data-record-id]')]
                        .find((button) => button.dataset.recordId === failedEditId);

                    if (failedEditButton) {
                        const editRecord = JSON.parse(failedEditButton.dataset.record);
                        form.querySelectorAll('[name]').forEach((field) => {
                            if (['_token', '_method', 'editing_id'].includes(field.name)) {
                                return;
                            }

                            editRecord[field.name] = oldValues[field.name] ?? '';
                        });
                        failedEditButton.dataset.record = JSON.stringify(editRecord);
                        openForm(failedEditButton);
                    }
                } else {
                    const failedCreateButton = document.querySelector('[data-open-form]:not([data-record-id])');
                    if (failedCreateButton) {
                        failedCreateButton.dataset.record = JSON.stringify(oldValues);
                        openForm(failedCreateButton);
                    }
                }
            }
        })();
    </script>
@endsection
