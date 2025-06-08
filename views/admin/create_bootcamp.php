<?php
// views/admin/create_bootcamp.php - Create Bootcamp Page
$pageTitle = 'Tambah Bootcamp Baru';

// Security function
function sanitizeOutput($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .form-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .form-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            overflow: hidden;
        }

        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
        }

        .section-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .section-header p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .section-content {
            padding: 25px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .required {
            color: #dc3545;
        }

        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .form-control.is-invalid + .invalid-feedback {
            display: block;
        }

        .image-upload-area {
            border: 2px dashed #e1e5e9;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: #f8f9fa;
        }

        .image-upload-area:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }

        .image-upload-area.dragover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }

        .upload-icon {
            font-size: 48px;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .upload-text {
            color: #495057;
            margin-bottom: 10px;
        }

        .upload-hint {
            font-size: 12px;
            color: #6c757d;
        }

        .image-preview {
            display: none;
            margin-top: 15px;
        }

        .preview-image {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .price-input-group {
            position: relative;
        }

        .currency-symbol {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-weight: 600;
        }

        .price-input-group .form-control {
            padding-left: 35px;
        }

        .duration-selector {
            display: grid;
            grid-template-columns: 1fr 120px;
            gap: 10px;
        }

        .quick-duration {
            display: flex;
            gap: 5px;
            margin-top: 10px;
        }

        .duration-chip {
            padding: 4px 10px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 15px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .duration-chip:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .status-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
        }

        .status-option {
            position: relative;
        }

        .status-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .status-option label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .status-option input:checked + label {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .status-indicator.active { background: #28a745; }
        .status-indicator.draft { background: #ffc107; }
        .status-indicator.archived { background: #6c757d; }

        .feature-toggles {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .feature-toggle {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .toggle-info {
            flex: 1;
        }

        .toggle-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 3px;
        }

        .toggle-description {
            font-size: 12px;
            color: #6c757d;
        }

        .toggle-switch {
            position: relative;
            width: 50px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #667eea;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }

        .form-actions {
            background: #f8f9fa;
            padding: 20px 25px;
            border-top: 1px solid #e9ecef;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .character-counter {
            font-size: 12px;
            color: #6c757d;
            text-align: right;
            margin-top: 5px;
        }

        .character-counter.warning {
            color: #ffc107;
        }

        .character-counter.danger {
            color: #dc3545;
        }

        .help-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }

        .slug-preview {
            font-size: 12px;
            color: #667eea;
            margin-top: 5px;
            font-family: monospace;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .duration-selector {
                grid-template-columns: 1fr;
            }
            
            .status-options {
                grid-template-columns: 1fr;
            }
            
            .feature-toggles {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content">
            <div class="page-header">
                <div>
                    <h1><?= $pageTitle ?></h1>
                    <div class="breadcrumb">
                        <i class="fas fa-home"></i> Admin / 
                        <a href="admin.php?action=manage_bootcamps" style="color: #667eea; text-decoration: none;">
                            <i class="fas fa-graduation-cap"></i> Kelola Bootcamps
                        </a> / 
                        Tambah Bootcamp
                    </div>
                </div>
                <div class="page-actions">
                    <a href="admin.php?action=manage_bootcamps" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </a>
                </div>
            </div>

            <div class="content-wrapper">
                <div class="form-container">
                    <!-- Alert Messages -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?= sanitizeOutput($_SESSION['success']) ?>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?= sanitizeOutput($_SESSION['error']) ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form method="POST" action="admin.php?action=create_bootcamp" enctype="multipart/form-data" id="createBootcampForm" novalidate>
                        <!-- CSRF Protection -->
                        <input type="hidden" name="csrf_token" value="<?= sanitizeOutput($_SESSION['csrf_token'] ?? '') ?>">

                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-info-circle"></i> Informasi Dasar</h3>
                                <p>Masukkan informasi dasar tentang bootcamp</p>
                            </div>
                            <div class="section-content">
                                <div class="form-grid">
                                    <div class="form-group full-width">
                                        <label for="title">
                                            <i class="fas fa-heading"></i>
                                            Judul Bootcamp <span class="required">*</span>
                                        </label>
                                        <input type="text" 
                                               id="title" 
                                               name="title" 
                                               class="form-control" 
                                               placeholder="Contoh: Full Stack Web Development Bootcamp"
                                               required
                                               maxlength="200"
                                               data-counter="title-counter">
                                        <div class="invalid-feedback">Judul bootcamp harus diisi dengan maksimal 200 karakter.</div>
                                        <div class="character-counter" id="title-counter">0/200</div>
                                        <div class="slug-preview" id="slug-preview"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="category_id">
                                            <i class="fas fa-tag"></i>
                                            Kategori <span class="required">*</span>
                                        </label>
                                        <select id="category_id" name="category_id" class="form-control" required>
                                            <option value="">Pilih Kategori</option>
                                            <?php if (isset($categories) && !empty($categories)): ?>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= $category['id'] ?>">
                                                        <?= sanitizeOutput($category['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <div class="invalid-feedback">Pilih kategori untuk bootcamp ini.</div>
                                        <div class="help-text">
                                            Belum ada kategori yang sesuai? 
                                            <a href="admin.php?action=manage_categories" target="_blank">Kelola kategori</a>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="instructor_name">
                                            <i class="fas fa-user-tie"></i>
                                            Nama Instruktur <span class="required">*</span>
                                        </label>
                                        <input type="text" 
                                               id="instructor_name" 
                                               name="instructor_name" 
                                               class="form-control" 
                                               placeholder="Nama instruktur utama"
                                               required
                                               maxlength="100">
                                        <div class="invalid-feedback">Nama instruktur harus diisi dengan maksimal 100 karakter.</div>
                                    </div>

                                    <div class="form-group full-width">
                                        <label for="description">
                                            <i class="fas fa-align-left"></i>
                                            Deskripsi <span class="required">*</span>
                                        </label>
                                        <textarea id="description" 
                                                  name="description" 
                                                  class="form-control" 
                                                  rows="6"
                                                  placeholder="Jelaskan secara detail tentang bootcamp ini, materi yang akan dipelajari, dan manfaatnya..."
                                                  required
                                                  maxlength="2000"
                                                  data-counter="description-counter"></textarea>
                                        <div class="invalid-feedback">Deskripsi bootcamp harus diisi dengan maksimal 2000 karakter.</div>
                                        <div class="character-counter" id="description-counter">0/2000</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Image Upload Section -->
                        <div class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-image"></i> Gambar Bootcamp</h3>
                                <p>Upload gambar thumbnail untuk bootcamp</p>
                            </div>
                            <div class="section-content">
                                <div class="form-group">
                                    <label for="image">
                                        <i class="fas fa-camera"></i>
                                        Gambar Thumbnail
                                    </label>
                                    <div class="image-upload-area" id="imageUploadArea">
                                        <div class="upload-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <div class="upload-text">
                                            <strong>Klik untuk upload</strong> atau drag & drop gambar di sini
                                        </div>
                                        <div class="upload-hint">
                                            Format: JPG, PNG, GIF (Maksimal 5MB, Rekomendasi 1200x600px)
                                        </div>
                                        <input type="file" 
                                               id="image" 
                                               name="image" 
                                               accept="image/jpeg,image/png,image/gif"
                                               style="display: none;">
                                    </div>
                                    <div class="image-preview" id="imagePreview">
                                        <img id="previewImage" src="" alt="Preview" class="preview-image">
                                        <button type="button" class="btn btn-sm btn-danger mt-2" id="removeImage">
                                            <i class="fas fa-trash"></i> Hapus Gambar
                                        </button>
                                    </div>
                                    <div class="help-text">
                                        Gambar akan digunakan sebagai thumbnail bootcamp di website
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Section -->
                        <div class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-dollar-sign"></i> Harga & Pembayaran</h3>
                                <p>Atur harga dan opsi pembayaran</p>
                            </div>
                            <div class="section-content">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="price">
                                            <i class="fas fa-tag"></i>
                                            Harga Normal <span class="required">*</span>
                                        </label>
                                        <div class="price-input-group">
                                            <span class="currency-symbol">Rp</span>
                                            <input type="number" 
                                                   id="price" 
                                                   name="price" 
                                                   class="form-control" 
                                                   placeholder="2500000"
                                                   required
                                                   min="0"
                                                   step="1000">
                                        </div>
                                        <div class="invalid-feedback">Masukkan harga normal bootcamp.</div>
                                    </div>

                                    <div class="form-group">
                                        <label for="discount_price">
                                            <i class="fas fa-percentage"></i>
                                            Harga Diskon
                                        </label>
                                        <div class="price-input-group">
                                            <span class="currency-symbol">Rp</span>
                                            <input type="number" 
                                                   id="discount_price" 
                                                   name="discount_price" 
                                                   class="form-control" 
                                                   placeholder="2000000"
                                                   min="0"
                                                   step="1000">
                                        </div>
                                        <div class="help-text">Kosongkan jika tidak ada diskon</div>
                                        <div id="discountInfo" class="help-text"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="max_participants">
                                            <i class="fas fa-users"></i>
                                            Maksimal Peserta
                                        </label>
                                        <input type="number" 
                                               id="max_participants" 
                                               name="max_participants" 
                                               class="form-control" 
                                               placeholder="50"
                                               min="1"
                                               max="1000">
                                        <div class="help-text">Kosongkan untuk unlimited peserta</div>
                                    </div>

                                    <div class="form-group">
                                        <label for="start_date">
                                            <i class="fas fa-calendar"></i>
                                            Tanggal Mulai
                                        </label>
                                        <input type="date" 
                                               id="start_date" 
                                               name="start_date" 
                                               class="form-control"
                                               min="<?= date('Y-m-d') ?>">
                                        <div class="help-text">Tanggal bootcamp akan dimulai</div>
                                    </div>

                                    <div class="form-group full-width">
                                        <label for="duration">
                                            <i class="fas fa-clock"></i>
                                            Durasi Bootcamp <span class="required">*</span>
                                        </label>
                                        <div class="duration-selector">
                                            <input type="text" 
                                                   id="duration" 
                                                   name="duration" 
                                                   class="form-control" 
                                                   placeholder="Contoh: 12 minggu, 3 bulan, 6 bulan"
                                                   required
                                                   maxlength="50">
                                            <select id="duration_unit" class="form-control">
                                                <option value="">Unit</option>
                                                <option value="hari">Hari</option>
                                                <option value="minggu">Minggu</option>
                                                <option value="bulan">Bulan</option>
                                            </select>
                                        </div>
                                        <div class="quick-duration">
                                            <span class="duration-chip" data-duration="6 minggu">6 minggu</span>
                                            <span class="duration-chip" data-duration="3 bulan">3 bulan</span>
                                            <span class="duration-chip" data-duration="6 bulan">6 bulan</span>
                                            <span class="duration-chip" data-duration="12 bulan">12 bulan</span>
                                        </div>
                                        <div class="invalid-feedback">Durasi bootcamp harus diisi.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Settings Section -->
                        <div class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-cog"></i> Pengaturan</h3>
                                <p>Status dan pengaturan tambahan</p>
                            </div>
                            <div class="section-content">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-toggle-on"></i>
                                        Status Bootcamp <span class="required">*</span>
                                    </label>
                                    <div class="status-options">
                                        <div class="status-option">
                                            <input type="radio" id="status_draft" name="status" value="draft" checked required>
                                            <label for="status_draft">
                                                <span class="status-indicator draft"></span>
                                                Draft
                                            </label>
                                        </div>
                                        <div class="status-option">
                                            <input type="radio" id="status_active" name="status" value="active">
                                            <label for="status_active">
                                                <span class="status-indicator active"></span>
                                                Aktif
                                            </label>
                                        </div>
                                        <div class="status-option">
                                            <input type="radio" id="status_archived" name="status" value="archived">
                                            <label for="status_archived">
                                                <span class="status-indicator archived"></span>
                                                Arsip
                                            </label>
                                        </div>
                                    </div>
                                    <div class="help-text">
                                        Draft: Belum dipublikasi | Aktif: Dapat dilihat dan dibeli | Arsip: Tidak aktif
                                    </div>
                                </div>

                                <div class="feature-toggles">
                                    <div class="feature-toggle">
                                        <div class="toggle-info">
                                            <div class="toggle-label">Featured Bootcamp</div>
                                            <div class="toggle-description">Tampilkan di halaman utama</div>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="featured" value="1">
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-section">
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i>
                                    Simpan Bootcamp
                                </button>
                                
                                <button type="button" class="btn btn-secondary" id="saveDraftBtn">
                                    <i class="fas fa-file-alt"></i>
                                    Simpan sebagai Draft
                                </button>
                                
                                <a href="admin.php?action=manage_bootcamps" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                    Batal
                                </a>
                                
                                <div class="ms-auto">
                                    <button type="button" class="btn btn-info" id="previewBtn">
                                        <i class="fas fa-eye"></i>
                                        Preview
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('createBootcampForm');
            const submitBtn = document.getElementById('submitBtn');
            const saveDraftBtn = document.getElementById('saveDraftBtn');
            
            // Character counters
            document.querySelectorAll('[data-counter]').forEach(input => {
                const counterId = input.dataset.counter;
                const counter = document.getElementById(counterId);
                const maxLength = input.getAttribute('maxlength');
                
                function updateCounter() {
                    const currentLength = input.value.length;
                    counter.textContent = `${currentLength}/${maxLength}`;
                    
                    if (currentLength > maxLength * 0.9) {
                        counter.classList.add('danger');
                        counter.classList.remove('warning');
                    } else if (currentLength > maxLength * 0.7) {
                        counter.classList.add('warning');
                        counter.classList.remove('danger');
                    } else {
                        counter.classList.remove('warning', 'danger');
                    }
                }
                
                input.addEventListener('input', updateCounter);
                updateCounter();
            });

            // Slug generation
            const titleInput = document.getElementById('title');
            const slugPreview = document.getElementById('slug-preview');
            
            titleInput.addEventListener('input', function() {
                const slug = generateSlug(this.value);
                slugPreview.textContent = slug ? `URL: /bootcamp/${slug}` : '';
            });

            function generateSlug(text) {
                return text
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim('-');
            }

            // Image upload
            const imageUploadArea = document.getElementById('imageUploadArea');
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const previewImage = document.getElementById('previewImage');
            const removeImageBtn = document.getElementById('removeImage');

            imageUploadArea.addEventListener('click', () => imageInput.click());

            imageUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            imageUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });

            imageUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleImageUpload(files[0]);
                }
            });

            imageInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    handleImageUpload(this.files[0]);
                }
            });

            function handleImageUpload(file) {
                // Validate file
                if (!file.type.match('image.*')) {
                    alert('File harus berupa gambar!');
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file maksimal 5MB!');
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    imagePreview.style.display = 'block';
                    imageUploadArea.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }

            removeImageBtn.addEventListener('click', function() {
                imageInput.value = '';
                imagePreview.style.display = 'none';
                imageUploadArea.style.display = 'block';
            });

            // Price calculations
            const priceInput = document.getElementById('price');
            const discountInput = document.getElementById('discount_price');
            const discountInfo = document.getElementById('discountInfo');

            function updateDiscountInfo() {
                const price = parseFloat(priceInput.value) || 0;
                const discount = parseFloat(discountInput.value) || 0;

                if (price > 0 && discount > 0) {
                    if (discount >= price) {
                        discountInfo.textContent = 'Harga diskon tidak boleh lebih besar atau sama dengan harga normal!';
                        discountInfo.style.color = '#dc3545';
                        discountInput.classList.add('is-invalid');
                    } else {
                        const percentage = Math.round(((price - discount) / price) * 100);
                        const savings = price - discount;
                        discountInfo.textContent = `Diskon ${percentage}% (Hemat Rp ${savings.toLocaleString('id-ID')})`;
                        discountInfo.style.color = '#28a745';
                        discountInput.classList.remove('is-invalid');
                    }
                } else {
                    discountInfo.textContent = '';
                    discountInput.classList.remove('is-invalid');
                }
            }

            priceInput.addEventListener('input', updateDiscountInfo);
            discountInput.addEventListener('input', updateDiscountInfo);

            // Duration quick select
            document.querySelectorAll('.duration-chip').forEach(chip => {
                chip.addEventListener('click', function() {
                    document.getElementById('duration').value = this.dataset.duration;
                });
            });

            // Save as draft
            saveDraftBtn.addEventListener('click', function() {
                document.getElementById('status_draft').checked = true;
                form.submit();
            });

            // Form validation
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                let isValid = true;
                const requiredFields = form.querySelectorAll('[required]');
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                // Validate discount price
                const price = parseFloat(priceInput.value) || 0;
                const discount = parseFloat(discountInput.value) || 0;
                
                if (discount > 0 && discount >= price) {
                    discountInput.classList.add('is-invalid');
                    isValid = false;
                }

                if (!isValid) {
                    alert('Mohon perbaiki error pada form sebelum menyimpan.');
                    return;
                }

                // Generate slug
                const slugInput = document.createElement('input');
                slugInput.type = 'hidden';
                slugInput.name = 'slug';
                slugInput.value = generateSlug(titleInput.value);
                form.appendChild(slugInput);

                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

                // Submit form
                this.submit();
            });

            // Auto-save draft
            let autoSaveTimer;
            const autoSaveDelay = 30000; // 30 seconds

            function scheduleAutoSave() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(() => {
                    saveFormData();
                }, autoSaveDelay);
            }

            function saveFormData() {
                const formData = new FormData(form);
                const data = Object.fromEntries(formData);
                localStorage.setItem('bootcamp_draft', JSON.stringify(data));
                console.log('Draft auto-saved');
            }

            // Load draft data
            const savedDraft = localStorage.getItem('bootcamp_draft');
            if (savedDraft) {
                try {
                    const data = JSON.parse(savedDraft);
                    Object.entries(data).forEach(([key, value]) => {
                        const field = form.querySelector(`[name="${key}"]`);
                        if (field && value) {
                            if (field.type === 'checkbox' || field.type === 'radio') {
                                field.checked = field.value === value;
                            } else {
                                field.value = value;
                            }
                        }
                    });
                } catch (e) {
                    console.error('Failed to load draft:', e);
                }
            }

            // Schedule auto-save on input
            form.addEventListener('input', scheduleAutoSave);

            // Clear draft on successful submit
            form.addEventListener('submit', function() {
                localStorage.removeItem('bootcamp_draft');
            });
        });
    </script>
</body>
</html>