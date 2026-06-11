<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #3730a3;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
            color: #1e1b4b;
        }
        .header p {
            font-size: 10px;
            margin: 0;
            color: #64748b;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 10px;
        }
        .info-table td {
            padding: 4px 0;
        }
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .schedule-table th {
            background-color: #3730a3;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            border: 1px solid #312e81;
            font-size: 10px;
            text-transform: uppercase;
        }
        .schedule-table td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            vertical-align: middle;
        }
        .schedule-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Sınav ve Derslik Tahsis Otomasyon Raporu</p>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Talep Eden:</strong> {{ $user->name }} ({{ $user->role === 'dekan' ? 'Dekan' : ($user->role === 'bolum_baskani' ? 'Bölüm Başkanı' : 'Eğitmen') }})</td>
            <td style="text-align: right;"><strong>Rapor Tarihi:</strong> {{ $generatedAt }}</td>
        </tr>
        @if($user->department)
            <tr>
                <td><strong>Bölüm:</strong> {{ $user->department->name }}</td>
                <td></td>
            </tr>
        @endif
    </table>

    <table class="schedule-table">
        <thead>
            <tr>
                <th style="width: 28%;">Sınav Adı</th>
                <th style="width: 22%;">Bölüm</th>
                <th style="width: 20%;">Dersi Veren</th>
                <th style="text-align: center; width: 10%;">Öğrenci</th>
                <th style="width: 10%;">Tarih / Saat</th>
                <th style="width: 10%;">Derslik</th>
            </tr>
        </thead>
        <tbody>
            @if(count($exams) == 0)
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">
                        Programda herhangi bir sınav kaydı bulunmamaktadır.
                    </td>
                </tr>
            @else
                @foreach($exams as $exam)
                    <tr>
                        <td><strong>{{ $exam->name }}</strong></td>
                        <td>{{ $exam->department->name ?? '-' }}</td>
                        <td>{{ $exam->instructor->name ?? '-' }}</td>
                        <td style="text-align: center;">{{ $exam->student_count }}</td>
                        <td>
                            {{ $exam->start_time->format('d.m.Y') }}<br>
                            <span style="color: #64748b;">{{ $exam->start_time->format('H:i') }} - {{ $exam->end_time->format('H:i') }}</span>
                        </td>
                        <td>
                            @if($exam->classrooms->isNotEmpty())
                                @foreach($exam->classrooms as $room)
                                    <span class="badge badge-success" style="margin-bottom: 2px; display: block;">{{ $room->name }}</span>
                                @endforeach
                            @else
                                <span class="badge badge-warning">Atanmadı</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="footer">
        Bu belge sistem tarafından otomatik olarak üretilmiştir. &copy; {{ date('Y') }} Kapalı Sınav Dağıtım Platformu.
    </div>

</body>
</html>
