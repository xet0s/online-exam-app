@extends('layouts.app', ['title' => 'Giriş Yap - Sınav Dağıtım'])

@section('content')
<div class="min-h-[70vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
        <h2 class="text-center text-3xl font-extrabold tracking-tight text-slate-900">
            Sisteme Giriş Yapın
        </h2>
        <p class="mt-2 text-center text-sm text-slate-500">
            Sınav ve derslik planlamasına erişmek için kimliğinizi doğrulayın.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md relative z-10">
        <div class="glass py-8 px-4 sm:rounded-none sm:px-10 shadow-sm bg-white border border-slate-200">
            <form class="space-y-6" action="{{ route('login') }}" method="POST">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">
                        E-posta Adresi
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                            class="appearance-none block w-full px-3 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm @error('email') border-rose-500/50 @enderror"
                            placeholder="ornek@universite.edu.tr">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">
                        Şifre
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="appearance-none block w-full px-3 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm @error('password') border-rose-500/50 @enderror"
                            placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500/50 border-slate-300 bg-white rounded-none cursor-pointer">
                        <label for="remember" class="ml-2 block text-xs text-slate-600 select-none cursor-pointer">
                            Beni Hatırla
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-none shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition-colors cursor-pointer">
                        Giriş Yap
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
                            Hesabınız yok mu?
                        </span>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('register') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-550 transition-colors">
                        Yeni Eğitmen Kaydı Oluşturun &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
