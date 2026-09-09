<?php
// admin.php - System Administration & Control Center
require_once __DIR__ . '/includes/auth_check.php';
checkAuth('admin');
require_once __DIR__ . '/config/database.php';

// Fetch users
$users = $pdo->query("SELECT id, name, email, role, businessName, isActive, createdAt FROM users WHERE role != 'admin' ORDER BY createdAt DESC")->fetchAll();

// Fetch System Stats
$userCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")->fetchColumn();
$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orderTotal = $pdo->query("SELECT COALESCE(SUM(totalAmount), 0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - VendLink</title>
  <link rel="stylesheet" href="assets/css/style.css?v=11">
  <script src="assets/js/lucide.min.js?v=3"></script>
</head>
<body class="role-admin min-h-screen bg-slate-50 text-slate-900 font-sans antialiased">
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main class="page-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
            <i data-lucide="shield-check" class="w-4 h-4"></i>
          </div>
          System Administration
        </h1>
        <p class="text-sm text-slate-500 mt-1">Platform overview, user authorization, and marketplace volume.</p>
      </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
          <i data-lucide="users" class="w-6 h-6"></i>
        </div>
        <div>
          <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Registered Users</div>
          <div class="text-2xl font-black text-slate-900 mt-0.5"><?= number_format($userCount) ?></div>
        </div>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
          <i data-lucide="boxes" class="w-6 h-6"></i>
        </div>
        <div>
          <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Active Products</div>
          <div class="text-2xl font-black text-slate-900 mt-0.5"><?= number_format($productCount) ?></div>
        </div>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
          <i data-lucide="receipt" class="w-6 h-6"></i>
        </div>
        <div>
          <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Platform Volume</div>
          <div class="text-2xl font-black text-slate-900 mt-0.5">₱<?= number_format((float)$orderTotal, 2) ?></div>
        </div>
      </div>
    </div>

    <section class="admin-background-card bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 mb-8" aria-labelledby="backgroundSettingsTitle">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 id="backgroundSettingsTitle" class="text-base font-bold text-slate-900 flex items-center gap-2"><i data-lucide="paintbrush" class="w-4 h-4 text-amber-600"></i> Shared Background</h2>
          <p class="text-sm text-slate-500 mt-1">Choose the background that everyone sees across VendLink.</p>
        </div>
        <div class="flex items-center gap-3">
          <select id="backgroundPreset" class="w-full sm:w-52">
            <option value="default">Cloud Grid</option>
            <option value="ocean">Ocean Blue</option>
            <option value="meadow">Fresh Meadow</option>
            <option value="sunset">Warm Sunset</option>
          </select>
          <button id="saveBackgroundButton" type="button" class="btn-primary px-4 py-2 rounded-lg font-bold text-sm whitespace-nowrap">Save Background</button>
        </div>
      </div>
      <p id="backgroundSaveState" class="hidden text-sm font-semibold mt-3" role="status"></p>
    </section>

    <!-- User Management Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
      <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <h2 class="text-base font-bold text-slate-900">User Authorization & Accounts</h2>
        <span class="text-xs text-slate-400 font-medium"><?= count($users) ?> accounts registered</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-bold">
              <th class="py-3.5 px-6">Name / Business</th>
              <th class="py-3.5 px-6">Email</th>
              <th class="py-3.5 px-6">Role</th>
              <th class="py-3.5 px-6">Status</th>
              <th class="py-3.5 px-6">Registered</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-sm">
            <?php if (empty($users)): ?>
              <tr>
                <td colspan="5" class="py-8 text-center text-slate-400">No registered users found.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($users as $u): ?>
                <tr class="hover:bg-slate-50/50 transition">
                  <td class="py-4 px-6 font-bold text-slate-900">
                    <?= htmlspecialchars($u['name'] ?? 'User') ?>
                    <?php if (!empty($u['businessName'])): ?>
                      <div class="text-xs font-normal text-slate-400"><?= htmlspecialchars($u['businessName']) ?></div>
                    <?php endif; ?>
                  </td>
                  <td class="py-4 px-6 text-slate-600"><?= htmlspecialchars($u['email']) ?></td>
                  <td class="py-4 px-6">
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg uppercase tracking-wider <?= $u['role'] === 'supplier' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' ?>">
                      <?= htmlspecialchars($u['role']) ?>
                    </span>
                  </td>
                  <td class="py-4 px-6">
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg uppercase tracking-wider <?= $u['isActive'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>">
                      <?= $u['isActive'] ? 'Authorized' : 'Suspended' ?>
                    </span>
                  </td>
                  <td class="py-4 px-6 text-xs text-slate-400">
                    <?= date('M d, Y', strtotime($u['createdAt'])) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script src="assets/js/api.js"></script>
  <script>
    lucide.createIcons();
    const backgroundPreset = document.getElementById('backgroundPreset');
    const backgroundSaveState = document.getElementById('backgroundSaveState');
    API.get('api/settings.php').then(settings => {
      backgroundPreset.value = settings.siteBackground || 'default';
    }).catch(() => {});
    document.getElementById('saveBackgroundButton').addEventListener('click', async () => {
      const button = document.getElementById('saveBackgroundButton');
      button.disabled = true;
      backgroundSaveState.className = 'text-sm font-semibold mt-3 text-slate-500';
      backgroundSaveState.textContent = 'Saving background...';
      try {
        await API.put('api/settings.php', { siteBackground: backgroundPreset.value });
        document.body.dataset.siteBackground = backgroundPreset.value;
        backgroundSaveState.className = 'text-sm font-semibold mt-3 text-emerald-700';
        backgroundSaveState.textContent = 'Background updated for all users.';
      } catch (err) {
        backgroundSaveState.className = 'text-sm font-semibold mt-3 text-red-700';
        backgroundSaveState.textContent = err.message || 'Unable to save background.';
      } finally {
        button.disabled = false;
      }
    });
  </script>
</body>
</html>