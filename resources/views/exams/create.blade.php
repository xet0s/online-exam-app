@extends('layouts.app', ['title' => 'Sınav Ekle - Sınav Dağıtım'])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="relative z-10">
        <div class="mb-6">
            <a href="{{ route('exams.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors flex items-center">
                &larr; Sınav Listesine Dön
            </a>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 mt-2">Yeni Sınav Ekle</h1>
            <p class="text-sm text-slate-500 mt-1">Sınav programına eklemek üzere ders ve gözetmen bilgilerini girin.</p>
            <div class="glass p-8 rounded-none shadow-sm bg-white border border-slate-200">
            <form action="{{ route('exams.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700">Sınav / Ders Adı</label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}"
                        class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm @error('name') border-rose-500/50 @enderror"
                        placeholder="Nesne Yönelimli Programlama Vize">
                    @error('name') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="student_count" class="block text-sm font-semibold text-slate-700">Öğrenci Sayısı</label>
                        <input id="student_count" name="student_count" type="number" required min="1" value="{{ old('student_count') }}"
                            class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm @error('student_count') border-rose-500/50 @enderror"
                            placeholder="45">
                        @error('student_count') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="instructor_id" class="block text-sm font-semibold text-slate-700">Görevli Eğitmen (Gözetmen)</label>
                        @if(auth()->user()->isInstructor())
                            <select id="instructor_id_disabled" name="instructor_id_disabled" required disabled
                                class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-slate-50 text-slate-500 cursor-not-allowed text-sm">
                                <option value="{{ auth()->user()->id }}" selected>{{ auth()->user()->name }}</option>
                            </select>
                            <input type="hidden" name="instructor_id" value="{{ auth()->user()->id }}">
                        @else
                            <select id="instructor_id" name="instructor_id" required
                                class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-850 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm cursor-pointer @error('instructor_id') border-rose-500/50 @enderror">
                                <option value="">Eğitmen Seçiniz</option>
                                @foreach($instructors as $inst)
                                    <option value="{{ $inst->id }}" {{ old('instructor_id') == $inst->id ? 'selected' : '' }}>
                                        {{ $inst->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('instructor_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="date" class="block text-sm font-semibold text-slate-700">Sınav Tarihi</label>
                        <input id="date" name="date" type="date" required value="{{ old('date') }}"
                            class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm @error('date') border-rose-500/50 @enderror">
                        @error('date') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="start_hour" class="block text-sm font-semibold text-slate-700">Başlangıç Saati</label>
                        <input id="start_hour" name="start_hour" type="time" required value="{{ old('start_hour') }}"
                            class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm @error('start_hour') border-rose-500/50 @enderror">
                        @error('start_hour') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="end_hour" class="block text-sm font-semibold text-slate-700">Bitiş Saati</label>
                        <input id="end_hour" name="end_hour" type="time" required value="{{ old('end_hour') }}"
                            class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm @error('end_hour') border-rose-500/50 @enderror">
                        @error('end_hour') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                @error('start_time')
                    <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
                @error('end_time')
                    <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror

                @if(auth()->user()->isAdmin() || auth()->user()->isDean())
                    <div>
                        <label for="department_id" class="block text-sm font-semibold text-slate-700">Bölüm İlişkisi</label>
                        <select id="department_id" name="department_id" required
                            class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm cursor-pointer @error('department_id') border-rose-500/50 @enderror">
                            <option value="">Bölüm Seçiniz</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Derslik Seçimi (Birden fazla seçilebilir)</label>
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-3" id="classroom-selector-container">
                        @foreach($classrooms as $room)
                            <label class="classroom-option-card relative flex items-center justify-between p-3.5 rounded-none border border-slate-200 bg-white cursor-pointer select-none hover:border-indigo-500/50 transition-all">
                                <div class="flex items-center space-x-3">
                                    <input type="checkbox" name="classroom_ids[]" value="{{ $room->id }}" class="classroom-checkbox h-4 w-4 rounded-none border-slate-300 bg-white text-indigo-600 focus:ring-indigo-500/50 focus:ring-offset-0 focus:outline-none"
                                        {{ (is_array(old('classroom_ids')) && in_array($room->id, old('classroom_ids'))) ? 'checked' : '' }}>
                                    <div>
                                        <span class="block text-sm font-semibold text-slate-800 classroom-name">{{ $room->name }}</span>
                                        <span class="block text-xs text-slate-500">Kapasite: <span class="classroom-capacity">{{ $room->capacity }}</span></span>
                                    </div>
                                </div>
                                <span class="classroom-status-badge hidden text-xs font-semibold px-2 py-0.5 rounded-none bg-rose-50 border border-rose-200 text-rose-600">
                                    Dolu
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('classroom_ids') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    <p class="text-[11px] text-slate-500 mt-1.5">Boş bırakırsanız otomatik dağıtım motoru uygun derslikleri atayacaktır.</p>
                </div>

                <div class="pt-2 flex justify-end space-x-3">
                    <a href="{{ route('exams.index') }}" class="px-5 py-2.5 rounded-none bg-slate-100 hover:bg-slate-200 border border-slate-200 text-sm font-semibold text-slate-700 transition-all">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('date');
        const startHourInput = document.getElementById('start_hour');
        const endHourInput = document.getElementById('end_hour');
        const checkboxes = document.querySelectorAll('.classroom-checkbox');

        function checkClassroomAvailability() {
            const date = dateInput.value;
            const startHour = startHourInput.value;
            const endHour = endHourInput.value;

            if (!date || !startHour || !endHour) {
                checkboxes.forEach(cb => {
                    cb.disabled = false;
                    const card = cb.closest('.classroom-option-card');
                    card.classList.remove('opacity-50', 'pointer-events-none', 'bg-slate-50', 'border-rose-200/50');
                    card.querySelector('.classroom-status-badge').classList.add('hidden');
                });
                return;
            }

            let url = `/exams/check-availability?date=${date}&start_hour=${startHour}&end_hour=${endHour}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const occupiedIds = data.occupied_classroom_ids || [];
                    checkboxes.forEach(cb => {
                        const classroomId = parseInt(cb.value);
                        const card = cb.closest('.classroom-option-card');
                        const badge = card.querySelector('.classroom-status-badge');

                        if (occupiedIds.includes(classroomId)) {
                            cb.disabled = true;
                            cb.checked = false;
                            card.classList.add('opacity-50', 'pointer-events-none', 'bg-slate-50', 'border-rose-200/50');
                            badge.classList.remove('hidden');
                        } else {
                            cb.disabled = false;
                            card.classList.remove('opacity-50', 'pointer-events-none', 'bg-slate-50', 'border-rose-200/50');
                            badge.classList.add('hidden');
                        }
                    });
                })
                .catch(err => {
                    console.error('Availability check failed:', err);
                });
        }

        [dateInput, startHourInput, endHourInput].forEach(input => {
            if (input) {
                input.addEventListener('change', checkClassroomAvailability);
            }
        });

        checkClassroomAvailability();
    });
</script>
@endsection
