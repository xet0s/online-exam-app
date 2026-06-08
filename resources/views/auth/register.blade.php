@extends('layouts.app', ['title' => 'Eğitmen Kaydı - Sınav Dağıtım'])

@section('content')
<div class="min-h-[70vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
        <h2 class="text-center text-3xl font-extrabold tracking-tight text-slate-900">
            Eğitmen Kayıt Paneli
        </h2>
        <p class="mt-2 text-center text-sm text-slate-500">
            Sisteme eğitmen rolüyle kayıt olmak için formu doldurun.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-lg relative z-10">
        <div class="glass py-8 px-4 sm:rounded-none sm:px-10 shadow-sm bg-white border border-slate-200">
            <form class="space-y-5" action="{{ route('register') }}" method="POST">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700">
                        Ad Soyad
                    </label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}"
                            class="appearance-none block w-full px-3 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm @error('name') border-rose-500/50 @enderror"
                            placeholder="Prof. Dr. Ahmet Yılmaz">
                    </div>
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">
                        E-posta Adresi
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                            class="appearance-none block w-full px-3 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm @error('email') border-rose-500/50 @enderror"
                            placeholder="ahmetyilmaz@universite.edu.tr">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-semibold text-slate-700">
                        Bağlı Bulunduğunuz Bölüm
                    </label>
                    <div class="mt-1">
                        <select id="department_id" name="department_id" required
                            class="appearance-none block w-full px-3 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm cursor-pointer @error('department_id') border-rose-500/50 @enderror">
                            <option value="">Bölüm Seçiniz</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('department_id')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700">
                            Şifre
                        </label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" required
                                class="appearance-none block w-full px-3 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm @error('password') border-rose-500/50 @enderror"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">
                            Şifre Tekrarı
                        </label>
                        <div class="mt-1">
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                class="appearance-none block w-full px-3 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm"
                                placeholder="••••••••">
                        </div>
                    </div>
                    @error('password')
                        <div class="sm:col-span-2">
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        </div>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-none shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition-colors cursor-pointer">
                        Kayıt Talebi Gönder
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-2 bg-white text-slate-500">
                            Zaten bir hesabınız var mı?
                        </span>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-550 transition-colors">
                        Giriş Yapın &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
