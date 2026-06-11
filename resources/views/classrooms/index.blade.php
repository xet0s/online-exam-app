@extends('layouts.app', ['title' => 'Derslikler - Sınav Dağıtım'])

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Derslik Yönetimi</h1>
            <p class="text-sm text-slate-500 mt-1">Sınav dağıtım motorunun kullanacağı derslikleri ekleyin, düzenleyin ve yönetin.</p>
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isDean())
            <div>
                <a href="{{ route('classrooms.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-none bg-indigo-600 hover:bg-indigo-500 text-sm font-bold text-white shadow-sm transition-colors">
                    <svg class="h-4.5 w-4.5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Yeni Derslik Ekle
                </a>
            </div>
        @endif
    </div>

    
    <div class="glass rounded-none shadow-sm overflow-hidden bg-white border border-slate-200">
        @if($classrooms->isEmpty())
            <div class="text-center py-16 px-4">
                <div class="h-12 w-12 rounded-none bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mx-auto mb-4">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Derslik Bulunmuyor</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Tanımlanmış herhangi bir derslik bulunmamaktadır. Dağıtım işleminin yapılabilmesi için derslik eklemelisiniz.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                            <th class="py-4.5 px-6">Bina Adı</th>
                            <th class="py-4.5 px-6">Derslik Adı</th>
                            <th class="py-4.5 px-6">Bölüm</th>
                            <th class="py-4.5 px-6 text-center">Kapasite</th>
                            @if(auth()->user()->isAdmin() || auth()->user()->isDean())
                                <th class="py-4.5 px-6 text-center">İşlemler</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm text-slate-600">
                        @foreach($classrooms as $room)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4 px-6 text-slate-800">
                                    {{ $room->building ?? '-' }}
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-800">
                                    {{ $room->name }}
                                </td>
                                <td class="py-4 px-6 text-slate-500">
                                    {{ $room->department->name ?? 'Fakülte Ortak' }}
                                </td>
                                <td class="py-4 px-6 text-center font-mono font-bold text-slate-700">
                                    {{ $room->capacity }}
                                </td>
                                @if(auth()->user()->isAdmin() || auth()->user()->isDean())
                                    <td class="py-4 px-6 text-center space-x-1.5">
                                        <a href="{{ route('classrooms.edit', $room->id) }}" class="p-1 px-2.5 rounded-none bg-white border border-slate-200 hover:bg-slate-50 text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-all inline-block">Düzenle</a>
                                        <form action="{{ route('classrooms.destroy', $room->id) }}" method="POST" class="inline" onsubmit="return confirm('Bu dersliği silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 px-2.5 rounded-none bg-white border border-slate-200 hover:bg-rose-50 hover:border-rose-250 text-xs font-bold text-rose-600 transition-all cursor-pointer">Sil</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
