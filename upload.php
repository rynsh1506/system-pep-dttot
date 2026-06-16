<?php include 'layout/header.php'; ?>

<div class="dashboard-header" style="margin-bottom: 2rem;">
    <h2 style="font-weight: 700; color: var(--primary-color);">Upload Data DTTOT</h2>
    <p style="color: var(--text-secondary); font-size: 0.9rem;">Impor data Terduga Teroris dari file Excel atau CSV.</p>
</div>

<div class="upload-container">
    <form action="save_data.php" method="POST" enctype="multipart/form-data" id="uploadForm">
        <div class="drop-zone" id="dropZone">
            <i class="fas fa-file-excel"></i>
            <h3>Tarik & Lepas File ke Sini</h3>
            <p>atau klik untuk pilih file (Support .xlsx, .csv)</p>
            <input type="file" name="excel_file" id="fileInput" accept=".xlsx, .csv" required>

            <div id="fileInfo" class="selected-file">
                <div>
                    <i class="fas fa-file-alt"
                        style="font-size: 1.2rem; color: var(--text-secondary); margin-bottom: 0;"></i>
                    <span id="fileName" style="margin-left: 10px; font-weight: 500;">nama_file.xlsx</span>
                </div>
                <i class="fas fa-times" id="removeFile" style="cursor: pointer; color: #e74a3b;"></i>
            </div>
        </div>

        <div
            style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; text-align: right;">
            <a href="index.php"
                style="text-decoration: none; color: var(--text-secondary); margin-right: 2rem; font-size: 0.9rem;">Batal</a>
            <button type="submit" class="btn-upload" id="submitBtn" disabled>
                <i class="fas fa-cloud-upload-alt" style="margin-right: 8px;"></i> Mulai Impor Data
            </button>
        </div>
    </form>
</div>

<div
    style="margin-top: 2rem; background: rgba(78, 115, 223, 0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(78, 115, 223, 0.1);">
    <h4 style="color: var(--accent-color); margin-bottom: 0.5rem;"><i class="fas fa-info-circle"></i> Petunjuk Format
    </h4>
    <p style="font-size: 0.85rem; color: var(--text-primary); line-height: 1.6;">
        Pastikan file Excel Anda memiliki urutan kolom berikut (tanpa header juga boleh, tapi urutan harus sama):<br>
        <strong>Nama | Deskripsi | Terduga (Orang/Korporasi) | Kode Densus | Tempat Lahir | Tanggal Lahir (DD/MM/YYYY) |
            WN/Asal Negara | Alamat</strong>
    </p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const removeFile = document.getElementById('removeFile');
        const submitBtn = document.getElementById('submitBtn');

        // Click to select
        dropZone.addEventListener('click', () => fileInput.click());

        // Drag & Drop effects
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.add('active');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.remove('active');
            }, false);
        });

        // Handle Drop
        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length) {
                fileInput.files = files;
                updateFileInfo(files[0]);
            }
        });

        // Handle Select
        fileInput.addEventListener('change', function () {
            if (this.files.length) {
                updateFileInfo(this.files[0]);
            }
        });

        function updateFileInfo(file) {
            fileName.textContent = file.name;
            fileInfo.style.display = 'flex';
            submitBtn.disabled = false;
            // Hide icons/text in dropzone
            dropZone.querySelector('i').style.display = 'none';
            dropZone.querySelector('h3').style.display = 'none';
            dropZone.querySelector('p').style.display = 'none';
        }

        removeFile.addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.value = '';
            fileInfo.style.display = 'none';
            submitBtn.disabled = true;
            dropZone.querySelector('i').style.display = 'block';
            dropZone.querySelector('h3').style.display = 'block';
            dropZone.querySelector('p').style.display = 'block';
        });
    });
</script>

<?php include 'layout/footer.php'; ?>