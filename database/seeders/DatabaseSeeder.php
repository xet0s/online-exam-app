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

        


        $admin = User::firstOrCreate(
            ['email' => 'admin@exam.com'],
            [
                'name'          => 'Sistem Yöneticisi',
                'password'      => Hash::make('password'),
                'role'          => 'admin',
                'status'        => 'approved',
                'department_id' => null,
            ]
        );

        $this->command->info('✔  Admin oluşturuldu: admin@exam.com / password');

        

        

        $departmentsData = [
            ['name' => 'Bilgisayar Mühendisliği'],
            ['name' => 'Elektrik-Elektronik Mühendisliği'],
            ['name' => 'Makine Mühendisliği'],
            ['name' => 'Endüstri Mühendisliği'],
        ];

        $departments = [];
        foreach ($departmentsData as $data) {
            $departments[] = Department::firstOrCreate(['name' => $data['name']]);
        }

        [$bilgisayar, $elektrik, $makine, $endustri] = $departments;

        $this->command->info('✔  4 bölüm oluşturuldu.');

        

        

        $dekan = User::firstOrCreate(
            ['email' => 'dekan@exam.com'],
            [
                'name'          => 'Prof. Dr. Ahmet Kaya',
                'password'      => Hash::make('password'),
                'role'          => 'dekan',
                'status'        => 'approved',
                'department_id' => $bilgisayar->id,
            ]
        );

        $this->command->info('✔  Dekan oluşturuldu: dekan@exam.com / password');

        

        

        $chairs = [
            [
                'name'  => 'Doç. Dr. Mehmet Yılmaz',
                'email' => 'baskanbilgisayar@exam.com',
                'dept'  => $bilgisayar,
            ],
            [
                'name'  => 'Doç. Dr. Fatma Şahin',
                'email' => 'baskanelektrik@exam.com',
                'dept'  => $elektrik,
            ],
            [
                'name'  => 'Doç. Dr. Ali Demir',
                'email' => 'baskanmakine@exam.com',
                'dept'  => $makine,
            ],
            [
                'name'  => 'Doç. Dr. Zeynep Arslan',
                'email' => 'baskanendustri@exam.com',
                'dept'  => $endustri,
            ],
        ];

        foreach ($chairs as $c) {
            User::firstOrCreate(
                ['email' => $c['email']],
                [
                    'name'          => $c['name'],
                    'password'      => Hash::make('password'),
                    'role'          => 'bolum_baskani',
                    'status'        => 'approved',
                    'department_id' => $c['dept']->id,
                ]
            );
        }

        $this->command->info('✔  4 bölüm başkanı oluşturuldu.');

        

        

        $instructorsData = [

            ['name' => 'Dr. Öğr. Üyesi Emre Çelik',    'email' => 'emrecelik@exam.com',    'dept' => $bilgisayar],
            ['name' => 'Dr. Öğr. Üyesi Selin Koç',     'email' => 'selinkoc@exam.com',     'dept' => $bilgisayar],
            ['name' => 'Arş. Gör. Dr. Burak Tekin',    'email' => 'buraktekin@exam.com',   'dept' => $bilgisayar],
            ['name' => 'Doç. Dr. Hande Aydın',         'email' => 'handeaydin@exam.com',   'dept' => $bilgisayar],

            
            ['name' => 'Prof. Dr. Sercan Güler',        'email' => 'sercangler@exam.com',   'dept' => $elektrik],
            ['name' => 'Dr. Öğr. Üyesi Elif Öztürk',  'email' => 'elifozturk@exam.com',   'dept' => $elektrik],
            ['name' => 'Dr. Öğr. Üyesi Caner Polat',  'email' => 'canerpolat@exam.com',   'dept' => $elektrik],

            
            ['name' => 'Prof. Dr. Tuncay Eroğlu',      'email' => 'tuncayeroglu@exam.com', 'dept' => $makine],
            ['name' => 'Doç. Dr. Derya Mutlu',         'email' => 'deryamutlu@exam.com',   'dept' => $makine],
            ['name' => 'Dr. Öğr. Üyesi Ozan Kurt',    'email' => 'ozankurt@exam.com',     'dept' => $makine],

            
            ['name' => 'Prof. Dr. Nihal Başaran',      'email' => 'nihalbasaran@exam.com', 'dept' => $endustri],
            ['name' => 'Doç. Dr. Kadir Yıldız',       'email' => 'kadiryildiz@exam.com',  'dept' => $endustri],
            ['name' => 'Dr. Öğr. Üyesi Aslı Toprak',  'email' => 'aslitoprak@exam.com',   'dept' => $endustri],
        ];

        $instructors = [];
        foreach ($instructorsData as $data) {
            $instructors[$data['email']] = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'          => $data['name'],
                    'password'      => Hash::make('password'),
                    'role'          => 'egitmen',
                    'status'        => 'approved',
                    'department_id' => $data['dept']->id,
                ]
            );
        }

        $this->command->info('✔  13 eğitmen oluşturuldu.');

        

        

        $buildingsData = [
            ['name' => 'A Binası', 'code' => 'A'],
            ['name' => 'B Binası', 'code' => 'B'],
            ['name' => 'C Binası', 'code' => 'C'],
            ['name' => 'D Binası', 'code' => 'D'],
        ];

        foreach ($buildingsData as $b) {
            \App\Models\Building::firstOrCreate(
                ['name' => $b['name']],
                ['code' => $b['code']]
            );
        }

        $this->command->info('✔  4 bina oluşturuldu.');

        

        

        $classroomsData = [

            ['name' => 'A-101', 'building' => 'A Binası', 'capacity' => 120, 'dept' => null],
            ['name' => 'A-102', 'building' => 'A Binası', 'capacity' => 80,  'dept' => null],
            ['name' => 'A-201', 'building' => 'A Binası', 'capacity' => 60,  'dept' => null],
            ['name' => 'A-202', 'building' => 'A Binası', 'capacity' => 40,  'dept' => null],

            
            ['name' => 'B-101', 'building' => 'B Binası', 'capacity' => 50,  'dept' => $bilgisayar],
            ['name' => 'B-102', 'building' => 'B Binası', 'capacity' => 30,  'dept' => $bilgisayar],
            ['name' => 'B-LAB1', 'building' => 'B Binası', 'capacity' => 25, 'dept' => $bilgisayar],

            
            ['name' => 'C-101', 'building' => 'C Binası', 'capacity' => 55,  'dept' => $elektrik],
            ['name' => 'C-102', 'building' => 'C Binası', 'capacity' => 35,  'dept' => $elektrik],
            ['name' => 'C-LAB1', 'building' => 'C Binası', 'capacity' => 20, 'dept' => $elektrik],

            
            ['name' => 'D-101', 'building' => 'D Binası', 'capacity' => 70,  'dept' => $makine],
            ['name' => 'D-102', 'building' => 'D Binası', 'capacity' => 45,  'dept' => $endustri],
            ['name' => 'D-201', 'building' => 'D Binası', 'capacity' => 30,  'dept' => $makine],
        ];

        $classrooms = [];
        foreach ($classroomsData as $data) {
            $classrooms[$data['name']] = Classroom::firstOrCreate(
                ['name' => $data['name']],
                [
                    'building'      => $data['building'],
                    'capacity'      => $data['capacity'],
                    'department_id' => $data['dept'] ? $data['dept']->id : null,
                ]
            );
        }

        $this->command->info('✔  13 derslik oluşturuldu.');

        

        

        

        $examsData = [

            [
                'name'          => 'Algoritmalar ve Veri Yapıları Vize',
                'student_count' => 85,
                'start_time'    => '2026-06-16 09:00:00',
                'end_time'      => '2026-06-16 11:00:00',
                'instructor'    => $instructors['emrecelik@exam.com'],
                'department'    => $bilgisayar,
                'status'        => 'approved',
            ],
            [
                'name'          => 'Nesne Yönelimli Programlama Final',
                'student_count' => 60,
                'start_time'    => '2026-06-17 13:00:00',
                'end_time'      => '2026-06-17 15:00:00',
                'instructor'    => $instructors['selinkoc@exam.com'],
                'department'    => $bilgisayar,
                'status'        => 'approved',
            ],
            [
                'name'          => 'Veritabanı Yönetim Sistemleri Vize',
                'student_count' => 45,
                'start_time'    => '2026-06-18 10:00:00',
                'end_time'      => '2026-06-18 12:00:00',
                'instructor'    => $instructors['buraktekin@exam.com'],
                'department'    => $bilgisayar,
                'status'        => 'sent_to_dean',
            ],
            [
                'name'          => 'Yapay Zeka ve Makine Öğrenmesi Final',
                'student_count' => 70,
                'start_time'    => '2026-06-19 09:00:00',
                'end_time'      => '2026-06-19 11:30:00',
                'instructor'    => $instructors['handeaydin@exam.com'],
                'department'    => $bilgisayar,
                'status'        => 'approved',
            ],
            [
                'name'          => 'İşletim Sistemleri Bütünleme',
                'student_count' => 30,
                'start_time'    => '2026-06-20 14:00:00',
                'end_time'      => '2026-06-20 16:00:00',
                'instructor'    => $instructors['emrecelik@exam.com'],
                'department'    => $bilgisayar,
                'status'        => 'pending',
            ],

            
            [
                'name'          => 'Devre Analizi Vize',
                'student_count' => 90,
                'start_time'    => '2026-06-16 13:00:00',
                'end_time'      => '2026-06-16 15:00:00',
                'instructor'    => $instructors['sercangler@exam.com'],
                'department'    => $elektrik,
                'status'        => 'approved',
            ],
            [
                'name'          => 'Sinyal ve Sistemler Final',
                'student_count' => 55,
                'start_time'    => '2026-06-17 09:00:00',
                'end_time'      => '2026-06-17 11:00:00',
                'instructor'    => $instructors['elifozturk@exam.com'],
                'department'    => $elektrik,
                'status'        => 'approved',
            ],
            [
                'name'          => 'Elektromanyetik Teori Vize',
                'student_count' => 40,
                'start_time'    => '2026-06-18 14:00:00',
                'end_time'      => '2026-06-18 16:00:00',
                'instructor'    => $instructors['canerpolat@exam.com'],
                'department'    => $elektrik,
                'status'        => 'sent_to_dean',
            ],

            
            [
                'name'          => 'Termodinamik Final',
                'student_count' => 75,
                'start_time'    => '2026-06-16 09:00:00',
                'end_time'      => '2026-06-16 11:30:00',
                'instructor'    => $instructors['tuncayeroglu@exam.com'],
                'department'    => $makine,
                'status'        => 'approved',
            ],
            [
                'name'          => 'Mukavemet Vize',
                'student_count' => 50,
                'start_time'    => '2026-06-17 13:00:00',
                'end_time'      => '2026-06-17 15:00:00',
                'instructor'    => $instructors['deryamutlu@exam.com'],
                'department'    => $makine,
                'status'        => 'approved',
            ],
            [
                'name'          => 'Akışkanlar Mekaniği Final',
                'student_count' => 35,
                'start_time'    => '2026-06-19 14:00:00',
                'end_time'      => '2026-06-19 16:00:00',
                'instructor'    => $instructors['ozankurt@exam.com'],
                'department'    => $makine,
                'status'        => 'pending',
            ],

            
            [
                'name'          => 'Yöneylem Araştırması Vize',
                'student_count' => 65,
                'start_time'    => '2026-06-16 13:00:00',
                'end_time'      => '2026-06-16 15:30:00',
                'instructor'    => $instructors['nihalbasaran@exam.com'],
                'department'    => $endustri,
                'status'        => 'approved',
            ],
            [
                'name'          => 'İstatistiksel Kalite Kontrol Final',
                'student_count' => 48,
                'start_time'    => '2026-06-18 09:00:00',
                'end_time'      => '2026-06-18 11:00:00',
                'instructor'    => $instructors['kadiryildiz@exam.com'],
                'department'    => $endustri,
                'status'        => 'sent_to_dean',
            ],
            [
                'name'          => 'Üretim Planlama ve Kontrol Bütünleme',
                'student_count' => 28,
                'start_time'    => '2026-06-20 10:00:00',
                'end_time'      => '2026-06-20 12:00:00',
                'instructor'    => $instructors['aslitoprak@exam.com'],
                'department'    => $endustri,
                'status'        => 'pending',
            ],
        ];

        

        
        \App\Models\ExamPeriod::firstOrCreate(
            ['department_id' => null],
            [
                'name'       => '2026 Bahar Yarıyılı Final Haftası',
                'start_date' => '2026-06-15',
                'end_date'   => '2026-06-28',
                'created_by' => $admin->id,
            ]
        );

        foreach ($examsData as $data) {
            Exam::firstOrCreate(
                [
                    'name'          => $data['name'],
                    'instructor_id' => $data['instructor']->id,
                ],
                [
                    'student_count' => $data['student_count'],
                    'start_time'    => null,
                    'end_time'      => null,
                    'instructor_id' => $data['instructor']->id,
                    'department_id' => $data['department']->id,
                    'status'        => $data['status'],
                ]
            );
        }

        $this->command->info('✔  14 sınav oluşturuldu.');

        

        
        $this->command->newLine();
        $this->command->table(
            ['Rol', 'E-posta', 'Şifre'],
            [
                ['Admin',             'admin@exam.com',               'password'],
                ['Dekan',             'dekan@exam.com',               'password'],
                ['Başkan (BM)',        'baskanbilgisayar@exam.com',    'password'],
                ['Başkan (EEM)',       'baskanelektrik@exam.com',      'password'],
                ['Başkan (Makine)',    'baskanmakine@exam.com',        'password'],
                ['Başkan (Endüstri)', 'baskanendustri@exam.com',      'password'],
                ['Eğitmen (BM)',       'emrecelik@exam.com',           'password'],
                ['Eğitmen (BM)',       'selinkoc@exam.com',            'password'],
                ['Eğitmen (EEM)',      'sercangler@exam.com',          'password'],
                ['Eğitmen (Makine)',   'tuncayeroglu@exam.com',        'password'],
                ['Eğitmen (Endüstri)', 'nihalbasaran@exam.com',       'password'],
            ]
        );
        $this->command->newLine();
        $this->command->info('🚀  Seed tamamlandı! "Otomatik Dağıtımı Çalıştır" butonuna basarak derslik + gözetmen atamalarını başlatabilirsiniz.');
    }
}
