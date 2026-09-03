<?php
$current  = $current_page ?? 1;
$last     = $last_page ?? 1;
$params   = $_GET;

if (!function_exists('buildPageUrl')) {
    function buildPageUrl(int $page, array $params): string {
        $params['page'] = $page;
        return '?' . http_build_query($params);
    }
}
?>
<nav class="flex items-center gap-1.5" aria-label="Phân trang">
    <!-- Prev -->
    <?php if ($current > 1): ?>
    <a href="<?= buildPageUrl($current - 1, $params) ?>"
       class="w-10 h-10 border border-beige rounded-xl flex items-center justify-center text-xs font-semibold text-charcoal hover:bg-cream transition"
       aria-label="Trang trước">
        ←
    </a>
    <?php endif; ?>

    <?php
    $range = 2;
    for ($p = max(1, $current - $range); $p <= min($last, $current + $range); $p++):
    ?>
    <a href="<?= buildPageUrl($p, $params) ?>"
       class="w-10 h-10 rounded-xl text-xs font-bold flex items-center justify-center transition <?= $p === $current ? 'bg-charcoal text-white shadow-warm' : 'border border-beige text-charcoal hover:bg-cream' ?>">
        <?= $p ?>
    </a>
    <?php endfor; ?>

    <!-- Next -->
    <?php if ($current < $last): ?>
    <a href="<?= buildPageUrl($current + 1, $params) ?>"
       class="w-10 h-10 border border-beige rounded-xl flex items-center justify-center text-xs font-semibold text-charcoal hover:bg-cream transition"
       aria-label="Trang tiếp">
        →
    </a>
    <?php endif; ?>
</nav>
