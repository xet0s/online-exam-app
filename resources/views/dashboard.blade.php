@extends('layouts.app', ['title' => 'Yönetim Paneli - Sınav Dağıtım'])

@section('content')
<div class="space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Yönetim Paneli</h1>
            <p class="text-sm text-slate-500 mt-1">Sınav programı ve derslik dağıtım durumunu bu ekrandan takip edebilirsiniz.</p>
        </div>

        <div class="flex flex-wrap gap-3">
            @if(auth()->user()->isAdmin() || auth()->user()->isDean())
                <form action="{{ route('allocation.run') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-none bg-indigo-600 hover:bg-indigo-500 text-sm font-bold text-white shadow-sm transition-colors cursor-pointer">
                        <svg class="h-4.5 w-4.5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        Otomatik Dağıtımı Çalıştır
                    </button>
                </form>
            @endif

            <!-- PDF Export Flow -->
            <div class="inline-flex items-center glass rounded-none p-1 bg-white border border-slate-200">
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
                    <a href="{{ route('pdf.download') }}" class="px-3.5 py-1.5 bg-emerald-650 hover:bg-emerald-600 text-white rounded-none text-xs font-bold transition-all shadow-md bg-emerald-600">
                        PDF İndir
                    </a>
                    <form action="{{ route('pdf.export') }}" method="POST" class="inline pl-1">
                        @csrf
                        <button type="submit" class="p-1 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-none text-xs font-semibold border border-slate-200 transition-all cursor-pointer">
                            Yenile
                        </button>
                    </form>
                @else
                    <form action="{{ route('pdf.export') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-none text-xs font-bold border border-slate-200 transition-all cursor-pointer">
                            PDF Çıktısı Al
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @if(!auth()->user()->isInstructor())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @if(auth()->user()->isAdmin() || auth()->user()->isDean())
                <!-- Departments Count -->
                <div class="glass p-6 rounded-none bg-white border border-slate-200 shadow-sm">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Bölüm Sayısı</p>
                    <h3 class="text-3xl font-extrabold text-slate-850 mt-2">{{ $stats['departments_count'] }}</h3>
                </div>
            @endif

            <!-- Classrooms Count -->
            <div class="glass p-6 rounded-none bg-white border border-slate-200 shadow-sm">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Derslik Sayısı</p>
                <h3 class="text-3xl font-extrabold text-slate-850 mt-2">{{ $stats['classrooms_count'] }}</h3>
            </div>

            <!-- Exams Count -->
            <div class="glass p-6 rounded-none bg-white border border-slate-200 shadow-sm">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sınav Sayısı</p>
                <h3 class="text-3xl font-extrabold text-slate-850 mt-2">{{ $stats['exams_count'] }}</h3>
            </div>

            <!-- Pending Approvals Count -->
            <div class="glass p-6 rounded-none bg-white border border-slate-200 shadow-sm">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Onay Bekleyen Eğitmen</p>
                <h3 class="text-3xl font-extrabold text-slate-850 mt-2 flex items-center justify-between">
                    <span>{{ $stats['pending_count'] }}</span>
                    @if($stats['pending_count'] > 0)
                        <a href="{{ route('approvals.index') }}" class="text-[10px] text-amber-600 hover:text-amber-700 underline font-semibold normal-case">İncele</a>
                    @endif
                </h3>
            </div>
        </div>
    @endif

    <!-- Exams Scheduling Table -->
    <div class="glass rounded-none shadow-sm overflow-hidden bg-white border border-slate-200">
        <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    {{ auth()->user()->isInstructor() ? 'Kişisel Sınav Görevlerim' : 'Sınav Programı ve Tahsis Listesi' }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Sınavların saat, gözetmen ve derslik dağıtım durumunu listeler.</p>
            </div>

            @if(auth()->user()->isAdmin() || auth()->user()->isDean() || auth()->user()->isChair() || auth()->user()->isInstructor())
                <a href="{{ route('exams.create') }}" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-500 transition-colors">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Yeni Sınav Tanımla
                </a>
            @endif
        </div>

        @if($exams->isEmpty())
            <div class="text-center py-16 px-4">
                <div class="h-12 w-12 rounded-none bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mx-auto mb-4">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Sınav Tanımı Bulunmuyor</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Herhangi bir sınav kaydı oluşturulmamış veya yetki dahilinde listelenecek sınav bulunmamaktadır.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-55 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200 bg-slate-50">
                            <th class="py-4.5 px-6">Sınav Adı</th>
                            @if(auth()->user()->isAdmin() || auth()->user()->isDean())
                                <th class="py-4.5 px-6">Bölüm</th>
                            @endif
                            <th class="py-4.5 px-6">Gözetmen</th>
                            <th class="py-4.5 px-6 text-center">Öğrenci</th>
                            <th class="py-4.5 px-6">Tarih / Saat</th>
                            <th class="py-4.5 px-6">Tahsis Edilen Derslik</th>
                            <th class="py-4.5 px-6 text-center">Durum</th>
                            <th class="py-4.5 px-6 text-center">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm text-slate-600">
                        @foreach($exams as $exam)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4.5 px-6 font-semibold text-slate-800">
                                    {{ $exam->name }}
                                </td>
                                @if(auth()->user()->isAdmin() || auth()->user()->isDean())
                                    <td class="py-4.5 px-6 text-xs text-slate-500">
                                        {{ $exam->department->name ?? '-' }}
                                    </td>
                                @endif
                                <td class="py-4.5 px-6 text-slate-600">
                                    {{ $exam->instructor->name ?? '-' }}
                                </td>
                                <td class="py-4.5 px-6 text-center text-slate-500 font-mono">
                                    {{ $exam->student_count }}
                                </td>
                                <td class="py-4.5 px-6 text-xs font-medium">
                                    <div class="text-slate-800">{{ $exam->start_time->format('d.m.Y') }}</div>
                                    <div class="text-slate-500 mt-0.5">{{ $exam->start_time->format('H:i') }} - {{ $exam->end_time->format('H:i') }}</div>
                                </td>
                                <td class="py-4.5 px-6">
                                    @if($exam->classrooms->isNotEmpty())
                                        <div class="flex flex-wrap gap-1 max-w-[200px]">
                                            @foreach($exam->classrooms as $room)
                                                <div class="inline-flex items-center px-2 py-0.5 rounded-none bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-semibold">
                                                    <svg class="h-2.5 w-2.5 mr-1 text-emerald-650" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                    {{ $room->name }} (Kap: {{ $room->capacity }})
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-none bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold">
                                            <svg class="h-3 w-3 mr-1 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            Atanmadı
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4.5 px-6 text-center">
                                    @if($exam->isApproved())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-none text-[10px] font-semibold bg-emerald-50 border border-emerald-250 text-emerald-700">Onaylandı</span>
                                    @elseif($exam->isSentToDean())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-none text-[10px] font-semibold bg-indigo-50 border border-indigo-250 text-indigo-700">Dekan Onayı Bekliyor</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-none text-[10px] font-semibold bg-amber-50 border border-amber-250 text-amber-700">Bölüm Onayı Bekliyor</span>
                                    @endif
                                </td>
                                <td class="py-4.5 px-6 text-center space-x-1.5">
                                    @if($exam->isPending() && (auth()->user()->isAdmin() || (auth()->user()->isChair() && auth()->user()->department_id === $exam->department_id)))
                                        <form action="{{ route('exams.approve-chair', $exam->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1 px-2.5 rounded-none bg-white border border-slate-200 hover:bg-indigo-50 text-xs font-bold text-indigo-600 cursor-pointer">Onayla & İlet</button>
                                        </form>
                                    @endif
                                    @if($exam->isSentToDean() && (auth()->user()->isAdmin() || auth()->user()->isDean()))
                                        <form action="{{ route('exams.approve-dean', $exam->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1 px-2.5 rounded-none bg-white border border-slate-200 hover:bg-emerald-50 text-xs font-bold text-emerald-600 cursor-pointer">Onayla</button>
                                        </form>
                                    @endif
                                    @can('update', $exam)
                                        <a href="{{ route('exams.edit', $exam->id) }}" class="p-1 px-2.5 rounded-none bg-white border border-slate-200 hover:bg-slate-50 text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-all inline-block">Düzenle</a>
                                    @endcan
                                    @can('delete', $exam)
                                        <form action="{{ route('exams.destroy', $exam->id) }}" method="POST" class="inline" onsubmit="return confirm('Bu sınav kaydını silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 px-2.5 rounded-none bg-white border border-slate-200 hover:bg-rose-50 hover:border-rose-250 text-xs font-bold text-rose-600 transition-all cursor-pointer">Sil</button>
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
</div>
@endsection
