<?php

namespace Tests\Feature;

use App\Models\GuruKelasMapel;
use App\Models\GuruProfile;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminKelasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public static function masterDataTabs(): array
    {
        return [
            'guru' => ['guru', 'List Data Guru'],
            'siswa' => ['siswa', 'List Data Siswa'],
            'kelas' => ['kelas', 'List Data Kelas'],
            'jurusan' => ['jurusan', 'List Data Jurusan'],
            'mata pelajaran' => ['mapel', 'List Data Mata Pelajaran'],
        ];
    }

    #[DataProvider('masterDataTabs')]
    public function test_admin_can_open_each_master_data_tab(string $tab, string $heading): void
    {
        $admin = User::factory()->withRole('admin')->create();

        $this->actingAs($admin)
            ->get(route('admin.kelas', ['tab' => $tab]))
            ->assertOk()
            ->assertSee('Pusat Master Data Kelas')
            ->assertSee($heading)
            ->assertSee('Tambah Data')
            ->assertSee('aria-current="page"', false);
    }

    public function test_only_admins_can_open_the_master_data_page(): void
    {
        $this->get(route('admin.kelas'))
            ->assertRedirect(route('login'));

        $student = User::factory()->withRole('siswa')->create();

        $this->actingAs($student)
            ->get(route('admin.kelas'))
            ->assertForbidden();
    }

    public function test_admin_can_create_update_and_delete_a_teacher_account_with_its_profile(): void
    {
        $admin = User::factory()->withRole('admin')->create();

        $this->actingAs($admin)
            ->post(route('admin.kelas.store', ['tab' => 'guru']), [
                'name' => 'Guru Pertama',
                'email' => 'guru.pertama@example.test',
                'nip' => '198501012010011001',
                'no_hp' => '081234567890',
                'password' => 'secret-password',
            ])
            ->assertRedirect(route('admin.kelas', ['tab' => 'guru']))
            ->assertSessionHasNoErrors();

        $teacher = User::query()->where('email', 'guru.pertama@example.test')->firstOrFail();
        $this->assertTrue($teacher->hasRole('guru'));
        $this->assertDatabaseHas('guru_profiles', [
            'user_id' => $teacher->getKey(),
            'nip' => '198501012010011001',
            'no_hp' => '081234567890',
        ]);

        $this->put(route('admin.kelas.update', ['tab' => 'guru', 'id' => $teacher->getKey()]), [
            'name' => 'Guru Diperbarui',
            'email' => 'guru.pertama@example.test',
            'nip' => '198501012010011001',
            'no_hp' => '081234567891',
            'password' => '',
        ])
            ->assertRedirect(route('admin.kelas', ['tab' => 'guru']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $teacher->getKey(),
            'name' => 'Guru Diperbarui',
        ]);
        $this->assertDatabaseHas('guru_profiles', [
            'user_id' => $teacher->getKey(),
            'nip' => '198501012010011001',
        ]);

        $this->get(route('admin.kelas', ['tab' => 'guru']))
            ->assertOk()
            ->assertSee('NIP')
            ->assertSee('name="nip"', false)
            ->assertSee('inputmode="numeric"', false)
            ->assertSee('minlength="18"', false)
            ->assertSee('maxlength="18"', false)
            ->assertSee('placeholder="18 digit NIP"', false)
            ->assertSee('required', false);

        $otherTeacher = User::factory()->withRole('guru')->create();
        $otherTeacher->guruProfile()->create(['nip' => '198601012010011002']);

        $this->from(route('admin.kelas', ['tab' => 'guru']))
            ->put(route('admin.kelas.update', ['tab' => 'guru', 'id' => $teacher->getKey()]), [
                'name' => 'Guru Diperbarui',
                'email' => 'guru.pertama@example.test',
                'nip' => '198601012010011002',
                'no_hp' => '081234567891',
                'password' => '',
            ])
            ->assertRedirect(route('admin.kelas', ['tab' => 'guru']))
            ->assertSessionHasErrors([
                'nip' => 'NIP ini sudah terdaftar pada guru lain.',
            ]);

        $this->assertDatabaseHas('guru_profiles', [
            'user_id' => $teacher->getKey(),
            'nip' => '198501012010011001',
        ]);

        $this->delete(route('admin.kelas.destroy', ['tab' => 'guru', 'id' => $teacher->getKey()]))
            ->assertRedirect(route('admin.kelas', ['tab' => 'guru']));

        $this->assertDatabaseMissing('users', ['id' => $teacher->getKey()]);
        $this->assertDatabaseMissing('guru_profiles', ['user_id' => $teacher->getKey()]);
    }

    public function test_teacher_account_requires_an_eighteen_digit_nip(): void
    {
        $admin = User::factory()->withRole('admin')->create();

        $this->actingAs($admin)
            ->from(route('admin.kelas', ['tab' => 'guru']))
            ->post(route('admin.kelas.store', ['tab' => 'guru']), [
                'name' => 'Guru NIP Tidak Valid',
                'email' => 'invalid.nip@example.test',
                'nip' => '19870001',
                'password' => 'secret-password',
            ])
            ->assertRedirect(route('admin.kelas', ['tab' => 'guru']))
            ->assertSessionHasErrors([
                'nip' => 'NIP harus terdiri dari 18 digit angka.',
            ]);

        $this->assertDatabaseMissing('users', ['email' => 'invalid.nip@example.test']);
    }

    public function test_teacher_account_requires_a_nip(): void
    {
        $admin = User::factory()->withRole('admin')->create();

        $this->actingAs($admin)
            ->from(route('admin.kelas', ['tab' => 'guru']))
            ->post(route('admin.kelas.store', ['tab' => 'guru']), [
                'name' => 'Guru Tanpa NIP',
                'email' => 'missing.nip@example.test',
                'password' => 'secret-password',
            ])
            ->assertRedirect(route('admin.kelas', ['tab' => 'guru']))
            ->assertSessionHasErrors([
                'nip' => 'NIP wajib diisi.',
            ]);

        $this->assertDatabaseMissing('users', ['email' => 'missing.nip@example.test']);
    }

    public function test_teacher_account_rejects_an_already_registered_nip(): void
    {
        $admin = User::factory()->withRole('admin')->create();
        $existingTeacher = User::factory()->withRole('guru')->create();
        $existingTeacher->guruProfile()->create(['nip' => '198501012010011001']);

        $this->actingAs($admin)
            ->from(route('admin.kelas', ['tab' => 'guru']))
            ->post(route('admin.kelas.store', ['tab' => 'guru']), [
                'name' => 'Guru Duplikat',
                'email' => 'duplicate.nip@example.test',
                'nip' => '198501012010011001',
                'password' => 'secret-password',
            ])
            ->assertRedirect(route('admin.kelas', ['tab' => 'guru']))
            ->assertSessionHasErrors([
                'nip' => 'NIP ini sudah terdaftar pada guru lain.',
            ]);

        $this->assertDatabaseMissing('users', ['email' => 'duplicate.nip@example.test']);
    }

    public function test_admin_can_create_update_and_delete_a_student_account_with_its_profile(): void
    {
        $admin = User::factory()->withRole('admin')->create();

        $this->actingAs($admin)
            ->post(route('admin.kelas.store', ['tab' => 'siswa']), [
                'name' => 'Siswa Pertama',
                'email' => 'siswa.pertama@example.test',
                'nisn' => '0012345678',
                'password' => 'secret-password',
            ])
            ->assertRedirect(route('admin.kelas', ['tab' => 'siswa']))
            ->assertSessionHasNoErrors();

        $student = User::query()->where('email', 'siswa.pertama@example.test')->firstOrFail();
        $this->assertTrue($student->hasRole('siswa'));
        $this->assertDatabaseHas('siswa_profiles', [
            'user_id' => $student->getKey(),
            'nisn' => '0012345678',
            'kelas_id' => null,
        ]);

        $this->put(route('admin.kelas.update', ['tab' => 'siswa', 'id' => $student->getKey()]), [
            'name' => 'Siswa Diperbarui',
            'email' => 'siswa.pertama@example.test',
            'nisn' => '0012345678',
            'password' => '',
        ])
            ->assertRedirect(route('admin.kelas', ['tab' => 'siswa']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $student->getKey(),
            'name' => 'Siswa Diperbarui',
        ]);
        $this->assertDatabaseHas('siswa_profiles', [
            'user_id' => $student->getKey(),
            'nisn' => '0012345678',
            'kelas_id' => null,
        ]);

        $otherStudent = User::factory()->withRole('siswa')->create();
        $otherStudent->siswaProfile()->create(['nisn' => '0098765432']);

        $this->from(route('admin.kelas', ['tab' => 'siswa']))
            ->put(route('admin.kelas.update', ['tab' => 'siswa', 'id' => $student->getKey()]), [
                'name' => 'Siswa Diperbarui',
                'email' => 'siswa.pertama@example.test',
                'nisn' => '0098765432',
                'password' => '',
            ])
            ->assertRedirect(route('admin.kelas', ['tab' => 'siswa']))
            ->assertSessionHasErrors([
                'nisn' => 'NISN ini sudah terdaftar pada siswa lain.',
            ]);

        $this->assertDatabaseHas('siswa_profiles', [
            'user_id' => $student->getKey(),
            'nisn' => '0012345678',
        ]);

        $this->get(route('admin.kelas', ['tab' => 'siswa']))
            ->assertOk()
            ->assertSee('Belum ditempatkan')
            ->assertSee('NISN')
            ->assertSee('name="nisn"', false)
            ->assertSee('inputmode="numeric"', false)
            ->assertSee('maxlength="10"', false)
            ->assertSee('placeholder="10 digit"', false)
            ->assertSee('required', false)
            ->assertDontSee('name="kelas_id"', false);

        $this->delete(route('admin.kelas.destroy', ['tab' => 'siswa', 'id' => $student->getKey()]))
            ->assertRedirect(route('admin.kelas', ['tab' => 'siswa']));

        $this->assertDatabaseMissing('users', ['id' => $student->getKey()]);
        $this->assertDatabaseMissing('siswa_profiles', ['user_id' => $student->getKey()]);
    }

    public function test_student_account_requires_a_ten_digit_nisn(): void
    {
        $admin = User::factory()->withRole('admin')->create();

        $this->actingAs($admin)
            ->from(route('admin.kelas', ['tab' => 'siswa']))
            ->post(route('admin.kelas.store', ['tab' => 'siswa']), [
                'name' => 'Siswa Tidak Valid',
                'email' => 'invalid.student@example.test',
                'nisn' => '1234',
                'password' => 'secret-password',
            ])
            ->assertRedirect(route('admin.kelas', ['tab' => 'siswa']))
            ->assertSessionHasErrors([
                'nisn' => 'NISN harus terdiri dari 10 digit angka.',
            ]);

        $this->assertDatabaseMissing('users', ['email' => 'invalid.student@example.test']);
    }

    public function test_student_account_requires_a_nisn(): void
    {
        $admin = User::factory()->withRole('admin')->create();

        $this->actingAs($admin)
            ->from(route('admin.kelas', ['tab' => 'siswa']))
            ->post(route('admin.kelas.store', ['tab' => 'siswa']), [
                'name' => 'Siswa Tanpa NISN',
                'email' => 'missing.nisn@example.test',
                'password' => 'secret-password',
            ])
            ->assertRedirect(route('admin.kelas', ['tab' => 'siswa']))
            ->assertSessionHasErrors([
                'nisn' => 'NISN wajib diisi.',
            ]);

        $this->assertDatabaseMissing('users', ['email' => 'missing.nisn@example.test']);
    }

    public function test_student_account_rejects_an_already_registered_nisn(): void
    {
        $admin = User::factory()->withRole('admin')->create();
        $existingStudent = User::factory()->withRole('siswa')->create();
        $existingStudent->siswaProfile()->create(['nisn' => '0012345678']);

        $this->actingAs($admin)
            ->from(route('admin.kelas', ['tab' => 'siswa']))
            ->post(route('admin.kelas.store', ['tab' => 'siswa']), [
                'name' => 'Siswa Duplikat',
                'email' => 'duplicate.nisn@example.test',
                'nisn' => '0012345678',
                'password' => 'secret-password',
            ])
            ->assertRedirect(route('admin.kelas', ['tab' => 'siswa']))
            ->assertSessionHasErrors([
                'nisn' => 'NISN ini sudah terdaftar pada siswa lain.',
            ]);

        $this->assertDatabaseMissing('users', ['email' => 'duplicate.nisn@example.test']);
    }

    public function test_admin_can_manage_jurusan_classes_and_subjects(): void
    {
        $admin = User::factory()->withRole('admin')->create();

        $this->actingAs($admin)
            ->post(route('admin.kelas.store', ['tab' => 'jurusan']), [
                'kode_jurusan' => 'RPL',
                'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            ])
            ->assertRedirect(route('admin.kelas', ['tab' => 'jurusan']));

        $jurusan = Jurusan::query()->where('kode_jurusan', 'RPL')->firstOrFail();

        $this->post(route('admin.kelas.store', ['tab' => 'mapel']), [
            'kode_mapel' => 'MTK',
            'nama_mapel' => 'Matematika',
        ])->assertRedirect(route('admin.kelas', ['tab' => 'mapel']));

        $subject = MataPelajaran::query()->where('kode_mapel', 'MTK')->firstOrFail();

        $this->post(route('admin.kelas.store', ['tab' => 'kelas']), [
            'nama_kelas' => 'X RPL 1',
            'tingkat' => 'X',
        ])->assertRedirect(route('admin.kelas', ['tab' => 'kelas']));

        $class = Kelas::query()->where('nama_kelas', 'X RPL 1')->firstOrFail();
        $this->assertDatabaseHas('kelas', [
            'id' => $class->getKey(),
            'jurusan_id' => null,
            'wali_kelas_id' => null,
        ]);

        $this->put(route('admin.kelas.update', ['tab' => 'kelas', 'id' => $class->getKey()]), [
            'nama_kelas' => 'XI RPL 1',
            'tingkat' => 'XI',
        ])->assertRedirect(route('admin.kelas', ['tab' => 'kelas']));

        $this->assertDatabaseHas('kelas', [
            'id' => $class->getKey(),
            'nama_kelas' => 'XI RPL 1',
            'tingkat' => 'XI',
            'jurusan_id' => null,
            'wali_kelas_id' => null,
        ]);

        $this->get(route('admin.kelas', ['tab' => 'kelas']))
            ->assertOk()
            ->assertSee('Belum diatur')
            ->assertDontSee('name="jurusan_id"', false)
            ->assertDontSee('name="wali_kelas_id"', false);

        $this->put(route('admin.kelas.update', ['tab' => 'mapel', 'id' => $subject->getKey()]), [
            'kode_mapel' => 'MTK',
            'nama_mapel' => 'Matematika Lanjutan',
        ])->assertRedirect(route('admin.kelas', ['tab' => 'mapel']));

        $this->assertDatabaseHas('mata_pelajarans', [
            'id' => $subject->getKey(),
            'nama_mapel' => 'Matematika Lanjutan',
        ]);

        $this->delete(route('admin.kelas.destroy', ['tab' => 'kelas', 'id' => $class->getKey()]));
        $this->delete(route('admin.kelas.destroy', ['tab' => 'mapel', 'id' => $subject->getKey()]));
        $this->delete(route('admin.kelas.destroy', ['tab' => 'jurusan', 'id' => $jurusan->getKey()]));

        $this->assertDatabaseMissing('kelas', ['id' => $class->getKey()]);
        $this->assertDatabaseMissing('mata_pelajarans', ['id' => $subject->getKey()]);
        $this->assertDatabaseMissing('jurusans', ['id' => $jurusan->getKey()]);
    }

    public function test_class_store_ignores_jurusan_and_homeroom_inputs(): void
    {
        $admin = User::factory()->withRole('admin')->create();
        $adminProfile = GuruProfile::query()->create([
            'user_id' => $admin->getKey(),
            'nip' => '198701012010011003',
        ]);
        $jurusan = $this->createJurusan();

        $this->actingAs($admin)
            ->post(route('admin.kelas.store', ['tab' => 'kelas']), [
                'nama_kelas' => 'X RPL 2',
                'tingkat' => 'X',
                'jurusan_id' => $jurusan->getKey(),
                'wali_kelas_id' => $adminProfile->getKey(),
            ])
            ->assertRedirect(route('admin.kelas', ['tab' => 'kelas']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('kelas', [
            'nama_kelas' => 'X RPL 2',
            'jurusan_id' => null,
            'wali_kelas_id' => null,
        ]);
    }

    public function test_jurusan_in_use_cannot_be_deleted(): void
    {
        $admin = User::factory()->withRole('admin')->create();
        $jurusan = $this->createJurusan();
        $class = Kelas::query()->create([
            'nama_kelas' => 'XI RPL 1',
            'tingkat' => 'XI',
            'jurusan_id' => $jurusan->getKey(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.kelas.destroy', ['tab' => 'jurusan', 'id' => $jurusan->getKey()]))
            ->assertRedirect(route('admin.kelas', ['tab' => 'jurusan']))
            ->assertSessionHasErrors('delete');

        $this->assertModelExists($jurusan);
        $this->assertModelExists($class);
    }

    public function test_teacher_detail_shows_homeroom_status_and_teaching_assignments(): void
    {
        $admin = User::factory()->withRole('admin')->create();
        $teacher = User::factory()->withRole('guru')->create([
            'name' => 'Guru Detail',
            'email' => 'guru.detail@example.test',
        ]);
        $profile = $teacher->guruProfile()->create([
            'nip' => '198701012010011004',
            'no_hp' => '081234567892',
        ]);
        $jurusan = $this->createJurusan();
        $class = Kelas::query()->create([
            'nama_kelas' => 'X RPL Detail',
            'tingkat' => 'X',
            'jurusan_id' => $jurusan->getKey(),
            'wali_kelas_id' => $profile->getKey(),
        ]);
        $subject = MataPelajaran::query()->create([
            'kode_mapel' => 'IPA',
            'nama_mapel' => 'Ilmu Pengetahuan Alam',
        ]);
        GuruKelasMapel::query()->create([
            'guru_profile_id' => $profile->getKey(),
            'kelas_id' => $class->getKey(),
            'mata_pelajaran_id' => $subject->getKey(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.kelas', ['tab' => 'guru']))
            ->assertOk()
            ->assertSee('Lihat Detail')
            ->assertSee('Guru Detail')
            ->assertSee('X RPL Detail')
            ->assertSee('Ilmu Pengetahuan Alam')
            ->assertSee('Belum ada penugasan kelas');
    }

    public function test_search_keeps_the_selected_tab_and_filters_its_records(): void
    {
        $admin = User::factory()->withRole('admin')->create();
        User::factory()->withRole('guru')->create([
            'name' => 'Guru Dicari',
            'email' => 'needle.teacher@example.test',
        ]);
        User::factory()->withRole('guru')->create([
            'name' => 'Guru Lain',
            'email' => 'other.teacher@example.test',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.kelas', ['tab' => 'guru', 'search' => 'needle']))
            ->assertOk()
            ->assertSee('Guru Dicari')
            ->assertDontSee('Guru Lain')
            ->assertSee('name="tab" value="guru"', false)
            ->assertSee('name="search"', false);
    }

    private function createJurusan(): Jurusan
    {
        return Jurusan::query()->create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
        ]);
    }
}
