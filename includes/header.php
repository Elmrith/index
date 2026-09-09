<?php
// includes/header.php
require_once __DIR__ . '/security.php';
$userRole = $_SESSION['user_role'] ?? null;
$userName = $_SESSION['user_name'] ?? 'User';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- Local offline styling and icons -->
<link rel="stylesheet" href="assets/css/style.css?v=11">
<script src="assets/js/lucide.min.js?v=3"></script>
<script src="assets/js/theme.js?v=1"></script>
<meta name="csrf-token" content="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">

<header class="site-header sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm">
  <div class="site-header-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
    <!-- Brand Logo -->
    <a href="index.php" class="flex items-center gap-2 text-xl font-extrabold text-slate-900 tracking-tight">
      <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20">
        <i data-lucide="boxes" class="w-5 h-5"></i>
      </div>
      <span>Vend<span class="text-blue-600">Link</span></span>
    </a>

    <!-- Navigation Links -->
    <button type="button" class="mobile-menu-button" aria-controls="primaryNavigation" aria-expanded="false" aria-label="Open navigation menu" onclick="toggleNavigation()">
      <i data-lucide="menu" class="w-5 h-5"></i>
    </button>

    <nav id="primaryNavigation" class="primary-navigation flex items-center gap-1 sm:gap-2" aria-label="Primary navigation">
      <?php if ($userRole === 'vendor'): ?>
        <a href="marketplace.php" class="nav-link <?= $currentPage === 'marketplace.php' ? 'active' : '' ?> flex items-center gap-1.5">
          <i data-lucide="store" class="w-4 h-4"></i> Marketplace
        </a>
        <a href="orders.php" class="nav-link <?= $currentPage === 'orders.php' ? 'active' : '' ?> flex items-center gap-1.5">
          <i data-lucide="shopping-bag" class="w-4 h-4"></i> My Orders
        </a>
        <a href="profile.php" class="nav-link <?= $currentPage === 'profile.php' ? 'active' : '' ?> flex items-center gap-1.5">
          <i data-lucide="user-cog" class="w-4 h-4"></i> Profile
        </a>
      <?php elseif ($userRole === 'supplier'): ?>
        <a href="supplier.php" class="nav-link <?= $currentPage === 'supplier.php' ? 'active' : '' ?> flex items-center gap-1.5">
          <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Supplier Hub
        </a>
        <a href="profile.php" class="nav-link <?= $currentPage === 'profile.php' ? 'active' : '' ?> flex items-center gap-1.5">
          <i data-lucide="user-cog" class="w-4 h-4"></i> Profile
        </a>
      <?php elseif ($userRole === 'admin'): ?>
        <a href="admin.php" class="nav-link <?= $currentPage === 'admin.php' ? 'active' : '' ?> flex items-center gap-1.5">
          <i data-lucide="shield-check" class="w-4 h-4"></i> Admin Panel
        </a>
        <a href="marketplace.php" class="nav-link <?= $currentPage === 'marketplace.php' ? 'active' : '' ?> flex items-center gap-1.5">
          Marketplace
        </a>
        <a href="profile.php" class="nav-link <?= $currentPage === 'profile.php' ? 'active' : '' ?> flex items-center gap-1.5">
          <i data-lucide="user-cog" class="w-4 h-4"></i> Profile
        </a>
      <?php endif; ?>

      <?php if (isset($_SESSION['user_id'])): ?>
        <div class="h-6 w-px bg-slate-200 mx-2"></div>
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-3">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-xs text-slate-700">
                <?= strtoupper(substr($userName, 0, 1)) ?>
              </div>
              <div class="hidden md:block text-left">
                <div class="text-xs font-bold text-slate-900"><?= htmlspecialchars($userName) ?></div>
                <div class="text-[10px] uppercase font-semibold text-slate-500 tracking-wider"><span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 text-[10px]"><?= ucfirst($userRole) ?></span></div>
              </div>
            </div>
          </div>

          <button onclick="logout()" class="logout-button px-3 py-1 text-white bg-red-500 hover:bg-red-600 rounded-lg shadow-sm transition" title="Logout">
            <i data-lucide="log-out" class="w-4 h-4 inline-block mr-1"></i> Logout
          </button>
        </div>
      <?php else: ?>
        <a href="index.php" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm">
          Sign In
        </a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<script>
  function toggleNavigation() {
    const navigation = document.getElementById('primaryNavigation');
    const menuButton = document.querySelector('.mobile-menu-button');
    const isOpen = navigation.classList.toggle('is-open');
    menuButton.setAttribute('aria-expanded', String(isOpen));
    menuButton.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
    menuButton.querySelector('svg')?.replaceWith(Object.assign(document.createElement('i'), {
      dataset: { lucide: isOpen ? 'x' : 'menu' }, className: 'w-5 h-5'
    }));
    lucide.createIcons();
  }
  lucide.createIcons();
</script>
