<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Sertifikat Kedaluwarsa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        p {
            margin-bottom: 15px;
        }

        strong {
            color: #007BFF;
        }

        em {
            font-style: italic;
            color: #28A745;
        }
    </style>
</head>
<body>
    <p>Dear {{ $user->name }},</p>

    <p>Kami ingin memberitahukan kepada Anda bahwa beberapa sertifikat akan segera kedaluwarsa. Berikut adalah daftar dokumen:</p>

    <table>
        <thead>
            <tr>
                <th>Nama Sertifikat</th>
                <th>Nomor Sertifikat</th>
                <th>Tanggal Kadaluwarsa</th>
                <th>Type Sertifikat</th>
                <th>Pemilik Sertifikat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataEndDate as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->certificate_number }}</td>
                    <td style="color: red">{{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}</td>
                    <td>{{ $item->certificateType->name }}</td>
                    <td>{{ $item->employee_name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Mohon untuk segera mengambil tindakan yang diperlukan untuk memperbarui atau memperpanjang sertifikat tersebut agar tetap berlaku.</p>

    <p>Terima kasih atas perhatian dan kerjasamanya.</p>

    <p><em>Hormat kami,</em></p>
    <p>HCDOC<br>
</body>
</html>
