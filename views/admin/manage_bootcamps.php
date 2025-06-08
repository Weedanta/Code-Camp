<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Bootcamps - Code Camp Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#1e40af',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 overflow-y-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 p-4 lg:p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="ml-12 lg:ml-0">
                        <h1 class="text-2xl font-bold text-gray-800">Kelola Bootcamps</h1>
                        <p class="text-gray-600 mt-1">Manajemen program bootcamp Code Camp</p>
                    </div>
                    <div class="flex items-center space-x-4 mt-4 lg:mt-0">
                        <a 
                            href="admin.php?action=create_bootcamp" 
                            class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Bootcamp
                        </a>
                        <span class="bg-primary text-white px-3 py-1 rounded-full text-sm">
                            Total: <?= number_format($totalBootcamps) ?>
                        </span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-4 lg:p-6 space-y-6">
                <!-- Alert Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" role="alert">
                        <span class="block sm:inline"><?= htmlspecialchars($_SESSION['success']) ?></span>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
                        <span class="block sm:inline"><?= htmlspecialchars($_SESSION['error']) ?></span>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Filters and Search -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <form method="GET" action="admin.php" class="flex flex-col lg:flex-row gap-4">
                        <input type="hidden" name="action" value="manage_bootcamps">
                        
                        <!-- Search -->
                        <div class="flex-1">
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Cari judul, instruktur, atau deskripsi..." 
                                value="<?= htmlspecialchars($search ?? '') ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                            >
                        </div>

                        <!-- Category Filter -->
                        <div class="w-full lg:w-48">
                            <select 
                                name="category" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                            >
                                <option value="">Semua Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($category ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="w-full lg:w-48">
                            <select 
                                name="status" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                            >
                                <option value="">Semua Status</option>
                                <option value="active" <?= ($status ?? '') == 'active' ? 'selected' : '' ?>>Aktif</option>
                                <option value="draft" <?= ($status ?? '') == 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="archived" <?= ($status ?? '') == 'archived' ? 'selected' : '' ?>>Arsip</option>
                            </select>
                        </div>

                        <!-- Search Button -->
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition-colors duration-200"
                        >
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>
                    </form>
                </div>

                <!-- Bootcamps Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (empty($bootcamps)): ?>
                        <div class="col-span-full">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                                <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 mb-4">Tidak ada bootcamp ditemukan</p>
                                <a 
                                    href="admin.php?action=create_bootcamp" 
                                    class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition-colors duration-200"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Buat Bootcamp Pertama
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($bootcamps as $bootcamp): ?>
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-200">
                                <!-- Bootcamp Image -->
                                <div class="relative h-48 bg-gray-200">
                                    <?php if (!empty($bootcamp['image'])): ?>
                                        <img 
                                            src="assets/images/bootcamps/<?= htmlspecialchars($bootcamp['image']) ?>" 
                                            alt="<?= htmlspecialchars($bootcamp['title']) ?>"
                                            class="w-full h-full object-cover"
                                        >
                                    <?php else: ?>
                                        <div class="w-full h-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                                            <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Featured Badge -->
                                    <?php if ($bootcamp['featured']): ?>
                                        <div class="absolute top-2 left-2">
                                            <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                                Featured
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Status Badge -->
                                    <div class="absolute top-2 right-2">
                                        <?php 
                                        $statusClass = match($bootcamp['status'] ?? 'draft') {
                                            'active' => 'bg-green-100 text-green-800',
                                            'draft' => 'bg-gray-100 text-gray-800',
                                            'archived' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                        ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full <?= $statusClass ?>">
                                            <?= ucfirst($bootcamp['status'] ?? 'draft') ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Bootcamp Content -->
                                <div class="p-6">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-800 mb-1 line-clamp-2">
                                                <?= htmlspecialchars($bootcamp['title']) ?>
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                <?= htmlspecialchars($bootcamp['category_name'] ?? 'Tidak berkategori') ?>
                                            </p>
                                        </div>
                                        
                                        <!-- Featured Toggle -->
                                        <button 
                                            onclick="toggleFeatured(<?= $bootcamp['id'] ?>, <?= $bootcamp['featured'] ? 'false' : 'true' ?>)"
                                            class="ml-2 p-1 rounded-full hover:bg-gray-100 transition-colors duration-200"
                                            title="<?= $bootcamp['featured'] ? 'Hapus dari featured' : 'Jadikan featured' ?>"
                                        >
                                            <svg class="w-5 h-5 <?= $bootcamp['featured'] ? 'text-yellow-500' : 'text-gray-400' ?>" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                        <?= htmlspecialchars(substr($bootcamp['description'] ?? '', 0, 100)) ?>...
                                    </p>

                                    <!-- Stats -->
                                    <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                                        <div>
                                            <span class="text-gray-500">Instruktur:</span>
                                            <p class="font-medium text-gray-800"><?= htmlspecialchars($bootcamp['instructor_name']) ?></p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Peserta:</span>
                                            <p class="font-medium text-gray-800"><?= number_format($bootcamp['total_enrollments'] ?? 0) ?></p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Harga:</span>
                                            <p class="font-medium text-gray-800">
                                                <?php if ($bootcamp['discount_price'] > 0): ?>
                                                    <span class="line-through text-gray-500">Rp <?= number_format($bootcamp['price'], 0, ',', '.') ?></span>
                                                    <span class="text-primary">Rp <?= number_format($bootcamp['discount_price'], 0, ',', '.') ?></span>
                                                <?php else: ?>
                                                    Rp <?= number_format($bootcamp['price'], 0, ',', '.') ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Rating:</span>
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <span class="ml-1 text-sm font-medium">
                                                    <?= number_format($bootcamp['avg_rating'] ?? 0, 1) ?>
                                                </span>
                                                <span class="ml-1 text-xs text-gray-500">
                                                    (<?= number_format($bootcamp['review_count'] ?? 0) ?>)
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                                        <div class="flex space-x-2">
                                            <a 
                                                href="admin.php?action=edit_bootcamp&id=<?= $bootcamp['id'] ?>" 
                                                class="text-primary hover:text-secondary"
                                                title="Edit bootcamp"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <a 
                                                href="admin.php?action=delete_bootcamp&id=<?= $bootcamp['id'] ?>" 
                                                class="text-red-600 hover:text-red-700"
                                                onclick="return confirm('Yakin ingin menghapus bootcamp ini?')"
                                                title="Hapus bootcamp"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </a>
                                        </div>
                                        
                                        <div class="text-xs text-gray-500">
                                            <?= date('d M Y', strtotime($bootcamp['created_at'] ?? 'now')) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-4">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div class="text-sm text-gray-700">
                                Menampilkan <?= (($page - 1) * 20) + 1 ?> sampai <?= min($page * 20, $totalBootcamps) ?> dari <?= $totalBootcamps ?> bootcamp
                            </div>
                            
                            <div class="flex items-center space-x-1">
                                <!-- Previous Page -->
                                <?php if ($page > 1): ?>
                                    <a 
                                        href="admin.php?action=manage_bootcamps&page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $category ? '&category=' . $category : '' ?><?= $status ? '&status=' . urlencode($status) : '' ?>" 
                                        class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                    >
                                        Sebelumnya
                                    </a>
                                <?php endif; ?>

                                <!-- Page Numbers -->
                                <?php
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                ?>
                                
                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <a 
                                        href="admin.php?action=manage_bootcamps&page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $category ? '&category=' . $category : '' ?><?= $status ? '&status=' . urlencode($status) : '' ?>" 
                                        class="px-3 py-2 text-sm border rounded-md <?= $i == $page ? 'bg-primary text-white border-primary' : 'bg-white border-gray-300 hover:bg-gray-50' ?>"
                                    >
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <!-- Next Page -->
                                <?php if ($page < $totalPages): ?>
                                    <a 
                                        href="admin.php?action=manage_bootcamps&page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $category ? '&category=' . $category : '' ?><?= $status ? '&status=' . urlencode($status) : '' ?>" 
                                        class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                    >
                                        Selanjutnya
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function toggleFeatured(bootcampId, featured) {
            fetch(`admin.php?action=toggle_featured_bootcamp&id=${bootcampId}&featured=${featured}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengupdate featured status');
            });
        }

        // Auto dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>