<?php
// marketplace.php - Vendor Marketplace & Product Discovery
require_once __DIR__ . '/includes/auth_check.php';
checkAuth(['vendor', 'admin']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Marketplace - VendLink</title>
  <link rel="stylesheet" href="assets/css/style.css?v=11">
  <script src="assets/js/lucide.min.js?v=3"></script>
</head>
<body class="<?= $_SESSION['user_role'] === 'admin' ? 'role-admin' : 'role-vendor' ?> min-h-screen bg-slate-50 text-slate-900 font-sans antialiased">
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main class="page-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <section class="marketplace-overview mb-8" aria-labelledby="marketplaceOverviewTitle">
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-3 mb-4">
        <div>
          <p class="marketplace-overview-kicker">Vendor overview</p>
          <h2 id="marketplaceOverviewTitle" class="marketplace-overview-title">Your purchasing activity</h2>
          <p class="marketplace-overview-description">Track orders and sourcing while you shop for supplies.</p>
        </div>
        <a href="vendor.php#sectionProcurement" class="marketplace-overview-link">View full sourcing summary <span aria-hidden="true">&rarr;</span></a>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="overview-stat-card overview-stat-blue">
          <i data-lucide="shopping-bag" class="w-5 h-5"></i>
          <div><div class="overview-stat-label">Total Orders</div><div id="marketplaceTotalOrders" class="overview-stat-value">0</div></div>
        </div>
        <div class="overview-stat-card overview-stat-amber">
          <i data-lucide="clock" class="w-5 h-5"></i>
          <div><div class="overview-stat-label">Pending Orders</div><div id="marketplacePendingOrders" class="overview-stat-value">0</div></div>
        </div>
        <div class="overview-stat-card overview-stat-green">
          <i data-lucide="receipt" class="w-5 h-5"></i>
          <div><div class="overview-stat-label">Total Spend</div><div id="marketplaceTotalSpend" class="overview-stat-value">₱0.00</div></div>
        </div>
      </div>
    </section>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Supplies Marketplace</h1>
        <p class="text-sm text-slate-500 mt-0.5">Browse current inventory from verified B2B food producers & distributors</p>
      </div>

      <div class="flex items-center gap-3">
        <div class="relative flex-1 sm:w-80">
          <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
          <input type="text" id="searchInput" placeholder="Search rice, eggs, poultry, sugar..."
            class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 shadow-sm">
        </div>
        <select id="categoryFilter" class="px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 shadow-sm focus:outline-none">
          <option value="all">All Categories</option>
          <option value="Grains">Grains & Rice</option>
          <option value="Poultry">Poultry & Meat</option>
          <option value="Produce">Fresh Produce</option>
          <option value="Baking">Baking Essentials</option>
          <option value="Oils">Oils & Seasonings</option>
        </select>
      </div>
    </div>

    <!-- Product Grid -->
    <div id="productGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div class="col-span-full py-12 text-center text-slate-400">Loading marketplace supplies...</div>
    </div>

    <section class="marketplace-summary mt-10" aria-labelledby="marketplaceSummaryTitle">
      <div class="flex items-center justify-between gap-3 mb-4">
        <div>
          <p class="text-xs font-bold uppercase tracking-wider text-blue-600">Purchasing insights</p>
          <h2 id="marketplaceSummaryTitle" class="text-lg font-black text-slate-900 mt-1">Purchase Volume &amp; Sourcing Summary</h2>
        </div>
        <a href="vendor.php#sectionProcurement" class="text-sm font-bold text-blue-600 hover:underline">Details</a>
      </div>
      <div id="marketplaceSummaryGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="summary-empty">Loading your purchasing summary...</div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script src="assets/js/api.js"></script>
  <script>
    let products = [];
    const escapeHtml = value => String(value ?? '').replace(/[&<>'"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[c]));
    const safeImageUrl = value => /^(https:\/\/|uploads\/products\/prod_[A-Za-z0-9_-]+\.(jpg|png|webp))$/i.test(String(value || '')) ? String(value) : '';

    const formatPeso = value => '₱' + Number(value || 0).toLocaleString('en-US', {minimumFractionDigits: 2});

    async function loadVendorOverview() {
      if (document.body.classList.contains('role-admin')) return;
      try {
        const [summary, analytics] = await Promise.all([API.get('api/vendor.php?action=summary'), API.get('api/analytics.php')]);
        document.getElementById('marketplaceTotalOrders').textContent = Number(summary?.totalOrders || 0);
        document.getElementById('marketplacePendingOrders').textContent = Number(summary?.pendingOrders || 0);
        document.getElementById('marketplaceTotalSpend').textContent = formatPeso(summary?.totalSpend);

        const top = analytics?.vendor?.top || {};
        document.getElementById('marketplaceSummaryGrid').innerHTML = [
          ['package', 'Units Purchased', Number(top.totalUnitsPurchased || 0), 'overview-stat-blue'],
          ['wallet', 'Procurement Spend', formatPeso(top.totalProcurementSpend), 'overview-stat-green'],
          ['truck', 'Completed Deliveries', Number(top.totalCompletedDeliveries || 0), 'overview-stat-amber'],
          ['users', 'Active Suppliers', Number(top.activeSuppliers || 0), 'overview-stat-blue']
        ].map(([icon, label, value, color]) => `<div class="summary-stat ${color}"><i data-lucide="${icon}" class="w-5 h-5"></i><div><div class="overview-stat-label">${label}</div><div class="overview-stat-value">${value}</div></div></div>`).join('');
        lucide.createIcons();
      } catch (err) {
        console.error('Failed to load vendor overview:', err);
        document.getElementById('marketplaceSummaryGrid').innerHTML = '<div class="summary-empty">Purchasing summary is temporarily unavailable.</div>';
      }
    }

    async function loadMarketplace() {
      const grid = document.getElementById('productGrid');
      try {
        products = await API.get('api/products.php');
        renderProducts(products);
      } catch (err) {
        grid.innerHTML = `<div class="col-span-full p-4 bg-red-50 text-red-600 rounded-xl text-center">${escapeHtml(err.message)}</div>`;
      }
    }

    function renderProducts(items) {
      const grid = document.getElementById('productGrid');
      const canOrder = !document.body.classList.contains('role-admin');
      if (!items.length) {
        grid.innerHTML = `
          <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-slate-200">
            <i data-lucide="package-search" class="w-12 h-12 text-slate-300 mx-auto mb-2"></i>
            <h3 class="font-bold text-slate-700">No products available</h3>
          </div>
        `;
        lucide.createIcons();
        return;
      }

      grid.innerHTML = items.map(p => `
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition overflow-hidden flex flex-col">
          <div class="h-44 bg-slate-100 flex items-center justify-center relative">
            ${safeImageUrl(p.imageUrl) ? `<img src="${escapeHtml(safeImageUrl(p.imageUrl))}" alt="${escapeHtml(p.name)}" class="w-full h-full object-cover">` : `<i data-lucide="package" class="w-12 h-12 text-slate-300"></i>`}
            <span class="absolute top-3 left-3 px-2.5 py-1 text-[10px] font-bold rounded-lg uppercase ${p.demandStatus === 'high' ? 'bg-red-500 text-white' : 'bg-amber-500 text-white'}">
              ${escapeHtml(p.demandStatus)} Demand
            </span>
          </div>
          <div class="p-5 flex-1 flex flex-col">
            <div class="text-xs font-semibold text-blue-600 mb-1">${escapeHtml(p.category || 'General')}</div>
            <h3 class="font-bold text-slate-900 text-base mb-1">${escapeHtml(p.name)}</h3>
            <p class="text-xs text-slate-500 mb-3">${escapeHtml(p.supplierBusinessName || p.supplierName || 'Verified Supplier')}</p>
            <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
              <div>
                <div class="text-[10px] text-slate-400 font-bold uppercase">Price</div>
                <div class="text-lg font-black text-slate-900">₱${Number(p.price).toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
              </div>
              ${canOrder ? `<a href="vendor.php?product=${encodeURIComponent(p.id)}" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition">Order Now</a>` : ''}
            </div>
          </div>
        </div>
      `).join('');
      lucide.createIcons();
    }

    document.getElementById('searchInput').addEventListener('input', e => {
      const q = e.target.value.toLowerCase();
      renderProducts(products.filter(p => p.name.toLowerCase().includes(q) || (p.category && p.category.toLowerCase().includes(q))));
    });

    document.addEventListener('DOMContentLoaded', () => {
      loadMarketplace();
      loadVendorOverview();
      lucide.createIcons();
    });
  </script>
</body>
</html>