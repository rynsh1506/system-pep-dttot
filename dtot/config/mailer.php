<?php
/**
 * Helper function to send email notification when DTTOT or PEP is "Terindikasi"
 * Uses PHPMailer with existing SMTP settings from Reksa Finance mail server
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer from parent vendor directory
require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Send alert email when a check result is "Terindikasi"
 * 
 * @param string $nama       Name of the debtor/cadeb
 * @param string $nik        NIK/KTP number
 * @param string $hasil_dtot DTTOT check result
 * @param string $hasil_pep  PEP check result
 * @param string $source     Source of the check ('Pengajuan Cek' or 'Reksaloan')
 * @param string $kontrak    Contract number (optional, for Reksaloan)
 * @return string             Status message for logging
 */
function sendAlertEmail($nama, $nik, $hasil_dtot, $hasil_pep, $source = 'Pengajuan Cek', $kontrak = '-') {
    // Only send if at least one is "Terindikasi"
    if ($hasil_dtot !== 'Terindikasi' && $hasil_pep !== 'Terindikasi') {
        return '';
    }

    $mail = new PHPMailer(true);
    try {
        // Server settings (sama dengan yang di add.php)
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = gethostbyname('mail.reksafinance.com');
        $mail->Port = 465;
        $mail->Username = '10271927@reksafinance.com';
        $mail->Password = 'Rf2024!@@';
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->isHTML(true);
        $mail->SetFrom('10271927@reksafinance.com', 'Sistem DTTOT & PEP');
        
        // Daftar penerima email alert
        $recipients = [
            'adwin.bhaskoro@reksafinance.com',
            'robert.syahratoe@reksafinance.com',
            'ghessa.utomo@reksafinance.com',
            'triyana.rahmawati@reksafinance.com',
            'asti.miftahul@reksafinance.com',
            'julies.barli@reksafinance.com',
            'rizal.dzalkarnaen@reksafinance.com',
            'agatha.saputri@reksafinance.com',
            'credit.ho3@reksafinance.com',
            'ericho.primadadi@reksafinance.com',
            'galih.prasetyo@reksafinance.com',
            'yoseph.halomoan@reksafinance.com',
            'siti.annisa@reksafinance.com',
            'nur.azizah@reksafinance.com',
            'ida.santi@reksafinance.com',
            'bustaman@reksafinance.com',
            'hanifah.adiyati@reksafinance.com'
        ];

        foreach ($recipients as $email) {
            $mail->addAddress($email);
        }
        $mail->CharSet = 'UTF-8';

        // Build alert details
        $terindikasi = [];
        if ($hasil_dtot === 'Terindikasi') $terindikasi[] = 'DTTOT';
        if ($hasil_pep === 'Terindikasi') $terindikasi[] = 'PEP';
        $alert_type = implode(' & ', $terindikasi);

        $checked_by = $_SESSION['full_name'] ?? 'Unknown';
        $waktu = date('d/m/Y H:i:s');

        // Subject
        $mail->Subject = "⚠️ ALERT: Terindikasi $alert_type - $nama";

        // HTML Body
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #e74a3b; color: #fff; padding: 15px 20px; border-radius: 8px 8px 0 0;'>
                <h2 style='margin: 0; font-size: 18px;'>⚠️ Alert: Terindikasi $alert_type</h2>
            </div>
            <div style='background: #fff; border: 1px solid #e0e0e0; border-top: none; padding: 20px; border-radius: 0 0 8px 8px;'>
                <p style='margin: 0 0 15px; color: #333;'>Hasil pengecekan dari <strong>$source</strong> menunjukkan adanya indikasi. Berikut detailnya:</p>
                <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                    <tr>
                        <td style='padding: 8px 12px; border: 1px solid #eee; font-weight: 600; background: #f8f9fc; width: 40%;'>Nama</td>
                        <td style='padding: 8px 12px; border: 1px solid #eee;'>$nama</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 12px; border: 1px solid #eee; font-weight: 600; background: #f8f9fc;'>NIK / KTP</td>
                        <td style='padding: 8px 12px; border: 1px solid #eee;'>$nik</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 12px; border: 1px solid #eee; font-weight: 600; background: #f8f9fc;'>No Kontrak</td>
                        <td style='padding: 8px 12px; border: 1px solid #eee;'>$kontrak</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 12px; border: 1px solid #eee; font-weight: 600; background: #f8f9fc;'>Hasil DTTOT</td>
                        <td style='padding: 8px 12px; border: 1px solid #eee; color: " . ($hasil_dtot === 'Terindikasi' ? '#e74a3b; font-weight:700;' : '#1cc88a;') . "'>$hasil_dtot</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 12px; border: 1px solid #eee; font-weight: 600; background: #f8f9fc;'>Hasil PEP</td>
                        <td style='padding: 8px 12px; border: 1px solid #eee; color: " . ($hasil_pep === 'Terindikasi' ? '#e74a3b; font-weight:700;' : '#1cc88a;') . "'>$hasil_pep</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 12px; border: 1px solid #eee; font-weight: 600; background: #f8f9fc;'>Dicek oleh</td>
                        <td style='padding: 8px 12px; border: 1px solid #eee;'>$checked_by</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 12px; border: 1px solid #eee; font-weight: 600; background: #f8f9fc;'>Waktu</td>
                        <td style='padding: 8px 12px; border: 1px solid #eee;'>$waktu</td>
                    </tr>
                </table>
                <p style='margin: 20px 0 0; font-size: 12px; color: #858796;'>Email ini dikirim otomatis oleh Sistem DTTOT & PEP.</p>
            </div>
        </div>";

        // Plain text fallback
        $mail->AltBody = "ALERT: Terindikasi $alert_type\n\n" .
            "Sumber: $source\n" .
            "Nama: $nama\n" .
            "NIK: $nik\n" .
            "No Kontrak: $kontrak\n" .
            "Hasil DTTOT: $hasil_dtot\n" .
            "Hasil PEP: $hasil_pep\n" .
            "Dicek oleh: $checked_by\n" .
            "Waktu: $waktu\n\n" .
            "Sistem DTTOT & PEP.";

        $mail->send();
        return ' & email alert terkirim.';
    } catch (Exception $e) {
        error_log("Gagal kirim email alert: " . $mail->ErrorInfo);
        return ' namun GAGAL mengirim email alert: ' . $mail->ErrorInfo;
    }
}
