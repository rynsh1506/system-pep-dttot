<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Alert: Terindikasi</title>
</head>
<body style="background-color: #f8f9fa; padding: 20px;">
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <div style="background: #e74a3b; color: #fff; padding: 15px 20px; border-radius: 8px 8px 0 0;">
            <h2 style="margin: 0; font-size: 18px;">⚠️ Alert: Terindikasi {{ $alert_type }}</h2>
        </div>
        <div style="background: #fff; border: 1px solid #e0e0e0; border-top: none; padding: 20px; border-radius: 0 0 8px 8px;">
            <p style="margin: 0 0 15px; color: #333;">Hasil pengecekan dari <strong>{{ $source }}</strong> menunjukkan adanya indikasi. Berikut detailnya:</p>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <td style="padding: 8px 12px; border: 1px solid #eee; font-weight: 600; background: #f8f9fc; width: 40%;">Nama</td>
                    <td style="padding: 8px 12px; border: 1px solid #eee;">{{ $nama }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 12px; border: 1px solid #eee; font-weight: 600; background: #f8f9fc;">NIK / KTP</td>
                    <td style="padding: 8px 12px; border: 1px solid #eee;">{{ $nik }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 12px; border: 1px solid #eee; font-weight: 600; background: #f8f9fc;">No Kontrak</td>
                    <td style="padding: 8px 12px; border: 1px solid #eee;">{{ $kontrak }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 12px; border: 1px solid #eee; font-weight: 600; background: #f8f9fc;">Hasil DTTOT</td>
                    <td style="padding: 8px 12px; border: 1px solid #eee; color: {{ $hasil_dtot === 'Terindikasi' ? '#e74a3b; font-weight:700;' : '#1cc88a;' }}">{{ $hasil_dtot }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 12px; border: 1px solid #eee; font-weight: 600; background: #f8f9fc;">Hasil PEP</td>
                    <td style="padding: 8px 12px; border: 1px solid #eee; color: {{ $hasil_pep === 'Terindikasi' ? '#e74a3b; font-weight:700;' : '#1cc88a;' }}">{{ $hasil_pep }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 12px; border: 1px solid #eee; font-weight: 600; background: #f8f9fc;">Dicek oleh</td>
                    <td style="padding: 8px 12px; border: 1px solid #eee;">{{ $checked_by }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 12px; border: 1px solid #eee; font-weight: 600; background: #f8f9fc;">Waktu</td>
                    <td style="padding: 8px 12px; border: 1px solid #eee;">{{ $waktu }}</td>
                </tr>
            </table>
            <p style="margin: 20px 0 0; font-size: 12px; color: #858796;">Email ini dikirim otomatis oleh Sistem DTTOT & PEP.</p>
        </div>
    </div>
</body>
</html>
