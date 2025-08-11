// About Management Dynamic JavaScript

// Search functionality
let searchTimer;
document.getElementById('sectionSearch').addEventListener('input', function(e) {
    clearTimeout(searchTimer);
    const query = e.target.value.toLowerCase();
    
    searchTimer = setTimeout(() => {
        const cards = document.querySelectorAll('.section-card');
        
        cards.forEach(card => {
            const key = card.dataset.key || '';
            const title = card.dataset.title || '';
            
            if (key.includes(query) || title.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }, 300);
});

// Image upload for new section
const imageUploadArea = document.getElementById('imageUploadArea');
const sectionImageInput = document.getElementById('sectionImage');
const previewImage = document.getElementById('previewImage');

if (imageUploadArea) {
    imageUploadArea.addEventListener('click', () => {
        sectionImageInput.click();
    });

    imageUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        imageUploadArea.classList.add('dragover');
    });

    imageUploadArea.addEventListener('dragleave', () => {
        imageUploadArea.classList.remove('dragover');
    });

    imageUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        imageUploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleImageFile(files[0]);
        }
    });
}

if (sectionImageInput) {
    sectionImageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            handleImageFile(this.files[0]);
        }
    });
}

function handleImageFile(file) {
    if (file.size > 2 * 1024 * 1024) {
        showErrorToast('Dosya boyutu 2MB\'dan küçük olmalıdır.');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        previewImage.src = e.target.result;
        imageUploadArea.classList.add('has-image');
        imageUploadArea.querySelector('.image-preview').style.display = 'flex';
    };
    reader.readAsDataURL(file);
    
    // Update the actual file input
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    sectionImageInput.files = dataTransfer.files;
}

function removeImage() {
    sectionImageInput.value = '';
    previewImage.src = '';
    imageUploadArea.classList.remove('has-image');
    imageUploadArea.querySelector('.image-preview').style.display = 'none';
}

// Edit Section Modal Functions
const editImageUploadArea = document.getElementById('editImageUploadArea');
const editSectionImageInput = document.getElementById('editSectionImage');
const editPreviewImage = document.getElementById('editPreviewImage');

if (editImageUploadArea) {
    editImageUploadArea.addEventListener('click', () => {
        editSectionImageInput.click();
    });

    editSectionImageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                editPreviewImage.src = e.target.result;
                editImageUploadArea.classList.add('has-image');
                editImageUploadArea.querySelector('.image-preview').style.display = 'flex';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
}

function removeEditImage() {
    editSectionImageInput.value = '';
    editPreviewImage.src = '';
    editImageUploadArea.classList.remove('has-image');
    editImageUploadArea.querySelector('.image-preview').style.display = 'none';
}

// Show Add Section Modal
function showAddSectionModal() {
    // Reset form
    document.getElementById('addSectionForm').reset();
    removeImage();
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('addSectionModal'));
    modal.show();
}

// Handle Section Add with AJAX
function handleSectionAdd(event) {
    event.preventDefault();
    
    const form = document.getElementById('addSectionForm');
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Ekleniyor...';
    
    fetch('/about/store', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addSectionModal'));
            if (modal) modal.hide();
            
            // Add section to grid
            addSectionToGrid(data.section);
            
            // Update stats
            updateAboutStats();
            
            // Show success toast
            showSuccessToast('Bölüm başarıyla eklendi!');
            
            // Reset form
            form.reset();
            removeImage();
        } else {
            showErrorToast(data.message || 'Bölüm eklenirken hata oluştu!');
        }
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Bölüm eklenirken hata oluştu!');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

// Edit Section Function
function editSection(sectionId) {
    showLoadingToast('Bölüm bilgileri yükleniyor...');
    
    fetch(`/admin/about/${sectionId}/edit-ajax`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const section = data.section;
            
            // Set form action
            document.getElementById('editSectionForm').action = `/admin/about/${section.id}`;
            document.getElementById('editSectionId').value = section.id;
            
            // Fill form fields
            document.getElementById('editSectionKey').value = section.section_key;
            document.getElementById('editTitle').value = section.title || '';
            document.getElementById('editContent').value = section.content || '';
            document.getElementById('editStatus').value = section.status ? '1' : '0';
            
            // Handle image
            if (section.image_url) {
                editPreviewImage.src = section.image_url;
                editImageUploadArea.classList.add('has-image');
                editImageUploadArea.querySelector('.image-preview').style.display = 'flex';
            } else {
                editImageUploadArea.classList.remove('has-image');
                editImageUploadArea.querySelector('.image-preview').style.display = 'none';
            }
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editSectionModal'));
            modal.show();
            
            hideLoadingToast();
        } else {
            showErrorToast(data.message || 'Bölüm bilgileri yüklenemedi!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Bölüm bilgileri yüklenirken hata oluştu!');
    });
}

// Handle Section Edit with AJAX
function handleSectionEdit(event) {
    event.preventDefault();
    
    const form = document.getElementById('editSectionForm');
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Güncelleniyor...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editSectionModal'));
            if (modal) modal.hide();
            
            // Update section in grid
            updateSectionInGrid(data.section);
            
            // Update stats
            updateAboutStats();
            
            // Show success toast
            showSuccessToast('Bölüm başarıyla güncellendi!');
        } else {
            showErrorToast(data.message || 'Bölüm güncellenirken hata oluştu!');
        }
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Bölüm güncellenirken hata oluştu!');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

// Toggle Section Status
function toggleSectionStatus(sectionId) {
    showLoadingToast('İşleminiz gerçekleştiriliyor...');
    
    fetch(`/admin/about/${sectionId}/toggle-status`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoadingToast();
        
        if (data.success) {
            // Update section in grid
            updateSectionInGrid(data.section);
            
            // Update stats
            updateAboutStats();
            
            // Show success toast
            showSuccessToast(data.message || 'Bölüm durumu güncellendi!');
        } else {
            showErrorToast(data.message || 'İşlem sırasında hata oluştu!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        hideLoadingToast();
        showErrorToast('İşlem sırasında hata oluştu!');
    });
}

// Delete Section
function deleteSection(sectionId) {
    if (confirm('Bu bölümü silmek istediğinizden emin misiniz?')) {
        showLoadingToast('Bölüm siliniyor...');
        
        fetch(`/admin/about/${sectionId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoadingToast();
            
            if (data.success) {
                // Remove section from grid
                removeSectionFromGrid(sectionId);
                
                // Update stats
                updateAboutStats();
                
                // Show success toast
                showSuccessToast('Bölüm başarıyla silindi!');
            } else {
                showErrorToast(data.message || 'Bölüm silinirken hata oluştu!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            hideLoadingToast();
            showErrorToast('Bölüm silinirken hata oluştu!');
        });
    }
}

// Add section to grid
function addSectionToGrid(section) {
    // Remove empty state if exists
    const emptyState = document.querySelector('.empty-state');
    if (emptyState) {
        emptyState.remove();
    }
    
    const sectionsGrid = document.querySelector('.sections-grid');
    const sectionCard = createSectionCard(section);
    
    sectionsGrid.insertAdjacentHTML('afterbegin', sectionCard);
    
    // Add animation
    const newCard = sectionsGrid.querySelector('.section-card:first-child');
    newCard.classList.add('section-card-new');
}

// Update section in grid
function updateSectionInGrid(section) {
    const card = document.querySelector(`.section-card[data-section-id="${section.id}"]`);
    
    if (card) {
        // Update data attributes
        card.dataset.key = section.section_key.toLowerCase();
        card.dataset.title = (section.title || '').toLowerCase();
        
        // Update section key
        const keyElement = card.querySelector('.section-key');
        if (keyElement) {
            keyElement.innerHTML = `<i class="bi bi-key"></i> ${section.section_key}`;
        }
        
        // Update title
        const titleElement = card.querySelector('.section-title');
        if (titleElement) {
            titleElement.textContent = section.title || 'Başlıksız Bölüm';
        }
        
        // Update image
        const imageContainer = card.querySelector('.section-image');
        if (section.image_url) {
            if (imageContainer) {
                imageContainer.innerHTML = `
                    <img src="${section.image_url}" alt="${section.title}">
                    <span class="section-status ${section.status ? 'active' : 'inactive'}">
                        <i class="bi bi-${section.status ? 'check' : 'x'}-circle"></i>
                        ${section.status ? 'Aktif' : 'Pasif'}
                    </span>
                `;
            } else {
                // Add image container if it doesn't exist
                const header = card.querySelector('.section-header');
                const imageHtml = `
                    <div class="section-image">
                        <img src="${section.image_url}" alt="${section.title}">
                        <span class="section-status ${section.status ? 'active' : 'inactive'}">
                            <i class="bi bi-${section.status ? 'check' : 'x'}-circle"></i>
                            ${section.status ? 'Aktif' : 'Pasif'}
                        </span>
                    </div>
                `;
                header.insertAdjacentHTML('afterend', imageHtml);
            }
        } else if (imageContainer) {
            imageContainer.remove();
        }
        
        // Update content
        const contentElement = card.querySelector('.section-text');
        if (contentElement) {
            const content = section.content || 'İçerik bulunmuyor...';
            contentElement.textContent = content.length > 200 ? content.substring(0, 200) + '...' : content;
        }
        
        // Update action buttons
        const toggleBtn = card.querySelector('.action-btn.toggle');
        if (toggleBtn) {
            toggleBtn.onclick = () => toggleSectionStatus(section.id);
            toggleBtn.innerHTML = `
                <i class="bi bi-${section.status ? 'pause' : 'play'}"></i>
                ${section.status ? 'Pasifleştir' : 'Aktifleştir'}
            `;
        }
        
        // Highlight updated card
        card.classList.add('section-card-updated');
        setTimeout(() => {
            card.classList.remove('section-card-updated');
        }, 2000);
    } else {
        // If card doesn't exist, add it
        addSectionToGrid(section);
    }
}

// Remove section from grid
function removeSectionFromGrid(sectionId) {
    const card = document.querySelector(`.section-card[data-section-id="${sectionId}"]`);
    
    if (card) {
        card.classList.add('section-card-removing');
        
        setTimeout(() => {
            card.remove();
            
            // Check if grid is empty
            const remainingCards = document.querySelectorAll('.section-card');
            if (remainingCards.length === 0) {
                const sectionsGrid = document.querySelector('.sections-grid');
                sectionsGrid.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-info-square empty-icon"></i>
                        <h3 class="empty-title">Henüz Bölüm Yok</h3>
                        <p class="empty-text">İlk bölümünüzü oluşturmak için yukarıdaki butonu kullanın.</p>
                    </div>
                `;
            }
        }, 300);
    }
}

// Create section card HTML
function createSectionCard(section) {
    const imageHtml = section.image_url 
        ? `<div class="section-image">
               <img src="${section.image_url}" alt="${section.title}">
               <span class="section-status ${section.status ? 'active' : 'inactive'}">
                   <i class="bi bi-${section.status ? 'check' : 'x'}-circle"></i>
                   ${section.status ? 'Aktif' : 'Pasif'}
               </span>
           </div>`
        : '';
    
    const content = section.content || 'İçerik bulunmuyor...';
    const excerpt = content.length > 200 ? content.substring(0, 200) + '...' : content;
    
    return `
        <div class="section-card section-card-new" data-section-id="${section.id}" 
             data-key="${section.section_key.toLowerCase()}" 
             data-title="${(section.title || '').toLowerCase()}">
            <div class="section-header">
                <span class="section-key">
                    <i class="bi bi-key"></i>
                    ${section.section_key}
                </span>
                <h3 class="section-title">${section.title || 'Başlıksız Bölüm'}</h3>
            </div>
            ${imageHtml}
            <div class="section-content">
                <p class="section-text">${excerpt}</p>
                <div class="section-actions">
                    <button class="action-btn edit" onclick="editSection(${section.id})">
                        <i class="bi bi-pencil"></i>
                        Düzenle
                    </button>
                    <button class="action-btn toggle" onclick="toggleSectionStatus(${section.id})">
                        <i class="bi bi-${section.status ? 'pause' : 'play'}"></i>
                        ${section.status ? 'Pasifleştir' : 'Aktifleştir'}
                    </button>
                    <button class="action-btn delete" onclick="deleteSection(${section.id})">
                        <i class="bi bi-trash"></i>
                        Sil
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Update about stats
function updateAboutStats() {
    fetch('/admin/about/stats', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('totalSections').textContent = data.total;
        document.getElementById('activeSections').textContent = data.active;
        document.getElementById('inactiveSections').textContent = data.inactive;
        document.getElementById('withImages').textContent = data.with_images;
    })
    .catch(error => console.error('Error updating stats:', error));
}

// Toast notifications
let loadingToast = null;

function showLoadingToast(message) {
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'loading-toast';
    const existingToast = document.getElementById(toastId);
    if (existingToast) existingToast.remove();
    
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-primary border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                    ${message}
                </div>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastEl = document.getElementById(toastId);
    loadingToast = new bootstrap.Toast(toastEl, { autohide: false });
    loadingToast.show();
}

function hideLoadingToast() {
    if (loadingToast) {
        loadingToast.hide();
        setTimeout(() => {
            const toastEl = document.getElementById('loading-toast');
            if (toastEl) toastEl.remove();
        }, 300);
    }
}

function showSuccessToast(message) {
    createToast(message, 'success', 'check-circle');
}

function showErrorToast(message) {
    createToast(message, 'danger', 'exclamation-circle');
}

function createToast(message, type, icon) {
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${icon} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastEl = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 3000 });
    toast.show();
    
    toastEl.addEventListener('hidden.bs.toast', () => {
        toastEl.remove();
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to upload areas
    document.querySelectorAll('.image-upload-area').forEach(area => {
        area.addEventListener('mouseenter', function() {
            if (!this.classList.contains('has-image')) {
                this.style.background = 'rgba(255, 255, 255, 0.7)';
                this.style.borderColor = 'rgba(169, 0, 0, 0.5)';
            }
        });
        area.addEventListener('mouseleave', function() {
            if (!this.classList.contains('has-image')) {
                this.style.background = 'rgba(255, 255, 255, 0.5)';
                this.style.borderColor = 'rgba(169, 0, 0, 0.3)';
            }
        });
    });
});