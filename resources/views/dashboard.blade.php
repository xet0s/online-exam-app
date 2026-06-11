@extends('layouts.app', ['title' => 'Yönetim Paneli - Sınav Dağıtım'])

@section('content')
<div class="space-y-8">

    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Yönetim Paneli</h1>
            <p class="text-sm text-slate-500 mt-1">Sınav programı ve derslik dağıtım durumunu bu ekrandan takip edebilirsiniz.</p>
        </div>

        <div class="flex flex-wrap gap-3 items-center">

            @if(auth()->user()->isAdmin() || auth()->user()->isDean())
                <form action="{{ route('allocation.run') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-none bg-indigo-600 hover:bg-indigo-500 text-sm font-bold text-white shadow-sm transition-colors cursor-pointer">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        Otomatik Dağıtımı Çalıştır
                    </button>
                </form>
            @endif

            
            <div class="inline-flex items-center rounded-none p-1 bg-white border border-slate-200">
                @if($pdfStatus === 'pending')
                    <span class="text-xs text-slate-600 px-3 py-1.5 flex items-center">
                        <svg class="animate-spin h-4 w-4 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        PDF Hazırlanıyor...
                    </span>
                    <a href="{{ route('dashboard') }}" class="p-1 px-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-none text-xs font-semibold border border-slate-200 transition-all">Yenile</a>
                @elseif($pdfStatus === 'completed')
                    <a href="{{ route('pdf.download') }}" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-none text-xs font-bold transition-all shadow-md">
                        Tüm Program PDF İndir
                    </a>
                    <form action="{{ route('pdf.export') }}" method="POST" class="inline pl-1">
                        @csrf
                        <button type="submit" class="p-1 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-none text-xs font-semibold border border-slate-200 transition-all cursor-pointer">Yenile</button>
                    </form>
                @else
                    <form action="{{ route('pdf.export') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-none text-xs font-bold border border-slate-200 transition-all cursor-pointer">
                            Tüm Program PDF Al
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    
    @if(!auth()->user()->isInstructor() && !empty($stats))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @if(auth()->user()->isAdmin() || auth()->user()->isDean())
                <div class="glass p-6 rounded-none bg-white border border-slate-200 shadow-sm">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Bölüm Sayısı</p>
                    <h3 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $stats['departments_count'] }}</h3>
                </div>
            @endif
            <div class="glass p-6 rounded-none bg-white border border-slate-200 shadow-sm">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Derslik Sayısı</p>
                <h3 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $stats['classrooms_count'] }}</h3>
            </div>
            <div class="glass p-6 rounded-none bg-white border border-slate-200 shadow-sm">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sınav Sayısı</p>
                <h3 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $stats['exams_count'] }}</h3>
            </div>
            <div class="glass p-6 rounded-none bg-white border border-slate-200 shadow-sm">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Onay Bekleyen Eğitmen</p>
                <h3 class="text-3xl font-extrabold text-slate-800 mt-2 flex items-center justify-between">
                    <span>{{ $stats['pending_count'] }}</span>
                    @if($stats['pending_count'] > 0)
                        <a href="{{ route('approvals.index') }}" class="text-[10px] text-amber-600 hover:text-amber-700 underline font-semibold normal-case">İncele</a>
                    @endif
                </h3>
            </div>
        </div>
    @endif

    
    @if(auth()->user()->isAdmin() || auth()->user()->isDean() || auth()->user()->isChair())
    <div class="glass rounded-none bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-violet-50 to-indigo-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-none bg-violet-100 border border-violet-200 flex items-center justify-center">
                    <svg class="h-4 w-4 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Sınav Haftası Ayarları</h2>
                    <p class="text-xs text-slate-500">Otomatik tarih atama için sınav dönemi aralığını tanımlayın.</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                @foreach($allPeriods as $period)
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-none bg-white border border-violet-200 text-xs">
                        <span class="font-semibold text-violet-800">
                            {{ $period->department ? $period->department->name : 'Fakülte Geneli' }}:
                        </span>
                        <span class="text-slate-600">{{ $period->start_date->format('d.m.Y') }} – {{ $period->end_date->format('d.m.Y') }}</span>
                        @if(auth()->user()->isAdmin() || auth()->user()->isDean() || (auth()->user()->isChair() && $period->department_id == auth()->user()->department_id))
                            <form action="{{ route('exam-periods.destroy', $period->id) }}" method="POST" class="inline" onsubmit="return confirm('Bu sınav haftası tanımını silmek istiyor musunuz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-1 text-rose-400 hover:text-rose-600 transition-colors cursor-pointer" title="Sil">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="px-6 py-5">
            <form action="{{ route('exam-periods.store') }}" method="POST" class="flex flex-wrap items-end gap-4">
                @csrf

                <div class="flex-1 min-w-[160px]">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Dönem Adı</label>
                    <input type="text" name="name" value="{{ old('name', 'Dönem Sonu Sınav Haftası') }}" required
                        class="appearance-none block w-full px-3 py-2 border border-slate-300 rounded-none bg-white text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Başlangıç Tarihi</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                        class="appearance-none block px-3 py-2 border border-slate-300 rounded-none bg-white text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Bitiş Tarihi</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required
                        class="appearance-none block px-3 py-2 border border-slate-300 rounded-none bg-white text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all">
                </div>

                @if(auth()->user()->isAdmin() || auth()->user()->isDean())
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Kapsam</label>
                        <select name="department_id"
                            class="appearance-none block px-3 py-2 border border-slate-300 rounded-none bg-white text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all cursor-pointer">
                            <option value="">Fakülte Geneli</option>
                            @foreach(\App\Models\Department::orderBy('name')->get() as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <button type="submit"
                    class="px-4 py-2 rounded-none bg-violet-600 hover:bg-violet-500 text-white text-sm font-bold shadow-sm transition-colors cursor-pointer inline-flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Kaydet
                </button>
            </form>

            @if($errors->any())
                <div class="mt-3 text-xs text-rose-600">
                    @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
                </div>
            @endif
        </div>
    </div>
    @endif

    @if($examsByDepartment->isEmpty())
        <div class="glass rounded-none bg-white border border-slate-200 p-16 text-center">
            <div class="h-12 w-12 rounded-none bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mx-auto mb-4">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h4 class="text-sm font-bold text-slate-800">Sınav Tanımı Bulunmuyor</h4>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Henüz sınav kaydı oluşturulmamış veya yetki dahilinde listelenecek sınav bulunmamaktadır.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($examsByDepartment as $group)
                @php
                    $dept      = $group['department'];
                    $deptExams = $group['exams'];
                    $deptId    = $dept->id ?? null;

                    
                    $deptPdfStatusKey = "pdf_status_" . auth()->id() . "_dept_{$deptId}";
                    $deptPdfPathKey   = "pdf_path_"   . auth()->id() . "_dept_{$deptId}";
                    $deptPdfStatus    = \Illuminate\Support\Facades\Cache::get($deptPdfStatusKey, 'idle');
                @endphp

                <div class="glass rounded-none shadow-sm overflow-hidden bg-white border border-slate-200">

                    
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-none bg-indigo-100 border border-indigo-200 flex items-center justify-center flex-shrink-0">
                                <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">{{ $dept->name ?? 'Bölüm' }}</h2>
                                <p class="text-xs text-slate-500">{{ $deptExams->count() }} sınav</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap">

                            @if(auth()->user()->isAdmin() || auth()->user()->isDean() || auth()->user()->isChair() || auth()->user()->isInstructor())
                                <a href="{{ route('exams.create') }}"
                                   class="inline-flex items-center px-3 py-1.5 rounded-none bg-white border border-slate-200 hover:bg-indigo-50 hover:border-indigo-300 text-xs font-semibold text-indigo-600 transition-all">
                                    <svg class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Yeni Sınav
                                </a>
                            @endif

                            
                            @if($deptId)
                                @if($deptPdfStatus === 'pending')
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-none bg-amber-50 border border-amber-200 text-xs font-semibold text-amber-700">
                                        <svg class="animate-spin h-3.5 w-3.5 mr-1.5 text-amber-600" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        PDF Hazırlanıyor...
                                    </span>
                                    <a href="{{ route('dashboard') }}" class="px-2.5 py-1.5 rounded-none bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold border border-slate-200 transition-all">Yenile</a>
                                @elseif($deptPdfStatus === 'completed')
                                    <a href="{{ route('pdf.download', ['department_id' => $deptId]) }}"
                                       class="inline-flex items-center px-3 py-1.5 rounded-none bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-sm">
                                        <svg class="h-3.5 w-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        PDF İndir
                                    </a>
                                    <form action="{{ route('pdf.export') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="department_id" value="{{ $deptId }}">
                                        <button type="submit" class="px-2.5 py-1.5 rounded-none bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold border border-slate-200 transition-all cursor-pointer">Yenile</button>
                                    </form>
                                @else
                                    <form action="{{ route('pdf.export') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="department_id" value="{{ $deptId }}">
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 rounded-none bg-white border border-slate-200 hover:bg-slate-50 text-xs font-semibold text-slate-600 transition-all cursor-pointer">
                                            <svg class="h-3.5 w-3.5 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            PDF Al
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>

                    
                    @if($deptExams->isEmpty())
                        <div class="text-center py-10 text-slate-400 text-sm">
                            Bu bölüme ait sınav kaydı bulunmamaktadır.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200 bg-slate-50/70">
                                        <th class="py-3.5 px-5">Sınav Adı</th>
                                        <th class="py-3.5 px-5">Dersi Veren</th>
                                        <th class="py-3.5 px-5">Gözetmen</th>
                                        <th class="py-3.5 px-5 text-center">Öğrenci</th>
                                        <th class="py-3.5 px-5">Tarih / Saat</th>
                                        <th class="py-3.5 px-5">Tahsis Edilen Derslik</th>
                                        <th class="py-3.5 px-5 text-center">Durum</th>
                                        <th class="py-3.5 px-5 text-center">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                                    @foreach($deptExams as $exam)
                                        <tr class="hover:bg-slate-50/60 transition-colors">
                                            <td class="py-4 px-5 font-semibold text-slate-800">{{ $exam->name }}</td>

                                            <td class="py-4 px-5 text-slate-600 text-xs">
                                                {{ $exam->instructor->name ?? '-' }}
                                            </td>

                                            <td class="py-4 px-5 text-xs">
                                                @if($exam->supervisor)
                                                    <span class="inline-flex items-center gap-1 text-indigo-700">
                                                        <svg class="h-3 w-3 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                        {{ $exam->supervisor->name }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 italic">Otomatik atanacak</span>
                                                @endif
                                            </td>

                                            <td class="py-4 px-5 text-center text-slate-500 font-mono text-xs">{{ $exam->student_count }}</td>

                                            <td class="py-4 px-5 text-xs font-medium">
                                                @if($exam->start_time && $exam->end_time)
                                                    <div class="text-slate-800 font-semibold">{{ $exam->start_time->format('d.m.Y') }}</div>
                                                    <div class="text-slate-500 mt-0.5">{{ $exam->start_time->format('H:i') }} – {{ $exam->end_time->format('H:i') }}</div>
                                                @else
                                                    <div class="inline-flex items-center px-2 py-0.5 rounded-none bg-rose-50 border border-rose-200 text-rose-600 text-[10px] font-semibold">
                                                        <svg class="h-2.5 w-2.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        Tarih Atanmadı
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="py-4 px-5">
                                                @if($exam->classrooms->isNotEmpty())
                                                    <div class="flex flex-wrap gap-1 max-w-[180px]">
                                                        @foreach($exam->classrooms as $room)
                                                            <div class="inline-flex items-center px-2 py-0.5 rounded-none bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-semibold">
                                                                <svg class="h-2.5 w-2.5 mr-1 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                                </svg>
                                                                {{ $room->name }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="inline-flex items-center px-2 py-0.5 rounded-none bg-rose-50 border border-rose-200 text-rose-600 text-[10px] font-semibold">
                                                        <svg class="h-2.5 w-2.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                        Atanmadı
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="py-4 px-5 text-center">
                                                @if($exam->isApproved())
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-none text-[10px] font-semibold bg-emerald-50 border border-emerald-200 text-emerald-700">Onaylandı</span>
                                                @elseif($exam->isSentToDean())
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-none text-[10px] font-semibold bg-indigo-50 border border-indigo-200 text-indigo-700">Dekan Onayı</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-none text-[10px] font-semibold bg-amber-50 border border-amber-200 text-amber-700">Bölüm Onayı</span>
                                                @endif
                                            </td>

                                            <td class="py-4 px-5 text-center space-x-1">
                                                @if($exam->isPending() && (auth()->user()->isAdmin() || (auth()->user()->isChair() && auth()->user()->department_id === $exam->department_id)))
                                                    <form action="{{ route('exams.approve-chair', $exam->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="p-1 px-2 rounded-none bg-white border border-slate-200 hover:bg-indigo-50 text-[10px] font-bold text-indigo-600 cursor-pointer">Onayla & İlet</button>
                                                    </form>
                                                @endif
                                                @if($exam->isSentToDean() && (auth()->user()->isAdmin() || auth()->user()->isDean()))
                                                    <form action="{{ route('exams.approve-dean', $exam->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="p-1 px-2 rounded-none bg-white border border-slate-200 hover:bg-emerald-50 text-[10px] font-bold text-emerald-600 cursor-pointer">Onayla</button>
                                                    </form>
                                                @endif
                                                @can('update', $exam)
                                                    <a href="{{ route('exams.edit', $exam->id) }}" class="p-1 px-2 rounded-none bg-white border border-slate-200 hover:bg-slate-50 text-[10px] font-bold text-indigo-600 hover:text-indigo-700 transition-all inline-block">Düzenle</a>
                                                @endcan
                                                @can('delete', $exam)
                                                    <form action="{{ route('exams.destroy', $exam->id) }}" method="POST" class="inline" onsubmit="return confirm('Bu sınav kaydını silmek istediğinize emin misiniz?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1 px-2 rounded-none bg-white border border-slate-200 hover:bg-rose-50 hover:border-rose-200 text-[10px] font-bold text-rose-600 transition-all cursor-pointer">Sil</button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
