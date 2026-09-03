<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'DecorNest - Không gian phòng ngủ an yên & sang trọng') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDesc ?? 'Đồ trang trí phòng ngủ phong cách Japandi & Scandinavian tối giản, sang trọng. Chốn về nghỉ ngơi, healing tâm trí sau ngày dài làm việc.') ?>">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🌿</text></svg>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cream':      '#FAF7F2',
                        'warmwhite':  '#FCFBF9',
                        'beige':      '#F0EAE1',
                        'sand':       '#E4D9C8',
                        'taupe':      '#C9BCAB',
                        'wood':       '#B07B52',
                        'wooddk':     '#8C5934',
                        'charcoal':   '#26211E',
                        'charcoaldk': '#1A1715',
                        'muted':      '#7D736A',
                        'sage':       '#5F7565',
                        'sage-light': '#EBF2ED',
                        'amber-warm': '#D98848',
                        'terracotta': '#C48B71',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                        display: ['"Cinzel"', 'serif'],
                    },
                    boxShadow: {
                        'warm': '0 4px 20px -2px rgba(90, 70, 50, 0.06), 0 2px 6px -1px rgba(90, 70, 50, 0.04)',
                        'warm-lg': '0 12px 36px -4px rgba(90, 70, 50, 0.09), 0 4px 12px -2px rgba(90, 70, 50, 0.05)',
                        'warm-xl': '0 24px 48px -6px rgba(90, 70, 50, 0.12), 0 8px 16px -4px rgba(90, 70, 50, 0.06)',
                        'glow-warm': '0 0 25px rgba(217, 136, 72, 0.15)',
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public' ?>/assets/css/app.css">

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-warmwhite font-sans text-charcoal antialiased flex flex-col min-h-screen selection:bg-sand/60 selection:text-charcoal" 
      x-data="{ cartOpen: false, searchOpen: false, mobileMenuOpen: false }">

    <?php require ROOT_PATH . '/app/views/frontend/partials/promo-bar.php'; ?>
    <?php require ROOT_PATH . '/app/views/frontend/partials/header.php'; ?>
    <?php require ROOT_PATH . '/app/views/frontend/partials/mega-menu.php'; ?>
    <?php require ROOT_PATH . '/app/views/frontend/partials/flash-message.php'; ?>

    <!-- Main Content -->
    <main class="flex-grow">
        <?= $content ?>
    </main>

    <?php require ROOT_PATH . '/app/views/frontend/partials/footer.php'; ?>

    <!-- Mini Cart Slide-out -->
    <?php require ROOT_PATH . '/app/views/frontend/partials/mini-cart-panel.php'; ?>

    <!-- Auth Modal -->
    <?php require ROOT_PATH . '/app/views/frontend/partials/auth-modal.php'; ?>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed bottom-6 right-6 z-50 space-y-3 pointer-events-none"></div>

    <!-- App JS Configuration & Scripts -->
    <script>
        const APP_URL = '<?= $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public' ?>';
    </script>
    <script src="<?= $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public' ?>/assets/js/app.js"></script>
</body>
</html>
