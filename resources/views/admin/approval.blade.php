@extends('layouts.app', ['title' => 'Eğitmen Onay Yönetimi - Sınav Dağıtım'])

@section('content')
<div class="space-y-8">

    <div>
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Kullanıcı & Onay Yönetimi</h1>
        <p class="text-sm text-slate-500 mt-1">Sisteme kayıtlı eğitmenlerin onay durumlarını kontrol edebilir ve yeni yetkili tanımlayabilirsiniz.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

        <div class="lg:col-span-2 space-y-8">

            
            <div class="glass rounded-none shadow-sm overflow-hidden bg-white border border-slate-200">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-900">Onay Bekleyen Kayıtlar</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Sisteme yeni kaydolmuş ve onay bekleyen eğitmen listesi.</p>
                </div>

                @if($pendingUsers->isEmpty())
                    <div class="text-center py-12 px-4 text-slate-500 text-sm font-medium">
                        Onay bekleyen herhangi bir eğitmen kaydı bulunmuyor.
                    </div>
                @else
                    <div class="divide-y divide-slate-200">
                        @foreach($pendingUsers as $pUser)
                            <div class="p-6 flex items-center justify-between hover:bg-slate-50 transition-colors">
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">{{ $pUser->name }}</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $pUser->email }}</p>
                                    @if($pUser->department)
                                        <div class="mt-2 text-xs text-slate-500 inline-flex items-center">
                                            <span class="w-1.5 h-1.5 rounded-none bg-indigo-500 mr-1.5"></span>
                                            Bölüm: {{ $pUser->department->name }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    @can('approve', $pUser)
                                        <form action="{{ route('approvals.approve', $pUser->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-none text-xs font-bold transition-colors cursor-pointer">
                                                Onayla
                                            </button>
                                        </form>
                                    @endcan
                                    @can('reject', $pUser)
                                        <form action="{{ route('approvals.reject', $pUser->id) }}" method="POST" onsubmit="return confirm('Bu kayıt talebini reddetmek ve silmek istediğinize emin misiniz?');">
                                            @csrf
                                            <button type="submit" class="px-3.5 py-1.5 bg-white border border-slate-200 hover:bg-rose-50 hover:border-rose-250 text-rose-600 rounded-none text-xs font-bold transition-all cursor-pointer">
                                                Reddet
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            
            <div class="glass rounded-none shadow-sm overflow-hidden bg-white border border-slate-200">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-900">Sistem Yetkilileri</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Sistemi aktif kullanan onaylı eğitmen, bölüm başkanı ve dekanların listesi.</p>
                </div>

                @if($approvedUsers->isEmpty())
                    <div class="text-center py-12 px-4 text-slate-500 text-sm font-medium">
                        Aktif diğer yetkili bulunmuyor.
                    </div>
                @else
                    <div class="divide-y divide-slate-200">
                        @foreach($approvedUsers as $aUser)
                            <div class="p-6 flex items-center justify-between hover:bg-slate-50 transition-colors">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <h4 class="font-bold text-slate-800 text-sm">{{ $aUser->name }}</h4>
                                        <span class="px-2 py-0.5 rounded-none text-[10px] font-bold capitalize border
                                            {{ $aUser->isDean() ? 'bg-purple-50 border-purple-200 text-purple-700' :
                                               ($aUser->isChair() ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'bg-slate-100 border-slate-200 text-slate-600') }}">
                                            {{ $aUser->role === 'dekan' ? 'Dekan' : ($aUser->role === 'bolum_baskani' ? 'Bölüm Bşk.' : 'Eğitmen') }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">{{ $aUser->email }}</p>
                                    @if($aUser->department)
                                        <div class="mt-1 text-xs text-slate-500 inline-flex items-center">
                                            <span class="w-1.5 h-1.5 rounded-none bg-slate-400 mr-1.5"></span>
                                            Bölüm: {{ $aUser->department->name }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    @can('reject', $aUser)
                                        <form action="{{ route('approvals.reject', $aUser->id) }}" method="POST" onsubmit="return confirm('Bu kullanıcının sistem yetkisini iptal edip silmek istediğinize emin misiniz?');">
                                            @csrf
                                            <button type="submit" class="p-1 px-2.5 rounded-none bg-white border border-slate-200 hover:bg-rose-50 hover:border-rose-250 text-xs font-bold text-rose-600 transition-all cursor-pointer">
                                                Yetkiyi Kaldır / Sil
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        
        <div>
            @if(auth()->user()->isDean() || auth()->user()->isAdmin())
                <div class="glass rounded-none shadow-sm overflow-hidden bg-white border border-slate-200 sticky top-24">
                    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
                        <h2 class="text-lg font-bold text-slate-900">Yeni Yetkili Tanımla</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Onay süreci olmadan doğrudan yetkili ekleyin.</p>
                    </div>

                    <form action="{{ route('approvals.pre-approved') }}" method="POST" class="p-6 space-y-4">
                        @csrf

                        <div>
                            <label for="name" class="block text-xs font-semibold text-slate-700">Ad Soyad</label>
                            <input id="name" name="name" type="text" required value="{{ old('name') }}"
                                class="appearance-none block w-full px-3 py-2 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-xs"
                                placeholder="Prof. Dr. Ahmet Yılmaz">
                            @error('name') <p class="mt-1 text-[10px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-semibold text-slate-700">E-posta</label>
                            <input id="email" name="email" type="email" required value="{{ old('email') }}"
                                class="appearance-none block w-full px-3 py-2 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-xs"
                                placeholder="ahmet@universite.edu.tr">
                            @error('email') <p class="mt-1 text-[10px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-semibold text-slate-700">Şifre</label>
                            <input id="password" name="password" type="password" required
                                class="appearance-none block w-full px-3 py-2 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-xs"
                                placeholder="••••••••">
                            @error('password') <p class="mt-1 text-[10px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="role" class="block text-xs font-semibold text-slate-700">Rol</label>
                            <select id="role" name="role" required
                                class="appearance-none block w-full px-3 py-2 border border-slate-300 rounded-none bg-white text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-xs cursor-pointer">
                                <option value="egitmen" {{ old('role') === 'egitmen' ? 'selected' : '' }}>Eğitmen</option>
                                <option value="bolum_baskani" {{ old('role') === 'bolum_baskani' ? 'selected' : '' }}>Bölüm Başkanı</option>
                                <option value="dekan" {{ old('role') === 'dekan' ? 'selected' : '' }}>Dekan</option>
                            </select>
                            @error('role') <p class="mt-1 text-[10px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="department_id" class="block text-xs font-semibold text-slate-700">Bölüm (Eğitmen & Bölüm Bşk. İçin)</label>
                            <select id="department_id" name="department_id"
                                class="appearance-none block w-full px-3 py-2 border border-slate-300 rounded-none bg-white text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-xs cursor-pointer">
                                <option value="">Seçiniz (Dekan için boş bırakılabilir)</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id') <p class="mt-1 text-[10px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-none shadow-sm text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition-colors cursor-pointer">
                                Doğrudan Ekle (Onaylı)
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="glass p-6 rounded-none shadow-sm border border-slate-200 bg-white text-slate-500 text-xs">
                    <p class="font-bold text-slate-850 mb-2">Onay Yetkisi Kapsamı</p>
                    <p class="leading-relaxed">
                        Bölüm başkanı olarak sadece kendi bölümünüze kayıt yaptıran eğitmenlerin üyelik taleplerini görüntüleyebilir ve onaylayabilirsiniz. Yeni yetkili ekleme işlemleri sadece Dekan tarafından gerçekleştirilebilir.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
