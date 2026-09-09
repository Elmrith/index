// assets/js/vendor.js
let productsData = [];

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>'"]/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[character]));
}

function safeImageUrl(value) {
    const imageUrl = String(value ?? '');
    return /^(https:\/\/|\/|assets\/)/i.test(imageUrl) ? imageUrl : '';
}

document.addEventListener('DOMContentLoaded', () => {
    loadVendorData();

    // Live search listener
    document.getElementById('vendorSearchInput')?.addEventListener('input', filterProducts);
    document.getElementById('vendorCategoryFilter')?.addEventListener('change', filterProducts);
});

// Load all vendor overview stats, products, and order history
async function loadVendorData() {
    try {
        const totalOrdersEl = document.getElementById('statTotalOrders');
        const pendingOrdersEl = document.getElementById('statPendingOrders');
        const totalSpendEl = document.getElementById('statTotalSpend');
        if (!totalOrdersEl || !pendingOrdersEl || !totalSpendEl) return;

        const stats = await API.get('api/vendor.php?action=summary');
        totalOrdersEl.textContent = String(stats?.totalOrders ?? 0);
        pendingOrdersEl.textContent = String(stats?.pendingOrders ?? 0);
        totalSpendEl.textContent = '₱' + Number(stats?.totalSpend ?? 0).toLocaleString('en-US', { minimumFractionDigits: 2 });

        productsData = await API.get('api/products.php');
        renderVendorProducts(Array.isArray(productsData) ? productsData : []);
        openRequestedProduct();

        const orders = await API.get('api/vendor.php?action=orders');
        renderVendorOrders(Array.isArray(orders) ? orders : []);

        lucide.createIcons();
    } catch (err) {
        console.error('Failed to load vendor data:', err);
    }
}

function openRequestedProduct() {
    const productId = new URLSearchParams(window.location.search).get('product');
    if (!productId) return;

    const product = productsData.find(item => String(item.id) === productId);
    if (product && Number(product.stockQuantity) > 0) {
        openCheckoutModal(product.id, product.name, product.price, product.stockQuantity, product.supplierId, product.supplierBusinessName || product.supplierName || 'Verified Supplier', product.imageUrl || '', product.category || 'Food Supplies');
    }
}

// Render Products Grid
function renderVendorProducts(products) {
    const grid = document.getElementById('vendorProductGrid');
    if (!products.length) {
        grid.innerHTML = `
            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-slate-200">
                <i data-lucide="inbox" class="w-12 h-12 text-slate-300 mx-auto mb-2"></i>
                <p class="text-sm text-slate-500 font-medium">No wholesale supplies currently available.</p>
            </div>
        `;
        lucide.createIcons();
        return;
    }

    grid.innerHTML = products.map(p => `
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col group">
            <div class="h-44 bg-slate-100 relative overflow-hidden flex items-center justify-center">
                ${safeImageUrl(p.imageUrl) ? `<img src="${escapeHtml(safeImageUrl(p.imageUrl))}" alt="${escapeHtml(p.name)}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">` 
                             : `<i data-lucide="package" class="w-12 h-12 text-slate-300"></i>`}
                <span class="absolute top-3 left-3 px-2.5 py-1 text-[10px] font-bold rounded-lg uppercase tracking-wider ${
                  p.demandStatus === 'high' ? 'bg-red-500/90 text-white' : 'bg-amber-500/90 text-white'
                }">
                  ${escapeHtml(p.demandStatus || 'medium')} Demand
                </span>
            </div>

            <div class="p-5 flex-1 flex flex-col">
                <div class="text-xs font-semibold text-blue-600 mb-1">${escapeHtml(p.category || 'Food Supplies')}</div>
                <h3 class="font-bold text-slate-900 text-base leading-snug mb-1">${escapeHtml(p.name)}</h3>
                <p class="text-xs text-slate-500 flex items-center gap-1 mb-2">
                    <i data-lucide="store" class="w-3.5 h-3.5 text-slate-400"></i>
                    ${escapeHtml(p.supplierBusinessName || p.supplierName || 'Verified Supplier')}
                </p>
                <div class="text-xs text-slate-500 mb-3">
                    Available: <strong class="${p.stockQuantity < 10 ? 'text-red-500' : 'text-slate-700'}">${Number(p.stockQuantity)} units</strong>
                </div>

                <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Wholesale Price</div>
                        <div class="text-lg font-black text-slate-900">₱${Number(p.price).toLocaleString('en-US', { minimumFractionDigits: 2 })}</div>
                    </div>
                    <button data-product-id="${escapeHtml(p.id)}" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5 ${p.stockQuantity <= 0 ? 'opacity-50 pointer-events-none' : ''}">
                        <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i> ${p.stockQuantity > 0 ? 'Order' : 'Out of Stock'}
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    grid.querySelectorAll('[data-product-id]').forEach(button => {
        button.addEventListener('click', () => {
            const product = products.find(item => String(item.id) === button.dataset.productId);
            if (product && Number(product.stockQuantity) > 0) {
                openCheckoutModal(product.id, product.name, product.price, product.stockQuantity, product.supplierId, product.supplierBusinessName || product.supplierName || 'Verified Supplier', product.imageUrl || '', product.category || 'Food Supplies');
            }
        });
    });
    lucide.createIcons();
}

// Render Orders Table
function renderVendorOrders(orders) {
    const tbody = document.getElementById('vendorOrdersTableBody');
    if (!orders.length) {
        tbody.innerHTML = `<tr><td colspan="7" class="py-8 text-center text-slate-400">No orders placed yet. Browse the marketplace to place an order!</td></tr>`;
        return;
    }

    tbody.innerHTML = orders.map(o => `
        <tr class="hover:bg-slate-50/50 transition">
            <td class="py-3.5 px-6 font-bold text-slate-900">#${o.supplierOrderNumber}</td>
            <td class="py-3.5 px-6 font-medium text-slate-700">${escapeHtml(o.supplierName || 'Supplier')}</td>
            <td class="py-3.5 px-6 text-slate-600">
                ${(o.items || []).map(item => `<div class="text-xs font-semibold">${Number(item.quantity)}x ${escapeHtml(item.name)}</div>`).join('')}
            </td>
            <td class="py-3.5 px-6 font-bold text-slate-900">₱${Number(o.totalAmount).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
            <td class="py-3.5 px-6 text-xs text-slate-500 font-medium">${o.paymentMethod || 'COD'}</td>
            <td class="py-3.5 px-6">
                <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg uppercase tracking-wider ${
                    o.status === 'delivered' ? 'bg-emerald-100 text-emerald-700' :
                    o.status === 'confirmed' ? 'bg-blue-100 text-blue-700' :
                    o.status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'
                }">
                    ${escapeHtml(o.status)}
                </span>
            </td>
            <td class="py-3.5 px-6 text-xs text-slate-400 font-medium">${new Date(o.createdAt).toLocaleDateString()}</td>
        </tr>
    `).join('');
}

// Search and Category Filter
function filterProducts() {
    const query = document.getElementById('vendorSearchInput').value.toLowerCase();
    const cat = document.getElementById('vendorCategoryFilter').value;

    const filtered = productsData.filter(p => {
        const matchesQuery = p.name.toLowerCase().includes(query) || (p.category && p.category.toLowerCase().includes(query));
        const matchesCat = (cat === 'all') || (p.category === cat);
        return matchesQuery && matchesCat;
    });

    renderVendorProducts(filtered);
}

// Switch between Marketplace and Order tabs
function switchVendorTab(tab) {
    const secMarket = document.getElementById('sectionMarketplace');
    const secOrders = document.getElementById('sectionOrders');
    const btnMarket = document.getElementById('tabMarketplaceBtn');
    const btnOrders = document.getElementById('tabOrdersBtn');

    if (tab === 'marketplace') {
        secMarket.classList.remove('hidden');
        secOrders.classList.add('hidden');
        btnMarket.className = "py-3 px-5 text-sm font-bold border-b-2 border-blue-600 text-blue-600 flex items-center gap-2";
        btnOrders.className = "py-3 px-5 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-900 flex items-center gap-2";
    } else {
        secMarket.classList.add('hidden');
        secOrders.classList.remove('hidden');
        btnOrders.className = "py-3 px-5 text-sm font-bold border-b-2 border-blue-600 text-blue-600 flex items-center gap-2";
        btnMarket.className = "py-3 px-5 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-900 flex items-center gap-2";
    }
}

let checkoutProduct = null;

function formatPeso(amount) {
    return '₱' + Number(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function openCheckoutModal(productId, productName, unitPrice, stockQuantity, supplierId, supplierName, imageUrl = '', category = 'Food Supplies') {
    checkoutProduct = { productId, productName, unitPrice: Number(unitPrice), stockQuantity: Number(stockQuantity), supplierId, supplierName };
    document.getElementById('modalProdId').value = productId;
    document.getElementById('modalRawPrice').value = checkoutProduct.unitPrice;
    document.getElementById('modalStockQuantity').value = checkoutProduct.stockQuantity;
    document.getElementById('modalProdName').textContent = productName;
    document.getElementById('modalSupplierName').textContent = supplierName;
    document.getElementById('modalCategory').textContent = category;
    document.getElementById('modalUnitPrice').textContent = formatPeso(checkoutProduct.unitPrice);
    document.getElementById('modalStock').textContent = `${checkoutProduct.stockQuantity} units available`;
    document.getElementById('modalStockHelper').textContent = `${checkoutProduct.stockQuantity} units`;
    document.getElementById('modalQuantity').max = checkoutProduct.stockQuantity;
    document.getElementById('modalQuantity').value = 1;
    document.getElementById('quantityError').classList.add('hidden');

    const image = document.getElementById('modalProductImage');
    const placeholder = document.getElementById('modalProductPlaceholder');
    image.alt = productName;
    image.src = imageUrl;
    image.classList.toggle('hidden', !imageUrl);
    placeholder?.classList.toggle('hidden', Boolean(imageUrl));
    calculateTotal();
    const checkoutModal = document.getElementById('checkoutModal');
    const checkoutPanel = checkoutModal.querySelector('.checkout-modal-panel');
    checkoutModal.classList.remove('hidden');
    checkoutPanel.scrollTop = 0;
    lucide.createIcons();
    document.getElementById('modalQuantity').focus({ preventScroll: true });
}

function closeCheckoutModal() {
    document.getElementById('checkoutModal').classList.add('hidden');
    document.getElementById('checkoutSubmitButton').disabled = false;
}

function updateQuantity(change) {
    const input = document.getElementById('modalQuantity');
    const max = Number(document.getElementById('modalStockQuantity').value) || 1;
    const current = Number.parseInt(input.value, 10) || 1;
    input.value = Math.min(max, Math.max(1, current + change));
    calculateTotal();
}

function calculateTotal() {
    const price = Number(document.getElementById('modalRawPrice').value) || 0;
    const max = Number(document.getElementById('modalStockQuantity').value) || 1;
    const input = document.getElementById('modalQuantity');
    const quantity = Number.parseInt(input.value, 10) || 1;
    const validQuantity = Math.min(max, Math.max(1, quantity));
    const subtotal = price * validQuantity;
    const deliveryFee = subtotal >= 5000 ? 0 : 150;
    input.value = validQuantity;
    document.getElementById('quantityError').classList.toggle('hidden', quantity >= 1 && quantity <= max);
    document.getElementById('modalSubtotal').textContent = formatPeso(subtotal);
    document.getElementById('modalDeliveryFee').textContent = deliveryFee ? formatPeso(deliveryFee) : 'Free';
    document.getElementById('modalTotalPayable').textContent = formatPeso(subtotal + deliveryFee);
    return { quantity: validQuantity, subtotal, deliveryFee, total: subtotal + deliveryFee };
}

function showCheckoutToast(order) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-5 right-5 z-[60] w-[min(22rem,calc(100vw-2rem))] rounded-2xl bg-emerald-600 text-white p-4 shadow-2xl';
    toast.innerHTML = '<div class="flex items-start gap-3"><i data-lucide="circle-check" class="w-5 h-5 shrink-0 mt-0.5"></i><div><p class="font-bold">Order confirmed</p><p id="checkoutReceipt" class="text-sm text-emerald-50 mt-1"></p><a href="orders.php" class="inline-block mt-2 text-sm font-bold underline">View My Orders</a></div></div>';
    toast.querySelector('#checkoutReceipt').textContent = `Receipt #${Number(order.supplierOrderNumber)} · ${formatPeso(order.totalAmount)}`;
    document.body.appendChild(toast);
    lucide.createIcons();
    setTimeout(() => { window.location.href = 'orders.php'; }, 1400);
}

async function handleCheckoutSubmit(event) {
    event.preventDefault();
    const button = document.getElementById('checkoutSubmitButton');
    const summary = calculateTotal();
    const address = document.getElementById('deliveryAddress').value.trim();
    if (!checkoutProduct || !address || summary.quantity < 1 || summary.quantity > checkoutProduct.stockQuantity) return;

    button.disabled = true;
    button.innerHTML = '<i data-lucide="loader-circle" class="w-4 h-4 animate-spin"></i><span>Processing order...</span>';
    lucide.createIcons();
    try {
        const order = await API.post('api/orders.php', {
            productId: checkoutProduct.productId,
            quantity: summary.quantity,
            paymentMethod: 'Cash on Delivery',
            deliveryAddress: address,
            deliveryNotes: document.getElementById('deliveryNotes').value.trim()
        });
        closeCheckoutModal();
        showCheckoutToast(order);
    } catch (err) {
        button.disabled = false;
        button.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4"></i><span>Confirm Checkout</span>';
        lucide.createIcons();
        alert(err.message || 'Unable to place the order.');
    }
}