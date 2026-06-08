@extends('layouts.app', ['title' => 'Onay Bekleniyor - Sınav Dağıtım'])

@section('content')
<div class="min-h-[70vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10 text-center">
        <div class="mx-auto h-16 w-16 rounded-none bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 mb-6">
            <svg class="h-8 w-8 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">
            Sistem Onayınız Bekleniyor...
        </h2>

        <div class="mt-6 glass py-8 px-6 rounded-none shadow-sm bg-white border border-slate-200 text-left space-y-4">
            <p class="text-slate-650 text-sm leading-relaxed">
                Kaydınız başarıyla oluşturulmuştur. Ancak sisteme giriş yapabilmeniz ve panelinizi görüntüleyebilmeniz için bağlı bulunduğunuz <strong>Bölüm Başkanı</strong> veya <strong>Fakülte Dekanı</strong> tarafından onaylanmanız gerekmektedir.
            </p>

            <div class="border-t border-slate-200 pt-4 flex flex-col space-y-2">
                <div class="flex items-center space-x-2 text-xs text-slate-500">
                    <svg class="h-4 w-4 text-indigo-650 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Rolünüz otomatik olarak <strong>Eğitmen (egitmen)</strong> olarak atanmıştır.</span>
                </div>
                <div class="flex items-center space-x-2 text-xs text-slate-500">
                    <svg class="h-4 w-4 text-indigo-650 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Onay işlemi sonrasında sisteme giriş yapabilirsiniz.</span>
                </div>
            </div>

            <div class="pt-4 flex justify-center">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-2.5 rounded-none bg-slate-100 border border-slate-200 hover:bg-slate-200 text-sm font-semibold text-slate-700 transition-colors cursor-pointer">
                    Giriş Sayfasına Dön
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
