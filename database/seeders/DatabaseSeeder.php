<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {

        $compDept = Department::create(['name' => 'Hitit Üniv. Bilgisayar Mühendisliği']);
        $chemDept = Department::create(['name' => 'Hitit Üniv. Kimya Mühendisliği']);
        $mechDept = Department::create(['name' => 'Hitit Üniv. Makine Mühendisliği']);

        User::create([
            'name' => 'Sistem Yöneticisi (Admin)',
            'email' => 'admin@hitit.edu.tr',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'approved',
            'department_id' => null,
        ]);

        User::create([
            'name' => 'Prof. Dr. Mehmet Alp (Dekan)',
            'email' => 'dekan@hitit.edu.tr',
            'password' => Hash::make('password'),
            'role' => 'dekan',
            'status' => 'approved',
            'department_id' => null,
        ]);

        User::create([
            'name' => 'Doç. Dr. Seyfi Şirin (Bölüm Başkanı)',
            'email' => 'chair@hitit.edu.tr',
            'password' => Hash::make('password'),
            'role' => 'bolum_baskani',
            'status' => 'approved',
            'department_id' => $compDept->id,
        ]);

        $inst1 = User::create([
            'name' => 'Dr. Öğr. Üyesi Kadir Şentürk',
            'email' => 'kadir@hitit.edu.tr',
            'password' => Hash::make('password'),
            'role' => 'egitmen',
            'status' => 'approved',
            'department_id' => $compDept->id,
        ]);

        $inst2 = User::create([
            'name' => 'Dr. Öğr. Üyesi Akif Akyol',
            'email' => 'akif@hitit.edu.tr',
            'password' => Hash::make('password'),
            'role' => 'egitmen',
            'status' => 'approved',
            'department_id' => $compDept->id,
        ]);

        $inst3 = User::create([
            'name' => 'Dr. Öğr. Üyesi Kübra Göksu',
            'email' => 'kubra@hitit.edu.tr',
            'password' => Hash::make('password'),
            'role' => 'egitmen',
            'status' => 'approved',
            'department_id' => $compDept->id,
        ]);

        $inst4 = User::create([
            'name' => 'Öğr. Gör. Mehmet Fatih Aşıcı',
            'email' => 'fatih@hitit.edu.tr',
            'password' => Hash::make('password'),
            'role' => 'egitmen',
            'status' => 'approved',
            'department_id' => $compDept->id,
        ]);

        User::create([
            'name' => 'Arş. Gör. Caner Yılmaz (Onay Bekliyor)',
            'email' => 'caner@hitit.edu.tr',
            'password' => Hash::make('password'),
            'role' => 'egitmen',
            'status' => 'pending',
            'department_id' => $compDept->id,
        ]);

        Classroom::create([
            'name' => 'M-101 Derslik',
            'capacity' => 45,
            'department_id' => $compDept->id,
        ]);

        Classroom::create([
            'name' => 'M-102 Derslik',
            'capacity' => 35,
            'department_id' => $compDept->id,
        ]);

        Classroom::create([
            'name' => 'Bilgisayar Laboratuvarı I',
            'capacity' => 50,
            'department_id' => $compDept->id,
        ]);

        Classroom::create([
            'name' => 'Bilgisayar Laboratuvarı II',
            'capacity' => 40,
            'department_id' => $compDept->id,
        ]);

        Classroom::create([
            'name' => 'Mühendislik Konferans Salonu',
            'capacity' => 120,
            'department_id' => null,
        ]);

        Exam::create([
            'name' => 'BM-101 Programlamaya Giriş Vize',
            'student_count' => 42,
            'start_time' => '2026-06-15 10:00:00',
            'end_time' => '2026-06-15 12:00:00',
            'instructor_id' => $inst4->id,
            'department_id' => $compDept->id,
            'classroom_id' => null,
        ]);

        Exam::create([
            'name' => 'BM-201 Veri Yapıları ve Algoritmalar Vize',
            'student_count' => 38,
            'start_time' => '2026-06-15 10:00:00',
            'end_time' => '2026-06-15 11:30:00',
            'instructor_id' => $inst1->id,
            'department_id' => $compDept->id,
            'classroom_id' => null,
        ]);

        Exam::create([
            'name' => 'BM-301 Veri Tabanı Yönetim Sistemleri Vize',
            'student_count' => 35,
            'start_time' => '2026-06-15 13:00:00',
            'end_time' => '2026-06-15 15:00:00',
            'instructor_id' => $inst2->id,
            'department_id' => $compDept->id,
            'classroom_id' => null,
        ]);

        Exam::create([
            'name' => 'BM-401 Yapay Zekaya Giriş Final',
            'student_count' => 110,
            'start_time' => '2026-06-15 10:00:00',
            'end_time' => '2026-06-15 12:00:00',
            'instructor_id' => $inst3->id,
            'department_id' => $compDept->id,
            'classroom_id' => null,
        ]);
    }
}
