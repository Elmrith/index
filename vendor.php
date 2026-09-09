<?php
// vendor.php - Vendor Dashboard & Marketplace
require_once __DIR__ . '/includes/auth_check.php';
checkAuth('vendor');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vendor Dashboard - VendLink</title>
  <link rel="stylesheet" href="assets/css/style.css?v=11">
  <script src="assets/js/lucide.min.js?v=3"></script>
</head>
<body class="role-vendor min-h-screen bg-slate-50 text-slate-900 font-sans antialiased">
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main class="page-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- Top Vendor Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
          <i data-lucide="shopping-bag" class="w-6 h-6"></i>
        </div>
        <div>
          <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Total Orders</div>
          <div id="statTotalOrders" class="text-2xl font-black text-slate-900 mt-0.5">0</div>
        </div>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
          <i data-lucide="clock" class="w-6 h-6"></i>
        </div>
        <div>
          <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Pending Orders</div>
          <div id="statPendingOrders" class="text-2xl font-black text-slate-900 mt-0.5">0</div>
        </div>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
          <i data-lucide="receipt" class="w-6 h-6"></i>
        </div>
        <div>
          <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Total Spend</div>
          <div id="statTotalSpend" class="text-2xl font-black text-slate-900 mt-0.5">₱0.00</div>
        </div>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-slate-200">
      <button id="tabMarketplaceBtn" onclick="switchVendorTab('marketplace')" 
        class="py-3 px-5 text-sm font-bold border-b-2 border-blue-600 text-blue-600 flex items-center gap-2">
        <i data-lucide="store" class="w-4 h-4"></i> Browse Supplies
      </button>
      <button id="tabOrdersBtn" onclick="switchVendorTab('orders')" 
        class="py-3 px-5 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-900 flex items-center gap-2">
        <i data-lucide="package-check" class="w-4 h-4"></i> Order History & Status
      </button>
    </div>

    <!-- SECTION 1: SUPPLIES MARKETPLACE -->
    <section id="sectionMarketplace">
      <!-- Search & Filters -->
      <div class="flex flex-col sm:flex-row gap-4 justify-between items-center mb-6">
        <div class="relative w-full sm:w-80">
          <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
          <input type="text" id="vendorSearchInput" placeholder="Search rice, poultry, onions, oil..." 
            class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 shadow-sm">
        </div>

        <select id="vendorCategoryFilter" class="w-full sm:w-auto px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 shadow-sm focus:outline-none">
          <option value="all">All Categories</option>
          <option value="Grains">Grains & Rice</option>
          <option value="Poultry">Poultry & Meat</option>
          <option value="Produce">Produce & Vegetables</option>
          <option value="Baking">Baking Essentials</option>
          <option value="Oils">Cooking Oils & Seasonings</option>
        </select>
      </div>

      <!-- Marketplace Product Grid -->
      <div id="vendorProductGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Products loaded via vendor.js -->
      </div>
    </section>

    <!-- SECTION: PURCHASE VOLUME & SOURCING SUMMARY -->
    <section id="sectionProcurement">
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
          <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
            <i data-lucide="pie-chart" class="w-4 h-4 text-blue-600"></i> Purchase Volume & Sourcing Summary
          </h3>
          <button onclick="loadVendorAnalytics()" class="text-xs font-semibold text-blue-600 hover:underline flex items-center gap-1">
            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Refresh
          </button>
        </div>

        <div id="vendorKpiGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <!-- Populated via JS -->
        </div>

        <div>
          <h4 class="text-sm font-bold text-slate-800 mb-3">Your Procurement Breakdown</h4>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                  <th class="py-3 px-4">Product</th>
                  <th class="py-3 px-4">Quantity Bought</th>
                  <th class="py-3 px-4">Amount Spent</th>
                  <th class="py-3 px-4">Primary Supplier</th>
                  <th class="py-3 px-4">Last Purchased</th>
                </tr>
              </thead>
              <tbody id="vendorProcurementBody" class="divide-y divide-slate-100 text-sm">
                <tr><td colspan="5" class="py-8 text-center text-slate-400">Loading procurement summary...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION 2: VENDOR ORDERS & DELIVERIES -->
    <section id="sectionOrders" class="hidden">
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
          <h2 class="text-base font-bold text-slate-900">Your Procurement Orders</h2>
          <button onclick="loadVendorData()" class="text-xs font-semibold text-blue-600 hover:underline flex items-center gap-1">
            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Refresh
          </button>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                <th class="py-3.5 px-6">Order ID</th>
                <th class="py-3.5 px-6">Supplier</th>
                <th class="py-3.5 px-6">Items Purchased</th>
                <th class="py-3.5 px-6">Total Amount</th>
                <th class="py-3.5 px-6">Payment</th>
                <th class="py-3.5 px-6">Status</th>
                <th class="py-3.5 px-6">Date</th>
              </tr>
            </thead>
            <tbody id="vendorOrdersTableBody" class="divide-y divide-slate-100 text-sm">
              <!-- Injected via JS -->
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <!-- Checkout Order Modal -->
  <div id="checkoutModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 hidden" role="dialog" aria-modal="true">
    <div class="checkout-modal-panel bg-white rounded-2xl max-w-lg w-full p-8 shadow-2xl border border-slate-100">
      <div class="flex items-start justify-between gap-4 mb-5">
        <div>
          <p class="checkout-kicker">Wholesale Checkout</p>
          <h3 id="modalProdName" class="text-base font-bold text-slate-900 mt-1"></h3>
          <p class="text-sm text-slate-500 mt-1">Sold by <span id="modalSupplierName" class="font-semibold text-slate-700"></span></p>
        </div>
        <button type="button" onclick="closeCheckoutModal()" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <div class="checkout-product-card flex gap-4 p-4 rounded-xl mb-5">
        <div class="w-16 h-16 rounded-lg bg-white overflow-hidden flex items-center justify-center shrink-0 border border-slate-200/60">
          <img id="modalProductImage" src="" alt="" class="w-full h-full object-cover hidden">
          <i id="modalProductPlaceholder" data-lucide="package" class="w-7 h-7 text-slate-300"></i>
        </div>
        <div class="min-w-0 flex-1">
          <span id="modalCategory" class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-md bg-blue-100 text-blue-700"></span>
          <div class="flex items-end justify-between gap-3 mt-1.5">
            <div><p class="text-xs text-slate-500">Unit Price</p><p id="modalUnitPrice" class="text-base font-black text-slate-900"></p></div>
            <p id="modalStock" class="text-xs font-bold text-emerald-600 text-right"></p>
          </div>
        </div>
      </div>

      <form onsubmit="handleCheckoutSubmit(event)" class="checkout-form">
        <input type="hidden" id="modalProdId">
        <input type="hidden" id="modalRawPrice">
        <input type="hidden" id="modalStockQuantity">

        <div class="checkout-section-heading"><span>1</span><div><strong>Order details</strong><small>Choose how many units you need.</small></div></div>
        <div class="checkout-field">
          <label for="modalQuantity" class="block text-xs font-bold text-slate-700 mb-1.5">Quantity to purchase</label>
          <div class="flex items-center gap-2">
            <button type="button" onclick="updateQuantity(-1)" class="w-11 h-11 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 font-bold text-lg">-</button>
            <input type="number" id="modalQuantity" min="1" value="1" required oninput="calculateTotal()" class="w-full h-11 text-center bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            <button type="button" onclick="updateQuantity(1)" class="w-11 h-11 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-bold text-lg">+</button>
          </div>
          <p class="checkout-helper">Available stock: <span id="modalStockHelper">0 units</span></p>
          <p id="quantityError" class="hidden text-xs text-red-600 mt-1.5">Quantity must be between 1 and available stock.</p>
        </div>

        <div class="checkout-section-heading"><span>2</span><div><strong>Payment method</strong><small>Your order will be collected on delivery.</small></div></div>
        <div class="checkout-payment flex items-center justify-between gap-3 p-3 rounded-xl border border-blue-100 bg-blue-50">
          <div><p class="text-xs font-bold text-blue-900">Payment Method</p><p class="text-xs font-semibold text-blue-700 mt-0.5">Cash on Delivery (COD)</p></div>
          <i data-lucide="banknote" class="w-5 h-5 text-blue-600"></i>
        </div>

        <div class="checkout-section-heading"><span>3</span><div><strong>Delivery details</strong><small>Tell the supplier where to send your order.</small></div></div>
        <div class="checkout-field">
          <label for="deliveryAddress" class="block text-xs font-bold text-slate-700 mb-1.5">Delivery address</label>
          <textarea id="deliveryAddress" rows="2" required maxlength="1000" placeholder="Enter complete stall/restaurant delivery address" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"></textarea>
        </div>

        <div class="checkout-field">
          <label for="deliveryNotes" class="block text-xs font-bold text-slate-700 mb-1.5">Special instructions <span class="font-normal text-slate-400">(optional)</span></label>
          <textarea id="deliveryNotes" rows="2" maxlength="1000" placeholder="Receiving contact person, preferred hours, etc." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"></textarea>
        </div>

        <div class="checkout-section-heading"><span>4</span><div><strong>Payment summary</strong><small>Review your total before confirming.</small></div></div>
        <div class="checkout-total pt-4 border-t border-slate-100 space-y-1.5 text-sm">
          <div class="flex justify-between text-slate-500"><span>Subtotal</span><span id="modalSubtotal">₱0.00</span></div>
          <div class="flex justify-between text-slate-500"><span>Wholesale Delivery Fee</span><span id="modalDeliveryFee">₱150.00</span></div>
          <div class="flex justify-between items-center pt-2"><span class="font-bold text-slate-700">Grand Total</span><span id="modalTotalPayable" class="text-xl font-black text-blue-600">₱0.00</span></div>
        </div>

        <div class="checkout-actions flex gap-3 pt-2">
          <button type="button" onclick="closeCheckoutModal()" class="flex-1 py-3 border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition">Cancel</button>
          <button id="checkoutSubmitButton" type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transition flex items-center justify-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i><span>Confirm Checkout</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <script src="assets/js/api.js"></script>
  <script src="assets/js/vendor.js?v=3"></script>
  <script>
    const escapeAnalyticsHtml = value => String(value ?? '').replace(/[&<>"']/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[character]));

    async function loadVendorAnalytics() {
      try {
        const res = await API.get('api/analytics.php');
        if (!res || !res.vendor) return;

        const { top, items } = res.vendor;
        const kpi = document.getElementById('vendorKpiGrid');
        if (!kpi) return;

        kpi.innerHTML = `
          <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
              <i data-lucide="package" class="w-5 h-5"></i>
            </div>
            <div>
              <div class="text-[11px] text-slate-400 font-bold uppercase">Total Units Purchased</div>
              <div class="text-lg font-black text-slate-900">${Number(top.totalUnitsPurchased || 0)}</div>
            </div>
          </div>
          <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
              <i data-lucide="dollar-sign" class="w-5 h-5"></i>
            </div>
            <div>
              <div class="text-[11px] text-slate-400 font-bold uppercase">Total Procurement Spend</div>
              <div class="text-lg font-black text-slate-900">₱${Number(top.totalProcurementSpend || 0).toLocaleString('en-US', {minimumFractionDigits:2})}</div>
            </div>
          </div>
          <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
              <i data-lucide="truck" class="w-5 h-5"></i>
            </div>
            <div>
              <div class="text-[11px] text-slate-400 font-bold uppercase">Completed Deliveries</div>
              <div class="text-lg font-black text-slate-900">${Number(top.totalCompletedDeliveries || 0)}</div>
            </div>
          </div>
          <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
              <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <div>
              <div class="text-[11px] text-slate-400 font-bold uppercase">Active Suppliers</div>
              <div class="text-lg font-black text-slate-900">${Number(top.activeSuppliers || 0)}</div>
            </div>
          </div>
        `;

        const tbody = document.getElementById('vendorProcurementBody');
        if (!tbody) return;

        if (!items || !items.length) {
          tbody.innerHTML = `<tr><td colspan="5" class="py-8 text-center text-slate-400">No purchase history recorded yet.</td></tr>`;
        } else {
          tbody.innerHTML = items.map(it => `
            <tr>
              <td class="py-3 px-4">
                <div class="font-bold text-slate-900">${escapeAnalyticsHtml(it.name)}</div>
                <div class="text-xs text-slate-400">${escapeAnalyticsHtml(it.category || '')}</div>
              </td>
              <td class="py-3 px-4 font-semibold">${Number(it.totalQuantityPurchased || 0)}</td>
              <td class="py-3 px-4 font-black">₱${Number(it.totalSpent || 0).toLocaleString('en-US', {minimumFractionDigits:2})}</td>
              <td class="py-3 px-4">${escapeAnalyticsHtml(it.supplierName || '')}</td>
              <td class="py-3 px-4 text-xs text-slate-500">${it.lastPurchasedAt ? new Date(it.lastPurchasedAt).toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'}) : '-'}</td>
            </tr>
          `).join('');
        }

        lucide.createIcons();
      } catch (e) {
        console.error('Vendor analytics load error:', e);
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      loadVendorAnalytics();
      lucide.createIcons();
    });
  </script>
</body>
</html>