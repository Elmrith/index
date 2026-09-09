<?php
// supplier.php - Polished Supplier Dashboard & Analytics
require_once __DIR__ . '/includes/auth_check.php';
checkAuth('supplier');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Supplier Dashboard - VendLink</title>
  <link rel="stylesheet" href="assets/css/style.css?v=11">
  <script src="assets/js/lucide.min.js?v=3"></script>
</head>
<body class="role-supplier min-h-screen bg-slate-50 text-slate-900 font-sans antialiased">
  <?php include __DIR__ . '/includes/header.php'; ?>

  <!-- Single Centered Main Container -->
  <main class="page-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- Dashboard Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
          </div>
          Supplier Command Center
        </h1>
        <p class="text-sm text-slate-500 mt-1">Manage wholesale inventory, publish supplies, and process incoming vendor orders.</p>
      </div>

      <div>
        <button onclick="openAddModal()" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/20 transition flex items-center gap-2">
          <i data-lucide="plus-circle" class="w-4 h-4"></i> Add New Product
        </button>
      </div>
    </div>

    <!-- 2-Column Split Layout: Orders & Inventory -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <!-- Left Column: Incoming Orders -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <i data-lucide="inbox" class="w-4 h-4 text-blue-600"></i>
            <h2 class="text-base font-bold text-slate-900">Incoming Vendor Orders</h2>
          </div>
          <button onclick="loadSupplierData()" class="text-xs font-semibold text-blue-600 hover:underline flex items-center gap-1">
            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Refresh
          </button>
        </div>
        
        <div id="supplierOrdersList" class="p-5 space-y-3 flex-1 overflow-y-auto max-h-[500px]">
          <p class="text-center text-slate-400 text-xs py-8">Loading incoming orders...</p>
        </div>
      </div>

      <!-- Right Column: My Inventory Catalog -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <i data-lucide="boxes" class="w-4 h-4 text-blue-600"></i>
            <h2 class="text-base font-bold text-slate-900">My Inventory Catalog</h2>
          </div>
          <span id="inventoryCountBadge" class="text-xs font-semibold px-2 py-0.5 bg-slate-100 text-slate-600 rounded-lg">0 items</span>
        </div>

        <div id="supplierProductList" class="p-5 space-y-3 flex-1 overflow-y-auto max-h-[500px]">
          <p class="text-center text-slate-400 text-xs py-8">Loading inventory...</p>
        </div>
      </div>

    </div>

    <!-- SALES & PRODUCT PERFORMANCE SECTION -->
    <section class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
      <div class="flex items-center justify-between mb-5">
        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
          <i data-lucide="bar-chart-2" class="w-4 h-4 text-blue-600"></i> Sales & Product Performance
        </h3>
        <button onclick="loadSupplierAnalytics()" class="text-xs font-semibold text-blue-600 hover:underline flex items-center gap-1">
          <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Refresh
        </button>
      </div>

      <!-- KPI Grid -->
      <div id="supplierKpiGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Populated via JS -->
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
          <h4 class="text-sm font-bold text-slate-800 mb-3">Product Sales Summary</h4>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                  <th class="py-3 px-4">Product</th>
                  <th class="py-3 px-4">Units Sold</th>
                  <th class="py-3 px-4">Revenue</th>
                  <th class="py-3 px-4">Buyers</th>
                  <th class="py-3 px-4">Stock</th>
                </tr>
              </thead>
              <tbody id="productPerformanceBody" class="divide-y divide-slate-100 text-sm">
                <tr><td colspan="5" class="py-8 text-center text-slate-400">Loading product performance...</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <div>
          <h4 class="text-sm font-bold text-slate-800 mb-3">Top Vendor Buyers</h4>
          <div id="topBuyersList" class="space-y-2 text-sm">
            <p class="text-slate-400">Loading top buyers...</p>
          </div>
        </div>
      </div>
    </section>

  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <!-- ADD PRODUCT MODAL (Centered Popup Overlay) -->
  <div id="addModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
      
      <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
            <i data-lucide="package-plus" class="w-4 h-4"></i>
          </div>
          <h3 class="text-base font-bold text-slate-900">List New Supply Product</h3>
        </div>
        <button type="button" onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600 transition">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <form onsubmit="handleAddProduct(event)" class="space-y-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Item Name</label>
          <input type="text" id="prodName" required placeholder="e.g. Organic Rice (25kg Sack)"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Wholesale Price (₱)</label>
            <input type="number" step="0.01" id="prodPrice" required placeholder="1250.00"
              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Stock Quantity</label>
            <input type="number" id="prodStock" required placeholder="50"
              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Category</label>
          <select id="prodCategory" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:bg-white focus:outline-none focus:border-blue-600">
            <option value="Grains">Grains & Rice</option>
            <option value="Poultry">Poultry & Meat</option>
            <option value="Produce">Produce & Vegetables</option>
            <option value="Baking">Baking Essentials</option>
            <option value="Oils">Cooking Oils & Seasonings</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Product Image</label>
          <div id="productDropzone" class="relative border-2 border-dashed border-slate-200 rounded-xl bg-slate-50 hover:bg-blue-50/50 hover:border-blue-300 transition cursor-pointer p-4 text-center">
            <input id="prodImageFile" type="file" accept="image/jpeg,image/png,image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
            <div id="productImageEmpty" class="py-3"><i data-lucide="image-plus" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i><p class="text-xs font-bold text-slate-600">Drop a product photo here or click to browse</p><p class="text-[11px] text-slate-400 mt-1">JPEG, PNG, or WebP up to 5MB</p></div>
            <div id="productImagePreviewWrap" class="hidden"><img id="productImagePreview" src="" alt="Selected product preview" class="mx-auto h-28 w-full object-cover rounded-lg"><p id="productImageName" class="text-xs font-semibold text-slate-600 mt-2 truncate"></p><button id="clearProductImage" type="button" class="relative z-10 mt-2 text-xs font-bold text-red-600 hover:underline">Remove image</button></div>
          </div>
          <p id="productUploadStatus" class="hidden text-xs mt-1.5"></p>
          <label class="block mt-3"><span class="block text-xs font-bold text-slate-700 mb-1">Or use an image URL</span><input id="prodImageUrl" type="url" placeholder="https://example.com/product.jpg" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"></label>
        </div>

        <div class="flex gap-3 pt-3">
          <button type="button" onclick="closeAddModal()" class="flex-1 py-2.5 border border-slate-200 rounded-xl font-bold text-xs text-slate-600 hover:bg-slate-50 transition">
            Cancel
          </button>
          <button type="submit" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs shadow-md shadow-blue-500/20 transition">
            Publish Product
          </button>
        </div>
      </form>
    </div>
  </div>

  <script src="assets/js/api.js"></script>
  <script>
    const escapeHtml = value => String(value ?? '').replace(/[&<>'"]/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[character]));
    const safeProductImage = value => /^(https:\/\/|uploads\/products\/prod_[A-Za-z0-9_-]+\.(jpg|png|webp))$/i.test(String(value || '')) ? String(value) : '';
    let selectedProductImageFile = null;

    function setProductUploadStatus(message, type = 'error') {
      const status = document.getElementById('productUploadStatus');
      status.textContent = message;
      status.className = `text-xs mt-1.5 ${type === 'success' ? 'text-emerald-600' : 'text-red-600'}`;
      status.classList.remove('hidden');
    }

    function showProductImagePreview(file) {
      selectedProductImageFile = file;
      const preview = document.getElementById('productImagePreview');
      const empty = document.getElementById('productImageEmpty');
      const wrap = document.getElementById('productImagePreviewWrap');
      preview.src = URL.createObjectURL(file);
      document.getElementById('productImageName').textContent = file.name;
      empty.classList.add('hidden');
      wrap.classList.remove('hidden');
      setProductUploadStatus('Image ready to upload.', 'success');
    }

    function handleProductImage(file) {
      if (!file) return;
      if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
        setProductUploadStatus('Choose a JPEG, PNG, or WebP image.');
        return;
      }
      if (file.size > 5 * 1024 * 1024) {
        setProductUploadStatus('The image must be 5MB or smaller.');
        return;
      }
      showProductImagePreview(file);
    }

    async function uploadProductImage(file) {
      for (let attempt = 0; attempt < 2; attempt += 1) {
        const formData = new FormData();
        formData.append('image', file);
        const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const response = await fetch('api/upload.php', { method: 'POST', credentials: 'same-origin', headers: { 'X-CSRF-Token': token }, body: formData });
        const result = await response.json();
        if (response.ok && result.url) return result.url;
        if (attempt === 0 && response.status === 403 && result.error === 'Invalid security token.') {
          const tokenResponse = await fetch('api/auth.php?action=csrf', { credentials: 'same-origin' });
          const tokenResult = await tokenResponse.json();
          if (tokenResponse.ok && tokenResult.csrfToken) {
            document.querySelector('meta[name="csrf-token"]')?.setAttribute('content', tokenResult.csrfToken);
            continue;
          }
        }
        throw new Error(result.error || 'Image upload failed.');
      }
      throw new Error('Image upload failed.');
    }

    function clearProductImage() {
      selectedProductImageFile = null;
      document.getElementById('prodImageFile').value = '';
      document.getElementById('productImagePreview').removeAttribute('src');
      document.getElementById('productImagePreviewWrap').classList.add('hidden');
      document.getElementById('productImageEmpty').classList.remove('hidden');
      document.getElementById('productUploadStatus').classList.add('hidden');
    }

    function resetAddProductForm() {
      document.querySelector('#addModal form').reset();
      clearProductImage();
    }

    async function loadSupplierAnalytics() {
      try {
        const res = await API.get('api/analytics.php');
        if (!res.supplier) return;
        const { top, products, leaderboard } = res.supplier;

        const kpiGrid = document.getElementById('supplierKpiGrid');
        kpiGrid.innerHTML = `
          <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
              <i data-lucide="box" class="w-5 h-5"></i>
            </div>
            <div>
              <div class="text-[11px] text-slate-400 font-bold uppercase">Total Units Sold</div>
              <div class="text-lg font-black text-slate-900">${Number(top.totalUnitsSold || 0)}</div>
            </div>
          </div>
          <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
              <i data-lucide="dollar-sign" class="w-5 h-5"></i>
            </div>
            <div>
              <div class="text-[11px] text-slate-400 font-bold uppercase">Total Gross Revenue</div>
              <div class="text-lg font-black text-slate-900">₱${Number(top.totalRevenue || 0).toLocaleString('en-US', {minimumFractionDigits:2})}</div>
            </div>
          </div>
          <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
              <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <div>
              <div class="text-[11px] text-slate-400 font-bold uppercase">Unique Buyers</div>
              <div class="text-lg font-black text-slate-900">${Number(top.totalUniqueBuyers || 0)}</div>
            </div>
          </div>
          <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-slate-50 text-slate-700 flex items-center justify-center font-bold">
              <i data-lucide="activity" class="w-5 h-5"></i>
            </div>
            <div>
              <div class="text-[11px] text-slate-400 font-bold uppercase">Average Order Size</div>
              <div class="text-lg font-black text-slate-900">${(top.totalOrders > 0 ? (Math.round((top.totalUnitsSold / top.totalOrders) * 100)/100) : 0)}</div>
            </div>
          </div>
        `;

        const tbody = document.getElementById('productPerformanceBody');
        if (!products || !products.length) {
          tbody.innerHTML = `<tr><td colspan="5" class="py-8 text-center text-slate-400">No sales data yet.</td></tr>`;
        } else {
          tbody.innerHTML = products.map(p => `
            <tr>
              <td class="py-3 px-4">
                <div class="font-bold text-slate-900">${escapeHtml(p.name)}</div>
                <div class="text-xs text-slate-400">${escapeHtml(p.category || '')}</div>
              </td>
              <td class="py-3 px-4 font-semibold">${Number(p.unitsSold || 0)}</td>
              <td class="py-3 px-4 font-black">₱${Number(p.totalEarned || 0).toLocaleString('en-US', {minimumFractionDigits:2})}</td>
              <td class="py-3 px-4">${Number(p.uniqueBuyersCount || 0)}</td>
              <td class="py-3 px-4 font-bold ${p.stockQuantity < 10 ? 'text-red-600' : 'text-slate-800'}">${Number(p.stockQuantity)}</td>
            </tr>
          `).join('');
        }

        const lb = document.getElementById('topBuyersList');
        if (!leaderboard || !leaderboard.length) {
          lb.innerHTML = `<p class="text-slate-400 text-xs py-4 text-center">No buyer data available yet.</p>`;
        } else {
          lb.innerHTML = leaderboard.map(v => `
            <div class="p-3 rounded-xl border border-slate-100 bg-slate-50/50 flex items-center justify-between">
              <div>
                <div class="font-bold text-slate-900 text-xs">${escapeHtml(v.vendorBusinessName || v.vendorName)}</div>
                <div class="text-[11px] text-slate-500">${escapeHtml(v.vendorName || '')}</div>
              </div>
              <div class="text-right">
                <div class="text-[11px] text-slate-400">Orders: <span class="font-bold text-slate-900">${v.ordersCount}</span></div>
                <div class="text-xs font-black text-slate-900">₱${Number(v.totalSpent || 0).toLocaleString('en-US', {minimumFractionDigits:2})}</div>
              </div>
            </div>
          `).join('');
        }

        lucide.createIcons();
      } catch (e) {
        console.error('Analytics load error:', e);
      }
    }

    async function loadSupplierData() {
      try {
        const orders = await API.get('api/orders.php');
        const orderContainer = document.getElementById('supplierOrdersList');
        if (!orders.length) {
          orderContainer.innerHTML = `
            <div class="py-12 text-center text-slate-400">
              <i data-lucide="clipboard-list" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
              <p class="text-xs font-medium">No incoming orders received yet.</p>
            </div>
          `;
        } else {
          orderContainer.innerHTML = orders.map(o => `
            <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-slate-200 transition space-y-2.5">
              <div class="flex items-center justify-between">
                <div>
                  <span class="font-black text-slate-900 text-sm">Order #${Number(o.supplierOrderNumber)}</span>
                  <span class="text-xs text-slate-500 font-medium ml-1.5">• ${escapeHtml(o.vendorBusinessName || o.vendorName)}</span>
                </div>
                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-lg tracking-wider ${
                  o.status === 'delivered' ? 'bg-emerald-100 text-emerald-700' :
                  o.status === 'confirmed' ? 'bg-blue-100 text-blue-700' :
                  o.status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'
                }">${escapeHtml(o.status)}</span>
              </div>
              <div class="text-xs text-slate-600 font-medium">
                ${(o.items || []).map(it => `${Number(it.quantity)}x ${escapeHtml(it.name)}`).join(', ')}
              </div>
              <div class="flex items-center justify-between pt-1 border-t border-slate-200/50">
                <span class="text-xs font-black text-slate-900">₱${Number(o.totalAmount).toLocaleString('en-US', {minimumFractionDigits:2})}</span>
                <div class="flex gap-1.5">
                  ${o.status === 'pending' ? `
                    <button onclick="updateOrderStatus(${Number(o.id)}, 'confirmed')" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] rounded-lg transition">Confirm</button>
                    <button onclick="updateOrderStatus(${Number(o.id)}, 'cancelled')" class="px-2.5 py-1 bg-slate-200 hover:bg-red-50 hover:text-red-600 text-slate-700 font-bold text-[11px] rounded-lg transition">Decline</button>
                  ` : ''}
                  ${o.status === 'confirmed' ? `
                    <button onclick="updateOrderStatus(${Number(o.id)}, 'delivered')" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] rounded-lg transition">Mark Delivered</button>
                  ` : ''}
                </div>
              </div>
            </div>
          `).join('');
        }
      } catch (err) {
        console.error(err);
      }

      try {
        const products = await API.get('api/products.php?supplierId=<?= $_SESSION['user_id'] ?>');
        const prodContainer = document.getElementById('supplierProductList');
        document.getElementById('inventoryCountBadge').textContent = `${products.length} items`;
        
        if (!products.length) {
          prodContainer.innerHTML = `
            <div class="py-12 text-center text-slate-400">
              <i data-lucide="package-open" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
              <p class="text-xs font-medium">No products listed. Click "Add New Product" to start selling.</p>
            </div>
          `;
        } else {
          prodContainer.innerHTML = products.map(p => `
            <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/50 flex items-center justify-between hover:bg-white hover:border-slate-200 transition">
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-12 h-12 rounded-lg bg-white border border-slate-100 overflow-hidden flex items-center justify-center shrink-0">
                  ${safeProductImage(p.imageUrl) ? `<img src="${escapeHtml(safeProductImage(p.imageUrl))}" alt="${escapeHtml(p.name)}" class="w-full h-full object-cover">` : '<i data-lucide="package" class="w-5 h-5 text-slate-300"></i>'}
                </div>
                <div>
                <h4 class="font-bold text-slate-900 text-sm">${escapeHtml(p.name)}</h4>
                <div class="text-xs text-slate-500 mt-0.5">
                  <span class="font-semibold text-blue-600">₱${Number(p.price).toLocaleString('en-US', {minimumFractionDigits:2})}</span> • 
                  Stock: <span class="font-bold ${p.stockQuantity < 10 ? 'text-red-500' : 'text-slate-700'}">${Number(p.stockQuantity)} units</span>
                </div>
                </div>
              </div>
              <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-lg ${p.status === 'approved' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'}">
                ${escapeHtml(p.status)}
              </span>
            </div>
          `).join('');
        }
        lucide.createIcons();
      } catch (err) {
        console.error(err);
      }
    }

    async function updateOrderStatus(orderId, status) {
      try {
        await API.put('api/orders.php', { orderId, status });
        await loadSupplierData();
        await loadSupplierAnalytics();
      } catch (error) {
        setProductUploadStatus(error.message || 'Order status could not be updated.');
      }
    }

    function openAddModal() {
      document.getElementById('addModal').classList.remove('hidden');
      lucide.createIcons();
    }

    function closeAddModal() {
      document.getElementById('addModal').classList.add('hidden');
      resetAddProductForm();
    }

    async function handleAddProduct(e) {
      e.preventDefault();
      const submitButton = e.submitter;
      submitButton.disabled = true;
      submitButton.textContent = 'Publishing...';
      try {
        let imageUrl = document.getElementById('prodImageUrl').value.trim();
        if (selectedProductImageFile) {
          setProductUploadStatus('Uploading image...', 'success');
          imageUrl = await uploadProductImage(selectedProductImageFile);
        }
        await API.post('api/products.php', {
          name: document.getElementById('prodName').value,
          price: parseFloat(document.getElementById('prodPrice').value),
          stockQuantity: parseInt(document.getElementById('prodStock').value),
          category: document.getElementById('prodCategory').value,
          imageUrl
        });
        closeAddModal();
        await loadSupplierData();
        await loadSupplierAnalytics();
      } catch (err) {
        setProductUploadStatus(err.message || 'Product could not be published.');
      } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Publish Product';
      }
    }

    document.addEventListener('DOMContentLoaded', async () => {
      const dropzone = document.getElementById('productDropzone');
      const fileInput = document.getElementById('prodImageFile');
      fileInput.addEventListener('change', event => handleProductImage(event.target.files[0]));
      ['dragenter', 'dragover'].forEach(eventName => dropzone.addEventListener(eventName, event => { event.preventDefault(); dropzone.classList.add('border-blue-500', 'bg-blue-50'); }));
      ['dragleave', 'drop'].forEach(eventName => dropzone.addEventListener(eventName, event => { event.preventDefault(); dropzone.classList.remove('border-blue-500', 'bg-blue-50'); }));
      dropzone.addEventListener('drop', event => handleProductImage(event.dataTransfer.files[0]));
      document.getElementById('clearProductImage').addEventListener('click', event => { event.preventDefault(); event.stopPropagation(); clearProductImage(); });
      await loadSupplierData();
      await loadSupplierAnalytics();
      lucide.createIcons();
    });
  </script>
</body>
</html>