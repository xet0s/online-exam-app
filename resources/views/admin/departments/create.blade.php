@extends('layouts.app', ['title' => 'Yeni Bölüm Ekle - Sınav Dağıtım'])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="relative z-10">
        <div class="mb-6">
            <a href="{{ route('departments.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors flex items-center">
                &larr; Bölüm Listesine Dön
            </a>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 mt-2">Yeni Bölüm Ekle</h1>
            <p class="text-sm text-slate-500 mt-1">Sisteme yeni bir fakülte bölümü ekleyin.</p>
        </div>

        <div class="glass p-8 rounded-none shadow-sm bg-white border border-slate-200">
            <form action="{{ route('departments.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700">Bölüm Adı</label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}"
                        class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm @error('name') border-rose-500/50 @enderror"
                        placeholder="Bilgisayar Mühendisliği">
                    @error('name') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2 flex justify-end space-x-3">
                    <a href="{{ route('departments.index') }}" class="px-5 py-2.5 rounded-none bg-slate-100 hover:bg-slate-200 border border-slate-200 text-sm font-semibold text-slate-700 transition-all">
                        İptal
                    </a>
                    <button type="submit" class="px-5 py-2.5 rounded-none bg-indigo-600 hover:bg-indigo-500 text-sm font-bold text-white shadow-sm transition-colors cursor-pointer">
                        Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
