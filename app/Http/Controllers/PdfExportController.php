<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateSchedulePdf;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class PdfExportController extends Controller
{

    /**
     * Tüm program veya belirli bir bölüm için PDF kuyruğa al.
     * department_id verilirse sadece o bölümün PDF'i üretilir.
     */
    public function export(Request $request)
    {
        $userId       = Auth::id();
        $departmentId = $request->input('department_id'); // null = genel

        // Bölüm bazlı PDF için cache key'i ayır
        $cacheKey = $departmentId
            ? "pdf_status_{$userId}_dept_{$departmentId}"
            : "pdf_status_{$userId}";

        Cache::put($cacheKey, 'pending', 3600);

        GenerateSchedulePdf::dispatch($userId, $departmentId ? (int) $departmentId : null);

        $message = $departmentId
            ? 'Bölüm PDF programı kuyruğa eklendi. Hazırlandığında indirme linki belirecektir.'
            : 'PDF programı kuyruğa eklendi. Hazırlandığında indirme linki belirecektir.';

        return redirect()->route('dashboard')->with('status', $message);
    }

    /**
     * Üretilen PDF'i indir.
     * department_id verilirse o bölümün PDF dosyası döndürülür.
     */
    public function download(Request $request)
    {
        $userId       = Auth::id();
        $departmentId = $request->input('department_id');

        $cacheKey     = $departmentId
            ? "pdf_path_{$userId}_dept_{$departmentId}"
            : "pdf_path_{$userId}";

        $statusKey    = $departmentId
            ? "pdf_status_{$userId}_dept_{$departmentId}"
            : "pdf_status_{$userId}";

        $fileName = Cache::get($cacheKey);

        if (!$fileName) {
            return redirect()->route('dashboard')
                ->with('error', 'İndirilecek program dosyası bulunamadı. Lütfen önce PDF çıktısı alın.');
        }

        $fileName = basename($fileName);
        $filePath = storage_path('app/public/schedules/' . $fileName);

        if (!File::exists($filePath)) {
            Cache::forget($statusKey);
            Cache::forget($cacheKey);

            return redirect()->route('dashboard')
                ->with('error', 'Dosya sunucuda bulunamadı. Lütfen programı tekrar üretin.');
        }

        if ($departmentId) {
            $dept         = Department::find($departmentId);
            $downloadName = 'sinav_programi_' . \Str::slug($dept->name ?? 'bolum') . '.pdf';
        } else {
            $downloadName = 'sinav_programi.pdf';
        }

        return response()->download($filePath, $downloadName, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
        ]);
    }
}
