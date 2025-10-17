<?php
// File ini hanya konten, tidak ada session_start(), <html>, <head>, atau <body>
if (!isset($_SESSION['username'])) {
    echo "Akses ditolak. Silakan login."; exit;
}

function isWeekday($date) {
    return date('N', strtotime($date)) <= 5;
}

$query = "SELECT * FROM arsip_paspor WHERE status != 'Serah' AND tanggal_input IS NOT NULL ORDER BY tanggal_input DESC";
$result = mysqli_query($conn, $query);
$pasporData = mysqli_fetch_all($result, MYSQLI_ASSOC);

$today = date('Y-m-d');
$reminders = [];

foreach ($pasporData as $data) {
    $tanggal_input = $data['tanggal_input'];
    $reminder_dates = [
        7 => date('Y-m-d', strtotime($tanggal_input . ' + 7 days')),
        14 => date('Y-m-d', strtotime($tanggal_input . ' + 14 days')),
        21 => date('Y-m-d', strtotime($tanggal_input . ' + 21 days')),
        28 => date('Y-m-d', strtotime($tanggal_input . ' + 28 days')),
    ];
    foreach ($reminder_dates as $hari => $tanggal) {
        if ($tanggal == $today && isWeekday($today)) {
            $check_query = "SELECT * FROM log_kirim_wa WHERE arsip_id = {$data['id']} AND reminder_ke = $hari AND DATE(waktu_kirim) = '$today'";
            $check_result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_result) == 0) {
                $dokim_query = "SELECT nama, no_hp FROM dokim_wni WHERE nomor_permohonan = '{$data['nomor_permohonan']}'";
                $dokim_result = mysqli_query($conn, $dokim_query);
                $dokim_data = mysqli_fetch_assoc($dokim_result);

                if ($dokim_data && !empty($dokim_data['no_hp'])) {
                    $reminders[] = [
                        'id' => $data['id'],
                        'nama' => $dokim_data['nama'],
                        'telepon' => $dokim_data['no_hp'],
                        'tanggal_input' => $data['tanggal_input'],
                        'reminder_ke' => $hari,
                        'pesan' => "Yth. Bapak/Ibu " . $dokim_data['nama'] . ", kami informasikan bahwa paspor Anda telah selesai dicetak. Mohon untuk segera diambil di Kantor Imigrasi."
                    ];
                }
            }
        }
    }
}
?>

<link rel="stylesheet" href="../app/css/bootstrap.min.css">
<link rel="stylesheet" href="../app/css/sweetalert2.min.css">
<style>
    .sending-spinner { display: none; width: 1rem; height: 1rem; border: 2px solid currentColor; border-right-color: transparent; border-radius: 50%; animation: spinner-border .75s linear infinite; }
</style>

<h3>Daftar Pengingat WhatsApp (Hari Ini)</h3>
<hr>
<?php if (!empty($reminders)) : ?>
    <button id="kirim-semua" style="border:none; padding: 8px 12px; background-color: #28a745; color: white; border-radius: 5px; cursor:pointer; margin-bottom: 15px;">Kirim Semua</button>
    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead bgcolor="yellow">
            <tr align="center">
                <th style="padding: 8px; width: 5%;">No</th>
                <th style="padding: 8px;">Nama</th>
                <th style="padding: 8px; width: 15%;">Telepon</th>
                <th style="padding: 8px; width: 15%;">Tgl. Paspor Jadi</th>
                <th style="padding: 8px; width: 15%;">Reminder Ke-</th>
                <th style="padding: 8px; width: 10%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reminders as $i => $reminder) : ?>
                <tr id="row-<?php echo $reminder['id']; ?>" align="center">
                    <td style="padding: 8px;"><?php echo $i + 1; ?></td>
                    <td style="padding: 8px; text-align: left;"><?php echo $reminder['nama']; ?></td>
                    <td style="padding: 8px;"><?php echo $reminder['telepon']; ?></td>
                    <td style="padding: 8px;"><?php echo date('d-m-Y', strtotime($reminder['tanggal_input'])); ?></td>
                    <td style="padding: 8px;">Hari ke-<?php echo $reminder['reminder_ke']; ?></td>
                    <td style="padding: 8px;">
                        <button class="kirim-pesan" style="background-color: #007bff; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor:pointer;"
                                data-id="<?php echo $reminder['id']; ?>"
                                data-nama="<?php echo htmlspecialchars($reminder['nama']); ?>"
                                data-telepon="<?php echo $reminder['telepon']; ?>"
                                data-pesan="<?php echo htmlspecialchars($reminder['pesan']); ?>"
                                data-reminder_ke="<?php echo $reminder['reminder_ke']; ?>">
                            <span class="button-text">Kirim</span>
                            <div class="sending-spinner"></div>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
    <div style="padding: 1rem; background-color: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;">Tidak ada jadwal pengiriman pesan untuk hari ini.</div>
<?php endif; ?>

<script src="../app/js/jquery-3.5.1.js"></script>
<script src="../app/js/sweetalert2.all.min.js"></script>
<script>
$(document).ready(function() {
    function kirimPesan(button) {
        var btn = $(button);
        var id = btn.data('id'), nama = btn.data('nama'), telepon = btn.data('telepon'), pesan = btn.data('pesan'), reminder_ke = btn.data('reminder_ke');
        btn.prop('disabled', true).find('.button-text').hide().siblings('.sending-spinner').show();
        $.ajax({
            url: 'send_wa_process.php', type: 'POST', data: { id, nama, telepon, pesan, reminder_ke }, dataType: 'json',
            success: function(res) {
                if (res.status == 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message }).then(() => $('#row-' + id).fadeOut());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: res.message });
                    btn.prop('disabled', false).find('.button-text').show().siblings('.sending-spinner').hide();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Swal.fire({ icon: 'error', title: 'Error!', text: 'Gagal terhubung ke server pengiriman. Status: ' + textStatus });
                btn.prop('disabled', false).find('.button-text').show().siblings('.sending-spinner').hide();
            }
        });
    }
    $('.kirim-pesan').on('click', function() { kirimPesan(this); });
    $('#kirim-semua').on('click', function() {
        var buttons = $('.kirim-pesan:visible');
        if (buttons.length === 0) return;
        Swal.fire({ title: 'Anda yakin?', text: "Mengirim " + buttons.length + " pesan?", icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, kirim semua!' }).then((result) => {
            if (result.isConfirmed) {
                 Swal.fire({ title: 'Mengirim semua pesan...', text: 'Mohon tunggu...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() } });
                buttons.each(function(index) { 
                    setTimeout(() => {
                        kirimPesan(this);
                        if (index + 1 === buttons.length) { 
                            setTimeout(() => {
                                if ($('.kirim-pesan:visible').length === 0) {
                                    Swal.close();
                                    location.reload();
                                }
                            }, 3000); 
                        }
                    }, index * 3000);
                });
            }
        });
    });
});
</script>