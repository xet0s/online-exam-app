@extends('layouts.app', ['title' => 'Sınav Düzenle - Sınav Dağıtım'])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="relative z-10">
        <div class="mb-6">
            <a href="{{ route('exams.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors flex items-center">
                &larr; Sınav Listesine Dön
            </a>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 mt-2">Sınav Düzenle</h1>
            <p class="text-sm text-slate-500 mt-1">Sınav bilgilerini güncelleyin.</p>
        </div>

        <div class="glass p-8 rounded-none shadow-sm bg-white border border-slate-200">
            <form action="{{ route('exams.update', $exam->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700">Sınav / Ders Adı</label>
                    <input id="name" name="name" type="text" required value="{{ old('name', $exam->name) }}"
                        class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm @error('name') border-rose-500/50 @enderror"
                        placeholder="Nesne Yönelimli Programlama Vize">
                    @error('name') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="student_count" class="block text-sm font-semibold text-slate-700">Öğrenci Sayısı</label>
                        <input id="student_count" name="student_count" type="number" required min="1" value="{{ old('student_count', $exam->student_count) }}"
                            class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm @error('student_count') border-rose-500/50 @enderror"
                            placeholder="45">
                        @error('student_count') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="instructor_id" class="block text-sm font-semibold text-slate-700">Dersi Veren Hoca</label>
                        @if(auth()->user()->isInstructor())
                            {{-- Eğitmen sadece kendi adını görür, instructor_id otomatik atanır --}}
                            <input type="text" disabled
                                value="{{ auth()->user()->name }}"
                                class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-slate-50 text-slate-500 cursor-not-allowed text-sm">
                            <input type="hidden" name="instructor_id" value="{{ auth()->user()->id }}">
                        @else
                            <select id="instructor_id" name="instructor_id" required
                                class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-850 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm cursor-pointer @error('instructor_id') border-rose-500/50 @enderror">
                                <option value="">Eğitmen Seçiniz</option>
                                @foreach($instructors as $inst)
                                    <option value="{{ $inst->id }}" {{ old('instructor_id', $exam->instructor_id) == $inst->id ? 'selected' : '' }}>
                                        {{ $inst->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('instructor_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        {{-- Hoca çakışma uyarısı (JS tarafından doldurulur) --}}
                        <p id="instructor-conflict-warning" class="mt-1 text-xs text-rose-600 font-medium hidden">
                            ⚠️ Bu eğitmenin seçilen saatte başka bir sınavı bulunmaktadır!
                        </p>
                    </div>
                </div>

                {{-- Gözetmen bilgi notu --}}
                <div class="flex items-start gap-2.5 p-3.5 rounded-none bg-indigo-50 border border-indigo-200">
                    <svg class="h-4 w-4 text-indigo-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-xs text-indigo-700">
                        <span class="font-semibold">Gözetmen Otomatik Atanacak:</span>
                        Sınav gözetmeni, otomatik dağıtım sistemi çalıştırıldığında bölüm hocaları arasından çakışma kontrolü yapılarak atanır.
                        @if($exam->supervisor)
                            <br><span class="mt-1 inline-block font-semibold text-indigo-900">Mevcut Gözetmen: {{ $exam->supervisor->name }}</span>
                        @endif
                    </div>
                </div>

                {{-- ── Otomatik Tarih Ata Paneli ─────────────────────────────── --}}
                <div id="auto-date-panel"
                    data-department-id="{{ $exam->department_id }}"
                    class="rounded-none border border-violet-200 bg-violet-50 p-4 space-y-3">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-violet-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <p class="text-xs font-semibold text-violet-800">Otomatik Tarih Ata</p>
                                <p id="period-label" class="text-[11px] text-violet-600 mt-0.5">Sınav haftası yükleniyor...</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div>
                                <label class="text-[11px] font-semibold text-violet-700 block mb-0.5">Süre (saat)</label>
                                <select id="duration-select" class="appearance-none px-2.5 py-1.5 border border-violet-300 rounded-none bg-white text-slate-700 text-xs focus:outline-none focus:border-violet-500 cursor-pointer">
                                    <option value="60">1 Saat</option>
                                    <option value="90">1.5 Saat</option>
                                    <option value="120" selected>2 Saat</option>
                                    <option value="150">2.5 Saat</option>
                                    <option value="180">3 Saat</option>
                                </select>
                            </div>
                            <button type="button" id="suggest-btn"
                                class="px-3.5 py-1.5 rounded-none bg-violet-600 hover:bg-violet-500 text-white text-xs font-bold shadow-sm transition-colors cursor-pointer inline-flex items-center gap-1.5 mt-4">
                                <svg id="suggest-icon" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <svg id="suggest-spinner" class="animate-spin h-3.5 w-3.5 hidden" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Otomatik Ata
                            </button>
                        </div>
                    </div>
                    <div id="suggest-result" class="hidden text-xs font-medium text-violet-800 bg-violet-100 border border-violet-200 px-3 py-2 rounded-none"></div>
                    <div id="suggest-error"  class="hidden text-xs font-medium text-rose-700 bg-rose-50 border border-rose-200 px-3 py-2 rounded-none"></div>
                </div>

                {{-- ── Tarih / Saat Alanları ─────────────────────────────────── --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="date" class="block text-sm font-semibold text-slate-700">Sınav Tarihi</label>
                        <input id="date" name="date" type="date" required value="{{ old('date', $exam->start_time ? $exam->start_time->format('Y-m-d') : '') }}"
                            class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm @error('date') border-rose-500/50 @enderror">
                        @error('date') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="start_hour" class="block text-sm font-semibold text-slate-700">Başlangıç Saati</label>
                        <input id="start_hour" name="start_hour" type="time" required value="{{ old('start_hour', $exam->start_time ? $exam->start_time->format('H:i') : '') }}"
                            class="appearance-none block w-full mt-1.5 px-3.5 py-2.5 border border-slate-300 rounded-none bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm @error('start_hour') border-rose-500/50 @enderror">
                        @error('start_hour') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="end_hour" class="block text-sm font-semibold text-slate-700">Bitiş Saati</label>
                        <input id="end_hour" name="end_hour" type="time" required value="{{ old('end_hour', $exam->end_time ? $exam->end_time->format('H:i') : '') }}"
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
                                <option value="{{ $dept->id }}" {{ old('department_id', $exam->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                @endif

                <input type="hidden" id="exam_id" value="{{ $exam->id }}">

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Derslik Seçimi (Birden fazla seçilebilir)</label>
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-3" id="classroom-selector-container">
                        @foreach($classrooms as $room)
                            <label class="classroom-option-card relative flex items-center justify-between p-3.5 rounded-none border border-slate-200 bg-white cursor-pointer select-none hover:border-indigo-500/50 transition-all">
                                <div class="flex items-center space-x-3">
                                    <input type="checkbox" name="classroom_ids[]" value="{{ $room->id }}" class="classroom-checkbox h-4 w-4 rounded-none border-slate-300 bg-white text-indigo-600 focus:ring-indigo-500/50 focus:ring-offset-0 focus:outline-none"
                                        {{ (is_array(old('classroom_ids')) && in_array($room->id, old('classroom_ids'))) || (!old('classroom_ids') && $exam->classrooms->contains($room->id)) ? 'checked' : '' }}>
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
                        Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput        = document.getElementById('date');
        const startHourInput   = document.getElementById('start_hour');
        const endHourInput     = document.getElementById('end_hour');
        const instructorSelect = document.getElementById('instructor_id');
        const checkboxes       = document.querySelectorAll('.classroom-checkbox');
        const examIdInput      = document.getElementById('exam_id');
        const conflictWarning  = document.getElementById('instructor-conflict-warning');

        // ── Otomatik Tarih Ata ──────────────────────────────────────────
        const suggestBtn     = document.getElementById('suggest-btn');
        const suggestIcon    = document.getElementById('suggest-icon');
        const suggestSpinner = document.getElementById('suggest-spinner');
        const suggestResult  = document.getElementById('suggest-result');
        const suggestError   = document.getElementById('suggest-error');
        const periodLabel    = document.getElementById('period-label');
        const durationSelect = document.getElementById('duration-select');

        function getDepartmentId() {
            const deptSelect = document.getElementById('department_id');
            if (deptSelect && deptSelect.value) return deptSelect.value;
            return document.getElementById('auto-date-panel')?.dataset.departmentId || '';
        }

        function loadPeriodLabel() {
            const deptId = getDepartmentId();
            if (!deptId) {
                periodLabel.textContent = 'Kapsam seçildikten sonra sınav haftası gösterilecek.';
                return;
            }
            fetch(`/exam-periods/for-department?department_id=${deptId}`)
                .then(r => r.json())
                .then(data => {
                    if (data.period) {
                        periodLabel.textContent = `Tanımlı Dönem: ${data.period.label}`;
                    } else {
                        periodLabel.textContent = 'Bu bölüm için sınav haftası tanımlanmamış.';
                    }
                })
                .catch(() => { periodLabel.textContent = 'Dönem bilgisi alınamadı.'; });
        }

        loadPeriodLabel();

        const deptSelect = document.getElementById('department_id');
        if (deptSelect) deptSelect.addEventListener('change', loadPeriodLabel);

        if (suggestBtn) {
            suggestBtn.addEventListener('click', function () {
                const deptId      = getDepartmentId();
                const instructorId = getInstructorId();
                const duration    = durationSelect ? durationSelect.value : '120';
                const examId      = examIdInput ? examIdInput.value : '';

                if (!deptId) {
                    suggestError.textContent = 'Lütfen önce bölümü seçin.';
                    suggestError.classList.remove('hidden');
                    suggestResult.classList.add('hidden');
                    return;
                }

                suggestIcon.classList.add('hidden');
                suggestSpinner.classList.remove('hidden');
                suggestBtn.disabled = true;
                suggestError.classList.add('hidden');
                suggestResult.classList.add('hidden');

                let url = `/exams/suggest-datetime?department_id=${deptId}&duration=${duration}`;
                if (instructorId) url += `&instructor_id=${instructorId}`;
                if (examId)       url += `&exam_id=${examId}`;

                fetch(url)
                    .then(r => r.json())
                    .then(data => {
                        if (data.error) {
                            suggestError.textContent = data.error;
                            suggestError.classList.remove('hidden');
                        } else {
                            if (dateInput)      dateInput.value      = data.date;
                            if (startHourInput) startHourInput.value = data.start_hour;
                            if (endHourInput)   endHourInput.value   = data.end_hour;

                            let msg = `✓ Atandı: ${data.date_label}, ${data.start_hour} – ${data.end_hour}`;
                            if (data.dept_conflict_count > 0) {
                                msg += ` (Bu saatte bölümde ${data.dept_conflict_count} başka sınav daha var)`;
                            }
                            suggestResult.textContent = msg;
                            suggestResult.classList.remove('hidden');
                            checkClassroomAvailability();
                        }
                    })
                    .catch(() => {
                        suggestError.textContent = 'Öneri alınırken bir hata oluştu.';
                        suggestError.classList.remove('hidden');
                    })
                    .finally(() => {
                        suggestIcon.classList.remove('hidden');
                        suggestSpinner.classList.add('hidden');
                        suggestBtn.disabled = false;
                    });
            });
        }

        // ── Derslik Müsaitliği ve Hoca Çakışması ───────────────────────
        function getInstructorId() {
            if (instructorSelect) return instructorSelect.value;
            const hidden = document.querySelector('input[name="instructor_id"]');
            return hidden ? hidden.value : '';
        }

        function checkClassroomAvailability() {
            const date        = dateInput ? dateInput.value : '';
            const startHour   = startHourInput ? startHourInput.value : '';
            const endHour     = endHourInput ? endHourInput.value : '';
            const instructorId = getInstructorId();

            if (!date || !startHour || !endHour) {
                checkboxes.forEach(cb => {
                    cb.disabled = false;
                    const card = cb.closest('.classroom-option-card');
                    card.classList.remove('opacity-50', 'pointer-events-none', 'bg-slate-55', 'border-rose-200/50');
                    card.querySelector('.classroom-status-badge').classList.add('hidden');
                });
                if (conflictWarning) conflictWarning.classList.add('hidden');
                return;
            }

            const examId = examIdInput ? examIdInput.value : '';
            let url = `/exams/check-availability?date=${date}&start_hour=${startHour}&end_hour=${endHour}`;
            if (examId)       url += `&exam_id=${examId}`;
            if (instructorId) url += `&instructor_id=${instructorId}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const occupiedIds = data.occupied_classroom_ids || [];
                    checkboxes.forEach(cb => {
                        const classroomId = parseInt(cb.value);
                        const card        = cb.closest('.classroom-option-card');
                        const badge       = card.querySelector('.classroom-status-badge');

                        if (occupiedIds.includes(classroomId)) {
                            cb.disabled = true;
                            cb.checked  = false;
                            card.classList.add('opacity-50', 'pointer-events-none', 'bg-slate-50', 'border-rose-200/50');
                            badge.classList.remove('hidden');
                        } else {
                            cb.disabled = false;
                            card.classList.remove('opacity-50', 'pointer-events-none', 'bg-slate-50', 'border-rose-200/50');
                            badge.classList.add('hidden');
                        }
                    });

                    if (conflictWarning) {
                        data.instructor_conflict
                            ? conflictWarning.classList.remove('hidden')
                            : conflictWarning.classList.add('hidden');
                    }
                })
                .catch(err => console.error('Availability check failed:', err));
        }

        [dateInput, startHourInput, endHourInput].forEach(input => {
            if (input) input.addEventListener('change', checkClassroomAvailability);
        });
        if (instructorSelect) instructorSelect.addEventListener('change', checkClassroomAvailability);

        checkClassroomAvailability();
    });
</script>
@endsection
