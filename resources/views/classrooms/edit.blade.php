@extends('layouts.app', ['title' => 'Derslik Düzenle - Sınav Dağıtım'])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="relative z-10">
        <div class="mb-6">
            <a href="{{ route('classrooms.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors flex items-center">
                &larr; Derslik Listesine Dön
            </a>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 mt-2">Derslik Düzenle</h1>
            <p class="text-sm text-slate-500 mt-1">Derslik kapasitesi ve bölüm bilgilerini güncelleyin.</p>
        </div>

        <div class="glass p-8 rounded-none shadow-sm bg-white border border-slate-200">
            <form action="{{ route('classrooms.update', $classroom->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="building" class="block text-sm font-semibold text-slate-700">Bina Adı</label>
                    <input id="building" name="building" type="text" required value="{{ old('building', $classroom->building) }}"
                        class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm @error('building') border-rose-500/50 @enderror"
                        placeholder="Mühendislik A Blok">
                    @error('building') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700">Derslik Adı / Kodu</label>
                    <input id="name" name="name" type="text" required value="{{ old('name', $classroom->name) }}"
                        class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm @error('name') border-rose-500/50 @enderror"
                        placeholder="D-101">
                    @error('name') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="capacity" class="block text-sm font-semibold text-slate-700">Kapasite</label>
                    <input id="capacity" name="capacity" type="number" required min="1" value="{{ old('capacity', $classroom->capacity) }}"
                        class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm @error('capacity') border-rose-500/50 @enderror"
                        placeholder="50">
                    @error('capacity') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                @if(auth()->user()->isDean() || auth()->user()->isAdmin())
                    <div>
                        <label for="department_id" class="block text-sm font-semibold text-slate-700">Bölüm İlişkisi (İsteğe Bağlı)</label>
                        <select id="department_id" name="department_id"
                            class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-850 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm cursor-pointer">
                            <option value="">Fakülte Ortak Dersliği (Bölüme Bağlı Değil)</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $classroom->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="pt-2 flex justify-end space-x-3">
                    <a href="{{ route('classrooms.index') }}" class="px-5 py-2.5 rounded-none bg-slate-100 hover:bg-slate-200 border border-slate-200 text-sm font-semibold text-slate-700 transition-all">
                        İptal
                    </a>
                    <button type="submit" class="px-5 py-2.5 rounded-none bg-indigo-600 hover:bg-indigo-500 text-sm font-bold text-white shadow-sm transition-colors cursor-pointer">
                        Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
