<?php include_once 'views/partials/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-comments"></i> Forum Diskusi</h2>
                <div class="btn-group">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="index.php?action=forum_create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Buat Post Baru
                        </a>
                        <a href="index.php?action=forum_my_posts" class="btn btn-outline-secondary">
                            <i class="fas fa-user"></i> Post Saya
                        </a>
                    <?php else: ?>
                        <a href="index.php?action=login" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Login untuk Posting
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Search Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="index.php" class="row g-3">
                        <input type="hidden" name="action" value="forum_search">
                        <div class="col-md-10">
                            <input type="text" name="q" class="form-control" 
                                   placeholder="Cari topik diskusi..." 
                                   value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    switch ($_GET['success']) {
                        case 'post_created':
                            echo 'Post berhasil dibuat!';
                            break;
                        case 'post_updated':
                            echo 'Post berhasil diperbarui!';
                            break;
                        case 'post_deleted':
                            echo 'Post berhasil dihapus!';
                            break;
                        case 'reply_added':
                            echo 'Reply berhasil ditambahkan!';
                            break;
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                    switch ($_GET['error']) {
                        case 'post_not_found':
                            echo 'Post tidak ditemukan!';
                            break;
                        case 'unauthorized':
                            echo 'Anda tidak memiliki akses untuk melakukan aksi ini!';
                            break;
                        default:
                            echo 'Terjadi kesalahan!';
                            break;
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Forum Posts -->
            <div class="row">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="col-md-12 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <h5 class="card-title">
                                                <a href="index.php?action=forum_detail&id=<?php echo $post['id']; ?>" 
                                                   class="text-decoration-none">
                                                    <?php echo htmlspecialchars($post['title']); ?>
                                                </a>
                                            </h5>
                                            <p class="card-text text-muted">
                                                <?php echo substr(htmlspecialchars($post['content']), 0, 150); ?>
                                                <?php if (strlen($post['content']) > 150): ?>...<?php endif; ?>
                                            </p>
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($post['user_name']); ?>
                                                <i class="fas fa-clock ms-3"></i> <?php echo date('d M Y H:i', strtotime($post['created_at'])); ?>
                                                <i class="fas fa-comments ms-3"></i> <?php echo $post['reply_count']; ?> balasan
                                            </small>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <a href="index.php?action=forum_detail&id=<?php echo $post['id']; ?>" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i> Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Belum ada post forum</h4>
                            <p class="text-muted">Jadilah yang pertama membuat diskusi!</p>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="index.php?action=forum_create" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Buat Post Pertama
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if (isset($total_pages) && $total_pages > 1): ?>
                <nav aria-label="Forum pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?action=forum&page=<?php echo ($page - 1); ?>">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="index.php?action=forum&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?action=forum&page=<?php echo ($page + 1); ?>">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once 'views/partials/footer.php'; ?>