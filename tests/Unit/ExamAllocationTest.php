<?php

namespace Tests\Unit;

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Exam;
use App\Models\User;
use App\Services\ExamAllocationService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamAllocationTest extends TestCase
{
    use RefreshDatabase;

    protected ExamAllocationService $allocationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->allocationService = new ExamAllocationService();
    }

    public function test_allocation_success_minimizes_waste_and_avoids_overlaps()
    {

        $dept = Department::create(['name' => 'Computer Engineering']);

        $instructor = User::create([
            'name' => 'Dr. Ahmet',
            'email' => 'ahmet@test.com',
            'password' => bcrypt('password'),
            'role' => 'egitmen',
            'status' => 'approved',
            'department_id' => $dept->id,
        ]);

        $cSmall = Classroom::create([
            'name' => 'Small Room',
            'capacity' => 30,
            'department_id' => $dept->id,
        ]);

        $cLarge = Classroom::create([
            'name' => 'Large Room',
            'capacity' => 60,
            'department_id' => $dept->id,
        ]);

        $exam1 = Exam::create([
            'name' => 'Exam 1',
            'student_count' => 25,
            'start_time' => '2026-06-15 10:00:00',
            'end_time' => '2026-06-15 11:30:00',
            'instructor_id' => $instructor->id,
            'department_id' => $dept->id,
        ]);

        $exam2 = Exam::create([
            'name' => 'Exam 2',
            'student_count' => 55,
            'start_time' => '2026-06-15 10:00:00',
            'end_time' => '2026-06-15 12:00:00',
            'instructor_id' => $instructor->id,
            'department_id' => $dept->id,
        ]);

        $exam3 = Exam::create([
            'name' => 'Exam 3',
            'student_count' => 20,
            'start_time' => '2026-06-15 12:00:00',
            'end_time' => '2026-06-15 13:00:00',
            'instructor_id' => $instructor->id,
            'department_id' => $dept->id,
        ]);

        $this->allocationService->allocateExams();

        $exam1->refresh();
        $exam2->refresh();
        $exam3->refresh();

        $this->assertTrue($exam1->classrooms->isNotEmpty());
        $this->assertTrue($exam2->classrooms->isNotEmpty());
        $this->assertTrue($exam3->classrooms->isNotEmpty());

        $this->assertTrue($exam1->classrooms->contains($cSmall->id));

        $this->assertTrue($exam2->classrooms->contains($cLarge->id));

        $this->assertTrue($exam3->classrooms->contains($cSmall->id));
    }

    public function test_allocation_fails_and_rolls_back_on_bottleneck()
    {
        $dept = Department::create(['name' => 'Computer Engineering']);

        $instructor = User::create([
            'name' => 'Dr. Ahmet',
            'email' => 'ahmet@test.com',
            'password' => bcrypt('password'),
            'role' => 'egitmen',
            'status' => 'approved',
            'department_id' => $dept->id,
        ]);

        $cRoom = Classroom::create([
            'name' => 'Room A',
            'capacity' => 30,
            'department_id' => $dept->id,
        ]);

        $exam1 = Exam::create([
            'name' => 'Exam 1',
            'student_count' => 25,
            'start_time' => '2026-06-15 10:00:00',
            'end_time' => '2026-06-15 11:30:00',
            'instructor_id' => $instructor->id,
            'department_id' => $dept->id,
        ]);
        $exam1->classrooms()->attach($cRoom->id);

        $exam2 = Exam::create([
            'name' => 'Exam 2',
            'student_count' => 20,
            'start_time' => '2026-06-15 10:30:00',
            'end_time' => '2026-06-15 12:00:00',
            'instructor_id' => $instructor->id,
            'department_id' => $dept->id,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Uyuşmayan Sınav Planı");

        try {
            $this->allocationService->allocateExams();
        } finally {

            $exam1->refresh();
            $exam2->refresh();
            $this->assertTrue($exam1->classrooms->contains($cRoom->id));
            $this->assertTrue($exam2->classrooms->isEmpty());
        }
    }

    public function test_allocation_combines_classrooms_when_single_room_is_insufficient()
    {
        $dept = Department::create(['name' => 'Computer Engineering']);

        $instructor = User::create([
            'name' => 'Dr. Ahmet',
            'email' => 'ahmet@test.com',
            'password' => bcrypt('password'),
            'role' => 'egitmen',
            'status' => 'approved',
            'department_id' => $dept->id,
        ]);

        $cRoomA = Classroom::create([
            'name' => 'Room A',
            'capacity' => 30,
            'department_id' => $dept->id,
        ]);

        $cRoomB = Classroom::create([
            'name' => 'Room B',
            'capacity' => 40,
            'department_id' => $dept->id,
        ]);

        $exam = Exam::create([
            'name' => 'Large Exam',
            'student_count' => 60,
            'start_time' => '2026-06-15 10:00:00',
            'end_time' => '2026-06-15 12:00:00',
            'instructor_id' => $instructor->id,
            'department_id' => $dept->id,
        ]);

        $this->allocationService->allocateExams();

        $exam->refresh();

        $this->assertEquals(2, $exam->classrooms->count());
        $this->assertTrue($exam->classrooms->contains($cRoomA->id));
        $this->assertTrue($exam->classrooms->contains($cRoomB->id));
    }
}
