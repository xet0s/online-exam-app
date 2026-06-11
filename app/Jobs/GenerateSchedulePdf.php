<?php

namespace App\Jobs;

use App\Models\Department;
use App\Models\Exam;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateSchedulePdf implements ShouldQueue
{
    use Queueable;

    protected int  $userId;
    protected ?int $departmentId;

    public function __construct(int $userId, ?int $departmentId = null)
    {
        $this->userId       = $userId;
        $this->departmentId = $departmentId;
    }

    public function handle(): void
    {
        $statusKey = $this->departmentId
            ? "pdf_status_{$this->userId}_dept_{$this->departmentId}"
            : "pdf_status_{$this->userId}";

        $pathKey = $this->departmentId
            ? "pdf_path_{$this->userId}_dept_{$this->departmentId}"
            : "pdf_path_{$this->userId}";

        Cache::put($statusKey, 'pending', 3600);

        try {
            $user  = User::with('department')->findOrFail($this->userId);
            $title = '';
            $exams = collect();

            if ($this->departmentId) {
                // ── Bölüm bazlı PDF ────────────────────────────────────
                $dept  = Department::findOrFail($this->departmentId);
                $title = $dept->name . ' Bölümü Sınav Programı';

                $exams = Exam::with(['instructor', 'department', 'classrooms'])
                    ->where('department_id', $this->departmentId)
                    ->orderBy('start_time')
                    ->get();

            } elseif ($user->isAdmin() || $user->isDean()) {
                // ── Tüm fakülte PDF'i ───────────────────────────────────
                $title = 'Fakülte Geneli Sınav ve Derslik Tahsis Programı';

                $exams = Exam::with(['instructor', 'department', 'classrooms'])
                    ->orderBy('start_time')
                    ->get();

            } elseif ($user->isChair()) {
                // ── Bölüm başkanı: kendi bölümü ────────────────────────
                $title = ($user->department->name ?? 'Bölüm') . ' Sınav ve Derslik Tahsis Programı';

                $exams = Exam::with(['instructor', 'department', 'classrooms'])
                    ->where('department_id', $user->department_id)
                    ->orderBy('start_time')
                    ->get();

            } else {
                // ── Eğitmen: kendi sınavları ───────────────────────────
                $title = $user->name . ' - Kişisel Sınav Görev Programı';

                $exams = Exam::with(['instructor', 'department', 'classrooms'])
                    ->where('instructor_id', $user->id)
                    ->orderBy('start_time')
                    ->get();
            }

            $directory = storage_path('app/public/schedules');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }

            $pdf = Pdf::loadView('pdf.schedule', [
                'user'        => $user,
                'exams'       => $exams,
                'title'       => $title,
                'generatedAt' => now()->format('d.m.Y H:i:s'),
            ]);

            $suffix   = $this->departmentId ? "_dept_{$this->departmentId}" : '';
            $fileName = 'schedule_' . $this->userId . $suffix . '.pdf';
            $filePath = $directory . '/' . $fileName;

            $pdf->save($filePath);

            Cache::put($statusKey, 'completed', 3600);
            Cache::put($pathKey,   $fileName,   3600);

        } catch (Exception $e) {
            Log::error("PDF Generation failed for user {$this->userId}: " . $e->getMessage());
            Cache::put($statusKey, 'failed', 3600);
            throw $e;
        }
    }
}
