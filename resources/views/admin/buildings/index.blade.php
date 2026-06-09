@extends('layouts.app', ['title' => 'Binalar - Sınav Dağıtım'])

@section('content')
<div class="relative z-10">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Binalar</h1>
            <p class="text-sm text-slate-500 mt-1">Derslik binalarını yönetin.</p>
        </div>
        <a href="{{ route('buildings.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-none bg-indigo-600 hover:bg-indigo-500 text-sm font-bold text-white shadow-sm transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Yeni Bina
        </a>
    </div>

    @if($buildings->isEmpty())
        <div class="glass p-12 rounded-none border border-slate-200 text-center">
            <svg class="h-12 w-12 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <p class="text-slate-500 text-sm font-medium">Henüz bina eklenmemiş.</p>
            <a href="{{ route('buildings.create') }}" class="mt-4 inline-block text-indigo-600 text-sm font-semibold hover:underline">İlk binayı ekle →</a>
        </div>
    @else
        <div class="glass rounded-none border border-slate-200 overflow-hidden shadow-sm">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Bina Adı</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kodu</th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($buildings as $building)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-slate-800">{{ $building->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($building->code)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-xs font-mono font-semibold border border-slate-200">{{ $building->code }}</span>
                                @else
                                    <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('buildings.edit', $building) }}"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-none bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 transition-all">
                                        Düzenle
                                    </a>
                                    <form action="{{ route('buildings.destroy', $building) }}" method="POST"
                                        onsubmit="return confirm('{{ $building->name }} binasını silmek istediğinize emin misiniz?')">
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
