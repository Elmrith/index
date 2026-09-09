<?php
require_once __DIR__ . '/includes/auth_check.php';
checkAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile & Account Settings - VendLink</title>
  <link rel="stylesheet" href="assets/css/style.css?v=11">
  <script src="assets/js/lucide.min.js?v=3"></script>
  <style>
    .field-label { display: block; margin-bottom: .375rem; font-size: .75rem; line-height: 1rem; font-weight: 700; color: #334155; }
    .field-wrap { position: relative; display: block; }
    .field-icon { position: absolute; left: .875rem; top: .75rem; width: 1rem; height: 1rem; color: #94a3b8; pointer-events: none; }
    .profile-input { width: 100%; border: 1px solid #e2e8f0; border-radius: .75rem; background: #f8fafc; padding: .6875rem .875rem .6875rem 2.75rem; font-size: .875rem; color: #0f172a; outline: none; transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease; }
    .profile-input:focus { border-color: #2563eb; background: #fff; box-shadow: 0 0 0 3px rgba(37, 99, 235, .14); }
  </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 font-sans antialiased">
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main class="page-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-3"><a href="index.php" class="hover:text-blue-600">VendLink</a><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i><span>Account Settings</span></div>
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
      <div><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/20"><i data-lucide="user-cog" class="w-5 h-5"></i></div><h1 class="text-2xl font-black tracking-tight">Profile & Account Settings</h1></div><p class="text-sm text-slate-500 mt-2">Keep your identity, business details, and account security current.</p></div>
      <div id="saveState" class="hidden text-sm font-semibold px-4 py-2.5 rounded-xl"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[18rem_minmax(0,1fr)] gap-6 items-start">
      <aside class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-6 lg:sticky lg:top-24">
        <div class="flex flex-col items-center text-center">
          <div class="relative w-28 h-28 rounded-full bg-blue-50 border-4 border-white shadow-md flex items-center justify-center overflow-hidden"><img id="avatarPreview" src="" alt="Profile avatar" class="hidden w-full h-full object-cover"><span id="avatarInitial" class="text-3xl font-black text-blue-600">?</span></div>
          <h2 id="summaryName" class="mt-4 text-lg font-black text-slate-900">Loading profile...</h2>
          <span id="summaryRole" class="mt-2 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-lg bg-blue-100 text-blue-700">Account</span>
          <p id="summaryMemberSince" class="text-xs text-slate-400 mt-3"></p>
        </div>
        <nav class="mt-7 space-y-1 text-sm font-semibold">
          <a href="#general" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-blue-50 text-blue-700"><i data-lucide="user" class="w-4 h-4"></i> General Profile</a>
          <a href="#business" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50"><i data-lucide="store" class="w-4 h-4"></i> Business Details</a>
          <a href="#payments" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50"><i data-lucide="credit-card" class="w-4 h-4"></i> Payment Methods</a>
          <a href="#security" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50"><i data-lucide="lock" class="w-4 h-4"></i> Security & Password</a>
        </nav>
      </aside>

      <section class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-5 sm:p-7">
        <div id="profileError" class="hidden mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium"></div>
        <form id="profileForm" class="space-y-9" novalidate>
          <section id="general" class="scroll-mt-24">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100"><div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i data-lucide="user-round" class="w-4 h-4"></i></div><div><h2 class="font-black text-slate-900">General Profile</h2><p class="text-xs text-slate-500 mt-0.5">Your personal identity and contact information.</p></div></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
              <label class="block"><span class="field-label">Full Name</span><span class="field-wrap"><i data-lucide="user" class="field-icon"></i><input id="name" name="name" required maxlength="100" class="profile-input"></span></label>
              <label class="block"><span class="field-label">Username</span><span class="field-wrap"><i data-lucide="at-sign" class="field-icon"></i><input id="username" name="username" maxlength="50" class="profile-input"></span></label>
              <label class="block"><span class="field-label">Email Address</span><span class="field-wrap"><i data-lucide="mail" class="field-icon"></i><input id="email" name="email" type="email" required maxlength="191" class="profile-input"></span></label>
              <label class="block"><span class="field-label">Contact / Mobile Number</span><span class="field-wrap"><i data-lucide="phone" class="field-icon"></i><input id="contactNumber" name="contactNumber" maxlength="30" class="profile-input"></span></label>
              <label class="block md:col-span-2"><span class="field-label">Profile Picture / Avatar URL</span><span class="field-wrap"><i data-lucide="image" class="field-icon"></i><input id="profileImage" name="profileImage" type="url" placeholder="https://example.com/avatar.jpg" class="profile-input"></span></label>
            </div>
          </section>

          <section id="business" class="scroll-mt-24">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100"><div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i data-lucide="building-2" class="w-4 h-4"></i></div><div><h2 class="font-black text-slate-900">Business Details</h2><p class="text-xs text-slate-500 mt-0.5">Store, stall, warehouse, or office information.</p></div></div>
            <div class="grid grid-cols-1 gap-5 mt-5">
              <label class="block"><span class="field-label">Business / Store Name</span><span class="field-wrap"><i data-lucide="store" class="field-icon"></i><input id="businessName" name="businessName" maxlength="100" class="profile-input"></span></label>
              <label class="block"><span class="field-label">Complete Physical / Warehouse Address</span><span class="field-wrap"><i data-lucide="map-pin" class="field-icon top-3.5"></i><textarea id="address" name="address" rows="3" maxlength="2000" class="profile-input pl-10"></textarea></span></label>
            </div>
          </section>

          <section id="payments" class="scroll-mt-24">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100"><div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i data-lucide="credit-card" class="w-4 h-4"></i></div><div><h2 class="font-black text-slate-900">Payment Methods</h2><p class="text-xs text-slate-500 mt-0.5">Registered payout and payment account details.</p></div></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
              <label class="block"><span class="field-label">GCash Registered Number</span><span class="field-wrap"><i data-lucide="smartphone" class="field-icon"></i><input id="gcashNumber" name="gcashNumber" maxlength="30" class="profile-input"></span></label>
              <label class="block"><span class="field-label">Maya / PayMaya Registered Number</span><span class="field-wrap"><i data-lucide="wallet-cards" class="field-icon"></i><input id="paymayaNumber" name="paymayaNumber" maxlength="30" class="profile-input"></span></label>
            </div>
          </section>

          <section id="security" class="scroll-mt-24">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100"><div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i data-lucide="shield-check" class="w-4 h-4"></i></div><div><h2 class="font-black text-slate-900">Security & Notifications</h2><p class="text-xs text-slate-500 mt-0.5">Change your password and control account alerts.</p></div></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
              <label class="block md:col-span-2"><span class="field-label">Current Password <span class="font-normal text-slate-400">(required to change password)</span></span><span class="field-wrap"><i data-lucide="lock" class="field-icon"></i><input id="currentPassword" type="password" autocomplete="current-password" class="profile-input"></span></label>
              <label class="block"><span class="field-label">New Password</span><span class="field-wrap"><i data-lucide="key-round" class="field-icon"></i><input id="newPassword" type="password" minlength="6" autocomplete="new-password" class="profile-input"></span></label>
              <label class="block"><span class="field-label">Confirm New Password</span><span class="field-wrap"><i data-lucide="key-round" class="field-icon"></i><input id="confirmPassword" type="password" minlength="6" autocomplete="new-password" class="profile-input"></span></label>
            </div>
            <label class="mt-5 flex items-center justify-between gap-4 p-4 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer"><span><span class="block text-sm font-bold text-slate-800">Receive order and delivery status alerts</span><span class="block text-xs text-slate-500 mt-1">Get important updates in your VendLink notifications.</span></span><input id="notificationsEnabled" type="checkbox" class="w-5 h-5 accent-blue-600"></label>
          </section>

          <div class="sticky bottom-4 flex justify-end pt-2"><button id="saveProfileButton" type="submit" class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-bold px-6 py-3 rounded-xl shadow-lg shadow-blue-500/20 flex items-center gap-2"><i data-lucide="save" class="w-4 h-4"></i><span>Save Changes</span></button></div>
        </form>
      </section>
    </div>
  </main>
  <?php include __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/js/api.js"></script>
  <script src="assets/js/profile.js"></script>
</body>
</html>
