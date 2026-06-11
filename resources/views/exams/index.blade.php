@extends('layouts.app', ['title' => 'Sınavlar - Sınav Dağıtım'])

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Sınav Yönetimi</h1>
            <p class="text-sm text-slate-500 mt-1">Sınav tanımlarını, öğrenci sayılarını ve tarih bilgilerini yönetin.</p>
        </div>
        <div>
            <a href="{{ route('exams.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-none bg-indigo-600 hover:bg-indigo-500 text-sm font-bold text-white shadow-sm transition-colors">
                <svg class="h-4.5 w-4.5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Yeni Sınav Ekle
            </a>
        </div>
    </div>

    
    <div class="glass rounded-none shadow-sm overflow-hidden bg-white border border-slate-200">
        @if($exams->isEmpty())
            <div class="text-center py-16 px-4">
                <div class="h-12 w-12 rounded-none bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mx-auto mb-4 font-bold">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Sınav Bulunmuyor</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Sistemde kayıtlı herhangi bir sınav kaydı bulunmamaktadır.</p>
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
                            <th class="py-4.5 px-6 text-center">Öğrenci Sayısı</th>
                            <th class="py-4.5 px-6">Tarih / Saat</th>
                            <th class="py-4.5 px-6">Atanan Derslik</th>
                            <th class="py-4.5 px-6 text-center">Durum</th>
                            <th class="py-4.5 px-6 text-center">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm text-slate-600">
                        @foreach($exams as $exam)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4 px-6 font-semibold text-slate-800">
                                    {{ $exam->name }}
                                </td>
                                @if(auth()->user()->isAdmin() || auth()->user()->isDean())
                                    <td class="py-4 px-6 text-slate-500 text-xs">
                                        {{ $exam->department->name ?? '-' }}
                                    </td>
                                @endif
                                <td class="py-4 px-6 text-slate-600">
                                    {{ $exam->instructor->name ?? '-' }}
                                </td>
                                <td class="py-4 px-6 text-center font-mono text-slate-600">
                                    {{ $exam->student_count }}
                                </td>
                                <td class="py-4 px-6 text-xs">
                                    @if($exam->start_time && $exam->end_time)
                                        <div class="text-slate-800 font-medium">{{ $exam->start_time->format('d.m.Y') }}</div>
                                        <div class="text-slate-500 mt-0.5">{{ $exam->start_time->format('H:i') }} - {{ $exam->end_time->format('H:i') }}</div>
                                    @else
                                        <div class="text-rose-600 font-semibold">Tarih Atanmadı</div>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    @if($exam->classrooms->isNotEmpty())
                                        <div class="flex flex-wrap gap-1 max-w-[200px]">
                                            @foreach($exam->classrooms as $room)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-none bg-emerald-50 border border-emerald-250 text-emerald-700 text-[10px] font-semibold">
                                                    {{ $room->name }} (Kap: {{ $room->capacity }})
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-none bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold">
                                            Atanmadı
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($exam->isApproved())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-none text-[10px] font-semibold bg-emerald-50 border border-emerald-250 text-emerald-700">Onaylandı</span>
                                    @elseif($exam->isSentToDean())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-none text-[10px] font-semibold bg-indigo-50 border border-indigo-250 text-indigo-700">Dekan Onayı Bekliyor</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-none text-[10px] font-semibold bg-amber-50 border border-amber-250 text-amber-700">Bölüm Onayı Bekliyor</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center space-x-1.5">
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
