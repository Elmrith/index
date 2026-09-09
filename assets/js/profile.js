// assets/js/profile.js
const profileForm = document.getElementById('profileForm');
const profileError = document.getElementById('profileError');
const saveState = document.getElementById('saveState');
const saveButton = document.getElementById('saveProfileButton');
const avatarPreview = document.getElementById('avatarPreview');
const avatarInitial = document.getElementById('avatarInitial');

function profileValue(id) {
    return document.getElementById(id).value.trim();
}

function setFeedback(message, type = 'success') {
    saveState.textContent = message;
    saveState.className = type === 'success'
        ? 'text-sm font-semibold px-4 py-2.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100'
        : 'text-sm font-semibold px-4 py-2.5 rounded-xl bg-red-50 text-red-700 border border-red-100';
    saveState.classList.remove('hidden');
    profileError.classList.add('hidden');
}

function setError(message) {
    profileError.textContent = message;
    profileError.classList.remove('hidden');
    saveState.classList.add('hidden');
    profileError.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function updateAvatar(name, imageUrl) {
    avatarInitial.textContent = (name || '?').trim().charAt(0).toUpperCase() || '?';
    const safeUrl = /^(https:\/\/|\/|assets\/)/i.test(imageUrl || '') ? imageUrl : '';
    if (safeUrl) {
        avatarPreview.src = safeUrl;
        avatarPreview.classList.remove('hidden');
        avatarInitial.classList.add('hidden');
        avatarPreview.onerror = () => {
            avatarPreview.classList.add('hidden');
            avatarInitial.classList.remove('hidden');
        };
    } else {
        avatarPreview.removeAttribute('src');
        avatarPreview.classList.add('hidden');
        avatarInitial.classList.remove('hidden');
    }
}

function fillProfile(profile) {
    ['name', 'username', 'email', 'contactNumber', 'profileImage', 'businessName', 'address', 'gcashNumber', 'paymayaNumber'].forEach(id => {
        document.getElementById(id).value = profile[id] || '';
    });
    document.getElementById('notificationsEnabled').checked = Number(profile.notificationsEnabled) === 1;
    document.getElementById('summaryName').textContent = profile.name || 'VendLink User';
    document.getElementById('summaryRole').textContent = profile.role || 'Account';
    document.getElementById('summaryMemberSince').textContent = profile.createdAt ? `Member since ${new Date(profile.createdAt.replace(' ', 'T')).toLocaleDateString('en-US', { month: 'short', year: 'numeric' })}` : '';
    updateAvatar(profile.name, profile.profileImage);
}

async function loadProfile() {
    try {
        const profile = await API.get('api/profile.php');
        fillProfile(profile);
        lucide.createIcons();
    } catch (error) {
        setError(error.message || 'Unable to load your profile.');
    }
}

function collectProfile() {
    return {
        name: profileValue('name'),
        username: profileValue('username'),
        email: profileValue('email'),
        contactNumber: profileValue('contactNumber'),
        profileImage: profileValue('profileImage'),
        businessName: profileValue('businessName'),
        address: profileValue('address'),
        gcashNumber: profileValue('gcashNumber'),
        paymayaNumber: profileValue('paymayaNumber'),
        notificationsEnabled: document.getElementById('notificationsEnabled').checked,
        currentPassword: document.getElementById('currentPassword').value,
        newPassword: document.getElementById('newPassword').value,
        confirmPassword: document.getElementById('confirmPassword').value
    };
}

async function handleProfileSubmit(event) {
    event.preventDefault();
    const data = collectProfile();
    if (!data.name || !data.email) {
        setError('Full name and email address are required.');
        return;
    }
    if ((data.currentPassword || data.newPassword || data.confirmPassword) && (!data.currentPassword || data.newPassword.length < 6 || data.newPassword !== data.confirmPassword)) {
        setError('Enter your current password, a new password of at least 6 characters, and matching confirmation.');
        return;
    }

    saveButton.disabled = true;
    saveButton.innerHTML = '<i data-lucide="loader-circle" class="w-4 h-4 animate-spin"></i><span>Saving changes...</span>';
    lucide.createIcons();
    try {
        const result = await API.post('api/profile.php', data);
        document.getElementById('currentPassword').value = '';
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmPassword').value = '';
        setFeedback(result.message || 'Profile updated successfully!');
        await loadProfile();
    } catch (error) {
        setError(error.message || 'Profile could not be updated.');
    } finally {
        saveButton.disabled = false;
        saveButton.innerHTML = '<i data-lucide="save" class="w-4 h-4"></i><span>Save Changes</span>';
        lucide.createIcons();
    }
}

document.getElementById('profileImage').addEventListener('input', event => {
    updateAvatar(profileValue('name'), event.target.value.trim());
});
document.getElementById('name').addEventListener('input', event => {
    updateAvatar(event.target.value, profileValue('profileImage'));
});
profileForm.addEventListener('submit', handleProfileSubmit);
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
    loadProfile();
});
