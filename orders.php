<?php
require_once __DIR__ . '/includes/auth_check.php';
checkAuth('vendor');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Orders - VendLink</title>
  <link rel="stylesheet" href="assets/css/style.css?v=11">
  <script src="assets/js/lucide.min.js?v=3"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 font-sans">
  <?php include __DIR__ . '/includes/header.php'; ?>
  <main class="page-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between gap-4 mb-8">
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-blue-600">Procurement</p>
        <h1 class="text-2xl font-black text-slate-900 mt-1">My Orders</h1>
        <p class="text-sm text-slate-500 mt-1">Track your wholesale purchases and delivery details.</p>
      </div>
      <a href="vendor.php" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-500/20 flex items-center gap-2"><i data-lucide="store" class="w-4 h-4"></i> Browse Supplies</a>
    </div>
    <div id="ordersState" class="py-12 text-center text-slate-400">Loading your orders...</div>
    <div id="ordersList" class="space-y-4"></div>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/js/api.js"></script>
  <script>
    const peso = value => '₱' + Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const escapeHtml = value => String(value ?? '').replace(/[&<>'"]/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[character]));
    const statusClass = status => ({ delivered: 'bg-emerald-100 text-emerald-700', confirmed: 'bg-blue-100 text-blue-700', cancelled: 'bg-red-100 text-red-700' }[status] || 'bg-amber-100 text-amber-700');
    const safeImageUrl = value => /^(https:\/\/|uploads\/products\/prod_[A-Za-z0-9_-]+\.(jpg|png|webp))$/i.test(String(value || '')) ? String(value) : '';

    async function loadOrders() {
      const state = document.getElementById('ordersState');
      try {
        const orders = await API.get('api/orders.php');
        if (!orders.length) {
          state.textContent = 'No orders placed yet. Browse the marketplace to get started.';
          return;
        }
        state.remove();
        document.getElementById('ordersList').innerHTML = orders.map(order => `
          <article class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 border-b border-slate-100 pb-4">
              <div><p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Supplier receipt #${escapeHtml(order.supplierOrderNumber)}</p><h2 class="text-base font-black text-slate-900 mt-1">${escapeHtml(order.supplierBusinessName || order.supplierName || 'Supplier')}</h2><p class="text-xs text-slate-400 mt-1">${new Date(order.createdAt).toLocaleString()}</p></div>
              <span class="self-start px-2.5 py-1 text-[10px] font-bold rounded-lg uppercase tracking-wider ${statusClass(order.status)}">${escapeHtml(order.status)}</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 text-sm">
              <div><p class="text-xs text-slate-400">Items</p>${(order.items || []).map(item => `<div class="flex items-center gap-2 mt-2"><div class="w-9 h-9 rounded-md bg-slate-50 overflow-hidden flex items-center justify-center shrink-0">${safeImageUrl(item.imageUrl) ? `<img src="${escapeHtml(safeImageUrl(item.imageUrl))}" alt="${escapeHtml(item.name)}" class="w-full h-full object-cover">` : '<i data-lucide="package" class="w-4 h-4 text-slate-300"></i>'}</div><p class="font-semibold text-slate-700">${escapeHtml(item.quantity)}x ${escapeHtml(item.name)}</p></div>`).join('')}</div>
              <div><p class="text-xs text-slate-400">Payment</p><p class="font-semibold text-slate-700 mt-1">${escapeHtml(order.paymentMethod)}</p></div>
              <div><p class="text-xs text-slate-400">Grand total</p><p class="font-black text-blue-600 text-lg mt-1">${peso(order.totalAmount)}</p></div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-500"><span class="font-bold text-slate-700">Deliver to:</span> ${escapeHtml(order.deliveryAddress || 'Address not recorded')}</div>
          </article>
        `).join('');
        lucide.createIcons();
      } catch (error) {
        state.className = 'p-4 rounded-xl bg-red-50 text-red-600 text-sm';
        state.textContent = error.message || 'Unable to load your orders.';
      }
    }
    document.addEventListener('DOMContentLoaded', loadOrders);
  </script>
</body>
</html>
