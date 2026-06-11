@extends('layouts.app', ['title' => 'Kapalı Sınav Dağıtım & Derslik Tahsis Platformu'])

@section('content')
<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">

    <div class="text-center space-y-4 mb-12">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Sınav & Derslik Dağıtım Portalı
        </h1>
        <p class="text-base text-slate-500 max-w-xl mx-auto leading-relaxed">
            Sınav programını yönetin, derslikleri tahsis edin ve dağıtım işlemlerini takip edin. Aşağıdaki hızlı işlem adımlarını kullanabilirsiniz.
        </p>
    </div>

    @guest

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mt-8">

            <div class="glass p-8 rounded-none flex flex-col justify-between space-y-6 bg-white">
                <div class="space-y-3">
                    <div class="h-10 w-10 rounded-none bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Sisteme Giriş Yapın</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Kişisel sınav görev listenizi görüntülemek, derslik planlaması yapmak veya otomatik dağıtımı çalıştırmak için giriş yapın.
                    </p>
                </div>
                <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center px-5 py-3 rounded-none bg-indigo-600 hover:bg-indigo-500 text-sm font-bold text-white shadow-sm transition-colors">
                    Giriş Yap &rarr;
                </a>
            </div>

            
            <div class="glass p-8 rounded-none flex flex-col justify-between space-y-6 bg-white">
                <div class="space-y-3">
                    <div class="h-10 w-10 rounded-none bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Eğitmen Kaydı Oluşturun</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Sisteme yeni bir eğitmen/gözetmen hesabı tanımlamak ve yetkilendirme sürecini başlatmak için kayıt olun.
                    </p>
                </div>
                <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center px-5 py-3 rounded-none bg-white hover:bg-slate-100 text-sm font-bold text-slate-700 border border-slate-200 transition-colors">
                    Kayıt Ol &rarr;
                </a>
            </div>
        </div>
    @else

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <div class="glass p-6 rounded-none flex flex-col justify-between space-y-4 bg-white">
                <div class="space-y-2">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center">
                        <span class="h-2 w-2 rounded-full bg-indigo-500 mr-2"></span>
                        Yönetim Paneli
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Kişisel sınav gözetmenlik görevlerinizi, tarih ve derslik tahsis bilgilerini listeleyin.
                    </p>
                </div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-none bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white transition-colors">
                    Yönetim Paneline Git &rarr;
                </a>
            </div>

            
            @if(auth()->user()->isAdmin() || auth()->user()->isDean() || auth()->user()->isChair())
                <div class="glass p-6 rounded-none flex flex-col justify-between space-y-4 bg-white">
                    <div class="space-y-2">
                        <h3 class="text-lg font-bold text-slate-900 flex items-center">
                            <span class="h-2 w-2 rounded-full bg-purple-500 mr-2"></span>
                            Derslik Yönetimi
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Fakülte/Bölüm bünyesindeki derslikleri listeleyin, yeni derslik ekleyin veya kapasiteleri güncelleyin.
                        </p>
                    </div>
                    <a href="{{ route('classrooms.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-none bg-white hover:bg-slate-100 text-xs font-bold text-slate-700 border border-slate-200 transition-colors">
                        Derslikleri Listele &rarr;
                    </a>
                </div>
            @endif

            
            @if(auth()->user()->isAdmin() || auth()->user()->isDean() || auth()->user()->isChair())
                <div class="glass p-6 rounded-none flex flex-col justify-between space-y-4 bg-white">
                    <div class="space-y-2">
                        <h3 class="text-lg font-bold text-slate-900 flex items-center">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 mr-2"></span>
                            Sınav Yönetimi
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Yeni sınav tanımları oluşturun, gözetmen atayın, öğrenci sayılarını ve sınav sürelerini yönetin.
                        </p>
                    </div>
                    <a href="{{ route('exams.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-none bg-white hover:bg-slate-100 text-xs font-bold text-slate-700 border border-slate-200 transition-colors">
                        Sınavları Listele &rarr;
                    </a>
                </div>
            @endif

            
            @if(auth()->user()->isAdmin() || auth()->user()->isDean() || auth()->user()->isChair())
                <div class="glass p-6 rounded-none flex flex-col justify-between space-y-4 bg-white">
                    <div class="space-y-2">
                        <h3 class="text-lg font-bold text-slate-900 flex items-center">
                            <span class="h-2 w-2 rounded-full bg-amber-500 mr-2"></span>
                            Eğitmen Onay İşlemleri
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Sisteme kaydolan yeni eğiticilerin hesap yetkilendirme ve üyelik onay isteklerini inceleyin.
                        </p>
                    </div>
                    <a href="{{ route('approvals.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-none bg-white hover:bg-slate-100 text-xs font-bold text-slate-700 border border-slate-200 transition-colors">
                        Kayıt İsteklerini Gör &rarr;
                    </a>
                </div>
            @endif

            
            @if(auth()->user()->isAdmin() || auth()->user()->isDean())
                <div class="glass p-6 rounded-none flex flex-col justify-between space-y-4 bg-white">
                    <div class="space-y-2">
                        <h3 class="text-lg font-bold text-slate-900 flex items-center">
                            <span class="h-2 w-2 rounded-full bg-indigo-400 mr-2"></span>
                            Otomatik Dağıtım
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Greedy algoritmasını çalıştırarak tüm sınavları zaman ve kapasite kısıtlarına göre dersliklere tahsis edin.
                        </p>
                    </div>
                    <form action="{{ route('allocation.run') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-none bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white shadow-sm transition-colors cursor-pointer">
                            Dağıtımı Çalıştır &rarr;
                        </button>
                    </form>
                </div>
            @endif

            
            @if(auth()->user()->isAdmin())
                <div class="glass p-6 rounded-none flex flex-col justify-between space-y-4 bg-white">
                    <div class="space-y-2">
                        <h3 class="text-lg font-bold text-slate-900 flex items-center">
                            <span class="h-2 w-2 rounded-full bg-rose-500 mr-2"></span>
                            Kullanıcı Yönetimi
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Sistemdeki kayıtlı kullanıcıların rollerini (Admin, Dekan, Bölüm Başkanı, Öğretmen) ve departman atamalarını yönetin.
                        </p>
                    </div>
                    <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-none bg-white hover:bg-slate-100 text-xs font-bold text-slate-700 border border-slate-200 transition-colors">
                        Kullanıcıları Yönet &rarr;
                    </a>
                </div>
            @endif
        </div>
    @endguest
</div>
@endsection
