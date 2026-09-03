<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

if ($flash):
    $type = $flash['type'] ?? 'info';
    $msg  = $flash['message'] ?? '';
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    <div class="p-4 rounded-2xl border text-xs sm:text-sm flex items-center justify-between shadow-warm <?= 
        $type === 'success' ? 'bg-[#EBF2ED] border-[#D5E2D8] text-[#44594A]' :
        ($type === 'error'   ? 'bg-red-50 border-red-200 text-red-700' :
                              'bg-cream border-sand text-charcoal')
    ?>">
        <div class="flex items-center gap-3">
            <span class="text-base">
                <?= $type === 'success' ? '🌿' : ($type === 'error' ? '⚠️' : 'ℹ️') ?>
            </span>
            <span><?= htmlspecialchars($msg) ?></span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-muted hover:text-charcoal p-1 text-base leading-none">&times;</button>
    </div>
</div>
<?php endif; ?>
