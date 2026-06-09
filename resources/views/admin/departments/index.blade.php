@extends('layouts.app', ['title' => 'Bölümler - Sınav Dağıtım'])

@section('content')
<div class="relative z-10">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Bölümler</h1>
            <p class="text-sm text-slate-500 mt-1">Fakülte bölümlerini yönetin.</p>
        </div>
        <a href="{{ route('departments.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-none bg-indigo-600 hover:bg-indigo-500 text-sm font-bold text-white shadow-sm transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Yeni Bölüm
        </a>
    </div>

    @if($departments->isEmpty())
        <div class="glass p-12 rounded-none border border-slate-200 text-center">
            <svg class="h-12 w-12 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <p class="text-slate-500 text-sm font-medium">Henüz bölüm eklenmemiş.</p>
            <a href="{{ route('departments.create') }}" class="mt-4 inline-block text-indigo-600 text-sm font-semibold hover:underline">İlk bölümü ekle →</a>
        </div>
    @else
        <div class="glass rounded-none border border-slate-200 overflow-hidden shadow-sm">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Bölüm Adı</th>
                        <th class="px-6 py-3.5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Kullanıcılar</th>
                        <th class="px-6 py-3.5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Derslikler</th>
                        <th class="px-6 py-3.5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Sınavlar</th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($departments as $department)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-slate-800">{{ $department->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold border border-indigo-100">
                                    {{ $department->users_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-100">
                                    {{ $department->classrooms_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold border border-amber-100">
                                    {{ $department->exams_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('departments.edit', $department) }}"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-none bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 transition-all">
                                        Düzenle
                                    </a>
                                    <form action="{{ route('departments.destroy', $department) }}" method="POST"
                                        onsubmit="return confirm('{{ $department->name }} bölümünü silmek istediğinize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-none bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-600 transition-all cursor-pointer">
                                            Sil
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
