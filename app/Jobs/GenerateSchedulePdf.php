<?php

namespace App\Jobs;

use App\Models\Exam;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class GenerateSchedulePdf implements ShouldQueue
{
    use Queueable;

    protected int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function handle(): void
    {
        Cache::put("pdf_status_{$this->userId}", 'pending', 3600);

        try {
            $user = User::with('department')->findOrFail($this->userId);
            $title = '';
            $exams = [];

            if ($user->isAdmin() || $user->isDean()) {
                $title = 'Fakülte Geneli Sınav ve Derslik Tahsis Programı';
                $exams = Exam::with(['instructor', 'department', 'classrooms'])
                    ->orderBy('start_time')
                    ->get();
            } elseif ($user->isChair()) {
                $title = ($user->department->name ?? 'Bölüm') . ' Sınav ve Derslik Tahsis Programı';
                $exams = Exam::with(['instructor', 'department', 'classrooms'])
                    ->where('department_id', $user->department_id)
                    ->orderBy('start_time')
                    ->get();
            } else {
                $title = $user->name . ' - Kişisel Sınav Görev Programı';
                $exams = Exam::with(['department', 'classrooms'])
                    ->where('instructor_id', $user->id)
                    ->orderBy('start_time')
                    ->get();
            }

            $directory = storage_path('app/public/schedules');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }

            $pdf = Pdf::loadView('pdf.schedule', [
                'user' => $user,
                'exams' => $exams,
                'title' => $title,
                'generatedAt' => now()->format('d.m.Y H:i:s'),
            ]);

            $fileName = 'schedule_' . $user->id . '.pdf';
            $filePath = $directory . '/' . $fileName;

            $pdf->save($filePath);

            Cache::put("pdf_status_{$this->userId}", 'completed', 3600);
            Cache::put("pdf_path_{$this->userId}", $fileName, 3600);

        } catch (Exception $e) {
            Log::error("PDF Generation failed for user {$this->userId}: " . $e->getMessage());
            Cache::put("pdf_status_{$this->userId}", 'failed', 3600);
            throw $e;
        }
    }
}
