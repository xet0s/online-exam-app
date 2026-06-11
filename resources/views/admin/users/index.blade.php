@extends('layouts.app', ['title' => 'Kullanıcı Yönetimi - Sınav Dağıtım'])

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Kullanıcı Yönetimi</h1>
            <p class="text-sm text-slate-500 mt-1">Sistemdeki tüm dekan, bölüm başkanı ve eğitmen hesaplarını bu panelden yönetin.</p>
        </div>
        <div>
            <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-none bg-indigo-600 hover:bg-indigo-500 text-sm font-bold text-white shadow-sm transition-colors">
                <svg class="h-4.5 w-4.5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Yeni Kullanıcı Ekle
            </a>
        </div>
    </div>

    
    <div class="glass rounded-none shadow-sm overflow-hidden bg-white border border-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <th class="py-4.5 px-6">Ad Soyad</th>
                        <th class="py-4.5 px-6">E-posta</th>
                        <th class="py-4.5 px-6">Rol</th>
                        <th class="py-4.5 px-6">Bölüm</th>
                        <th class="py-4.5 px-6 text-center">Durum</th>
                        <th class="py-4.5 px-6 text-center">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm text-slate-600">
                    @foreach($users as $u)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-6 font-semibold text-slate-800">
                                {{ $u->name }}
                                @if($u->id === auth()->id())
                                    <span class="text-[10px] text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-none ml-1 font-bold">Siz</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-slate-500">
                                {{ $u->email }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-none text-xs font-bold capitalize border
                                    {{ $u->isAdmin() ? 'bg-rose-50 border-rose-200 text-rose-700' :
                                       ($u->isDean() ? 'bg-purple-50 border-purple-200 text-purple-700' :
                                       ($u->isChair() ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'bg-slate-100 border-slate-200 text-slate-600')) }}">
                                    {{ $u->role === 'admin' ? 'Admin' : ($u->role === 'dekan' ? 'Dekan' : ($u->role === 'bolum_baskani' ? 'Bölüm Başkanı' : 'Eğitmen')) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-500">
                                {{ $u->department->name ?? '-' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($u->isApproved())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-none text-xs font-semibold bg-emerald-50 border border-emerald-200 text-emerald-700">
                                        Onaylı
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-none text-xs font-semibold bg-amber-50 border border-amber-200 text-amber-700">
                                        Onay Bekliyor
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center space-x-1.5">
                                <a href="{{ route('users.edit', $u->id) }}" class="p-1 px-2.5 rounded-none bg-white border border-slate-200 hover:bg-slate-50 text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-all inline-block">Düzenle</a>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Bu kullanıcıyı tamamen silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 px-2.5 rounded-none bg-white border border-slate-200 hover:bg-rose-50 hover:border-rose-250 text-xs font-bold text-rose-600 transition-all cursor-pointer">Sil</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
