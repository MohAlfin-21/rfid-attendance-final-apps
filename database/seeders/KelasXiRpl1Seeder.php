<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KelasXiRpl1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');
        $now = now();

        $walas = User::firstOrCreate(
            ['username' => 'walas.xi.rpl1'],
            [
                'name' => 'Hardiyanti Wahyuningsih, S.Pd.',
                'email' => 'walas.xi.rpl1@user.id',
                'nis' => null,
                'locale' => 'id',
                'is_active' => true,
                'password' => $password,
                'email_verified_at' => $now,
            ],
        );
        $walas->assignRole('teacher');

        $classroom = Classroom::updateOrCreate(
            ['code' => 'XI-RPL1'],
            [
                'name' => 'XI-RPL 1',
                'grade' => 11,
                'major' => 'RPL',
                'homeroom_teacher_id' => $walas->id,
                'is_active' => true,
            ],
        );

        // 4. Students Data
        $studentsData = [
            "Achmad Binu Caputra Fikri",
            "Achmad Saifudin",
            "Ahmad Rosyid Alfualdi",
            "Aisyafa Febriana Dewi",
            "Ajeng Natasya Nurlailly Istiqomah",
            "Akbar Aminurokhim",
            "Akmal Fadhlu Rahman Sasmito",
            "Al Mira Cahaya Suci Tungga Dewi",
            "Aleifa Nofita Damayanti",
            "Alindya Prajwalita",
            "Amelia Nur Firzatullah",
            "Andhika Wahyu Sugiarto",
            "Arfansyah Adi Saputra",
            "Arina Karimatilail",
            "Audy Muzaki",
            "Aujasena Risang Tauladani",
            "Devi Zalfah Andiyanah",
            "Indra Syah Putra",
            "Iqbal Rizky Ramadhana",
            "Jesica Joansabrina Putri",
            "Kahfi Athohillah",
            "Kharis Fatur Rohman",
            "Lailatul Rohmah",
            "Mohammad Alfin Dwi Prayetno", // +sekertaris role
            "Muhammad Ridho Maulidiyanto",
            "Mukhamad Fadil Agustiar",
            "Nadila Sandra Dewi",
            "Nazril Gilang Ramadhan",
            "Septivea Elisa Rahmadhani",
            "Teguh Dwi Santoso",
            "Ummi Nur Fadhilah",
            "Veronicha Gresta Haryanti",
            "Widyo Krisna Yana Yahya",
            "Wildan Achmad Mubarok",
            "Wisanggeni Cahya Manggalar"
        ];

        // 5. Seed Students
        foreach ($studentsData as $index => $name) {
            $noUrut = $index + 1;
            
            // Format: xi.rpl1.(no,urut)@user.id
            $emailUsername = "xi.rpl1." . $noUrut . "@user.id";
            $isSecretary = ($name === "Mohammad Alfin Dwi Prayetno");

            $student = User::firstOrCreate(
                ['username' => $emailUsername],
                [
                    'name' => $name,
                    'email' => $emailUsername,
                    'nis' => '110' . str_pad($noUrut, 3, '0', STR_PAD_LEFT),
                    'locale' => 'id',
                    'is_active' => true,
                    'password' => $password,
                    'email_verified_at' => $now,
                ],
            );

            $rolesToAssign = $isSecretary ? ['student', 'secretary'] : ['student'];
            $student->assignRole($rolesToAssign);

            DB::table('classroom_students')->upsert(
                [[
                    'classroom_id' => $classroom->id,
                    'user_id' => $student->id,
                    'academic_year' => '2025/2026',
                    'semester' => 2,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]],
                ['classroom_id', 'user_id', 'academic_year', 'semester'],
                ['is_active', 'updated_at'],
            );
        }
    }
}
