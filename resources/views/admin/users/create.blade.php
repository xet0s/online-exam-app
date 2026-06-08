@extends('layouts.app', ['title' => 'Kullanıcı Ekle - Sınav Dağıtım'])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="relative z-10">
        <div class="mb-6">
            <a href="{{ route('users.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors flex items-center">
                &larr; Kullanıcı Listesine Dön
            </a>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 mt-2">Yeni Kullanıcı Ekle</h1>
            <p class="text-sm text-slate-500 mt-1">Sistem yetkilisi veya eğitmen hesabı oluşturun.</p>
        </div>

        <div class="glass p-8 rounded-none shadow-sm bg-white border border-slate-200">
            <form action="{{ route('users.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700">Ad Soyad</label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}"
                        class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm @error('name') border-rose-500/50 @enderror"
                        placeholder="Prof. Dr. Hakan Yılmaz">
                    @error('name') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">E-posta Adresi</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}"
                        class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm @error('email') border-rose-500/50 @enderror"
                        placeholder="hakan@universite.edu.tr">
                    @error('email') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700">Şifre</label>
                        <input id="password" name="password" type="password" required
                            class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm @error('password') border-rose-500/50 @enderror"
                            placeholder="••••••••">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Şifre Tekrarı</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm"
                            placeholder="••••••••">
                    </div>
                    @error('password')
                        <div class="sm:col-span-2">
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        </div>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="role" class="block text-sm font-semibold text-slate-700">Rol</label>
                        <select id="role" name="role" required
                            class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm cursor-pointer @error('role') border-rose-500/50 @enderror">
                            <option value="egitmen" {{ old('role') == 'egitmen' ? 'selected' : '' }}>Eğitmen</option>
                            @if(auth()->user()->isAdmin() || auth()->user()->isDean())
                                <option value="bolum_baskani" {{ old('role') == 'bolum_baskani' ? 'selected' : '' }}>Bölüm Başkanı</option>
                            @endif
                            @if(auth()->user()->isAdmin())
                                <option value="dekan" {{ old('role') == 'dekan' ? 'selected' : '' }}>Dekan</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            @endif
                        </select>
                        @error('role') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-semibold text-slate-700">Durum</label>
                        @if(auth()->user()->isChair())
                            <select id="status_disabled" name="status_disabled" required disabled
                                class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-slate-50 text-slate-500 cursor-not-allowed text-sm">
                                <option value="pending" selected>Onay Bekliyor</option>
                            </select>
                            <input type="hidden" name="status" value="pending">
                        @else
                            <select id="status" name="status" required
                                class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm cursor-pointer @error('status') border-rose-500/50 @enderror">
                                <option value="approved" {{ old('status', 'approved') == 'approved' ? 'selected' : '' }}>Onaylı (Aktif)</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Onay Bekliyor</option>
                            </select>
                        @endif
                        @error('status') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-semibold text-slate-700">Bölüm (Eğitmen & Bölüm Başkanı İçin)</label>
                    @if(auth()->user()->isChair())
                        <select id="department_id_disabled" name="department_id_disabled" required disabled
                            class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-slate-50 text-slate-500 cursor-not-allowed text-sm">
                            <option value="{{ auth()->user()->department_id }}" selected>{{ auth()->user()->department->name }}</option>
                        </select>
                        <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}">
                    @else
                        <select id="department_id" name="department_id"
                            class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm cursor-pointer @error('department_id') border-rose-500/50 @enderror">
                            <option value="">Seçiniz (Admin ve Dekan için boş bırakılabilir)</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('department_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2 flex justify-end space-x-3">
                    <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-none bg-slate-100 hover:bg-slate-200 border border-slate-200 text-sm font-semibold text-slate-700 transition-all">
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
