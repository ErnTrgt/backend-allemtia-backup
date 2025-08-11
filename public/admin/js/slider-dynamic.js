// Slider Management Dynamic JavaScript

// Search functionality
let searchTimer;
document.getElementById('sliderSearch').addEventListener('input', function(e) {
    clearTimeout(searchTimer);
    const query = e.target.value.toLowerCase();
    
    searchTimer = setTimeout(() => {
        const cards = document.querySelectorAll('.slider-card');
        
        cards.forEach(card => {
            const tags = card.dataset.tags || '';
            const description = card.dataset.description || '';
            
            if (tags.includes(query) || description.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }, 300);
});

// Character counter
function addCharacterCounter(element, maxLength = 255) {
    const container = element.closest('.form-group').querySelector('.char-counter-container');
    if (!container) return;
    
    function updateCounter() {
        const currentLength = element.value.length;
        const remaining = maxLength - currentLength;
        
        let counter = container.querySelector('.char-counter');
        if (!counter) {
            counter = document.createElement('span');
            counter.className = 'char-counter';
            container.appendChild(counter);
        }
        
        counter.textContent = `${currentLength}/${maxLength} karakter`;
        
        counter.className = 'char-counter';
        if (remaining < 20) {
            counter.classList.add('warning');
        }
        if (remaining < 0) {
            counter.classList.add('danger');
        }
    }
    
    element.addEventListener('input', updateCounter);
    updateCounter();
}

// Image upload for new slider
const imageUploadArea = document.getElementById('imageUploadArea');
const sliderImageInput = document.getElementById('sliderImage');
const previewImage = document.getElementById('previewImage');

if (imageUploadArea) {
    imageUploadArea.addEventListener('click', () => {
        sliderImageInput.click();
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

if (sliderImageInput) {
    sliderImageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            handleImageFile(this.files[0]);
        }
    });
}

function handleImageFile(file) {
    if (file.size > 5 * 1024 * 1024) {
        showErrorToast('Dosya boyutu 5MB\'dan küçük olmalıdır.');
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
    sliderImageInput.files = dataTransfer.files;
}

function removeImage() {
    sliderImageInput.value = '';
    previewImage.src = '';
    imageUploadArea.classList.remove('has-image');
    imageUploadArea.querySelector('.image-preview').style.display = 'none';
}

// Edit Slider Modal Functions
const editImageUploadArea = document.getElementById('editImageUploadArea');
const editSliderImageInput = document.getElementById('editSliderImage');
const editPreviewImage = document.getElementById('editPreviewImage');

if (editImageUploadArea) {
    editImageUploadArea.addEventListener('click', () => {
        editSliderImageInput.click();
    });

    editSliderImageInput.addEventListener('change', function() {
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
    editSliderImageInput.value = '';
    editPreviewImage.src = '';
    editImageUploadArea.classList.remove('has-image');
    editImageUploadArea.querySelector('.image-preview').style.display = 'none';
}

// Show Add Slider Modal
function showAddSliderModal() {
    // Reset form
    document.getElementById('addSliderForm').reset();
    removeImage();
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('addSliderModal'));
    modal.show();
}

// Handle Slider Add with AJAX
function handleSliderAdd(event) {
    event.preventDefault();
    
    const form = document.getElementById('addSliderForm');
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Ekleniyor...';
    
    fetch('/admin/sliders/store', {
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
            const modal = bootstrap.Modal.getInstance(document.getElementById('addSliderModal'));
            if (modal) modal.hide();
            
            // Add slider to grid
            addSliderToGrid(data.slider);
            
            // Update stats
            updateSliderStats();
            
            // Show success toast
            showSuccessToast('Slider başarıyla eklendi!');
            
            // Reset form
            form.reset();
            removeImage();
        } else {
            showErrorToast(data.message || 'Slider eklenirken hata oluştu!');
        }
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Slider eklenirken hata oluştu!');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

// Edit Slider Function
function editSlider(sliderId) {
    showLoadingToast('Slider bilgileri yükleniyor...');
    
    fetch(`/admin/sliders/${sliderId}/edit-ajax`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const slider = data.slider;
            
            // Set form action
            document.getElementById('editSliderForm').action = `/admin/sliders/${slider.id}`;
            document.getElementById('editSliderId').value = slider.id;
            
            // Fill form fields
            document.getElementById('editTagOne').value = slider.tag_one || '';
            document.getElementById('editTagTwo').value = slider.tag_two || '';
            document.getElementById('editDescription').value = slider.description || '';
            document.getElementById('editStatus').value = slider.status ? '1' : '0';
            
            // Handle image
            if (slider.image_url) {
                editPreviewImage.src = slider.image_url;
                editImageUploadArea.classList.add('has-image');
                editImageUploadArea.querySelector('.image-preview').style.display = 'flex';
            } else {
                editImageUploadArea.classList.remove('has-image');
                editImageUploadArea.querySelector('.image-preview').style.display = 'none';
            }
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editSliderModal'));
            modal.show();
            
            hideLoadingToast();
        } else {
            showErrorToast(data.message || 'Slider bilgileri yüklenemedi!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Slider bilgileri yüklenirken hata oluştu!');
    });
}

// Handle Slider Edit with AJAX
function handleSliderEdit(event) {
    event.preventDefault();
    
    const form = document.getElementById('editSliderForm');
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
            const modal = bootstrap.Modal.getInstance(document.getElementById('editSliderModal'));
            if (modal) modal.hide();
            
            // Update slider in grid
            updateSliderInGrid(data.slider);
            
            // Update stats
            updateSliderStats();
            
            // Show success toast
            showSuccessToast('Slider başarıyla güncellendi!');
        } else {
            showErrorToast(data.message || 'Slider güncellenirken hata oluştu!');
        }
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Slider güncellenirken hata oluştu!');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

// Toggle Slider Status
function toggleSliderStatus(sliderId) {
    showLoadingToast('İşleminiz gerçekleştiriliyor...');
    
    fetch(`/admin/sliders/${sliderId}/toggle`, {
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
            // Update slider in grid
            updateSliderInGrid(data.slider);
            
            // Update stats
            updateSliderStats();
            
            // Show success toast
            showSuccessToast(data.message || 'Slider durumu güncellendi!');
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

// Delete Slider
function deleteSlider(sliderId) {
    if (confirm('Bu slider\'ı silmek istediğinizden emin misiniz?')) {
        showLoadingToast('Slider siliniyor...');
        
        fetch(`/admin/sliders/${sliderId}`, {
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
                // Remove slider from grid
                removeSliderFromGrid(sliderId);
                
                // Update stats
                updateSliderStats();
                
                // Show success toast
                showSuccessToast('Slider başarıyla silindi!');
            } else {
                showErrorToast(data.message || 'Slider silinirken hata oluştu!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            hideLoadingToast();
            showErrorToast('Slider silinirken hata oluştu!');
        });
    }
}

// Add slider to grid
function addSliderToGrid(slider) {
    // Remove empty state if exists
    const emptyState = document.querySelector('.empty-state');
    if (emptyState) {
        emptyState.remove();
    }
    
    const sliderGrid = document.querySelector('.slider-grid');
    const sliderCard = createSliderCard(slider, 0);
    
    sliderGrid.insertAdjacentHTML('afterbegin', sliderCard);
    
    // Add animation
    const newCard = sliderGrid.querySelector('.slider-card:first-child');
    newCard.classList.add('slider-card-new');
    
    // Update all order badges
    updateOrderBadges();
}

// Update slider in grid
function updateSliderInGrid(slider) {
    const card = document.querySelector(`.slider-card[data-slider-id="${slider.id}"]`);
    
    if (card) {
        // Update data attributes
        card.dataset.tags = (slider.tag_one + ' ' + slider.tag_two).toLowerCase();
        card.dataset.description = (slider.description || '').toLowerCase();
        
        // Update image
        const imageContainer = card.querySelector('.slider-image');
        const imageContent = slider.image_url 
            ? `<img src="${slider.image_url}" alt="${slider.tag_one}">`
            : `<div class="slider-image-placeholder"><i class="bi bi-image"></i></div>`;
        
        const existingImg = imageContainer.querySelector('img');
        const existingPlaceholder = imageContainer.querySelector('.slider-image-placeholder');
        
        if (existingImg) {
            if (slider.image_url) {
                existingImg.src = slider.image_url;
            } else {
                existingImg.outerHTML = `<div class="slider-image-placeholder"><i class="bi bi-image"></i></div>`;
            }
        } else if (existingPlaceholder) {
            if (slider.image_url) {
                existingPlaceholder.outerHTML = `<img src="${slider.image_url}" alt="${slider.tag_one}">`;
            }
        }
        
        // Update status badge
        const statusBadge = card.querySelector('.slider-status');
        statusBadge.className = `slider-status ${slider.status ? 'active' : 'inactive'}`;
        statusBadge.innerHTML = `
            <i class="bi bi-${slider.status ? 'check' : 'x'}-circle"></i>
            ${slider.status ? 'Aktif' : 'Pasif'}
        `;
        
        // Update tags
        const tagOne = card.querySelector('.slider-tag:nth-child(1) .tag-text');
        if (tagOne && slider.tag_one) tagOne.textContent = slider.tag_one;
        
        const tagTwo = card.querySelector('.slider-tag:nth-child(2) .tag-text');
        if (tagTwo && slider.tag_two) tagTwo.textContent = slider.tag_two;
        
        // Update description
        const descEl = card.querySelector('.slider-description');
        if (descEl) descEl.textContent = slider.description || 'Açıklama bulunmuyor...';
        
        // Update action buttons
        const toggleBtn = card.querySelector('.action-btn.toggle');
        toggleBtn.onclick = () => toggleSliderStatus(slider.id);
        toggleBtn.innerHTML = `
            <i class="bi bi-${slider.status ? 'pause' : 'play'}"></i>
            ${slider.status ? 'Pasifleştir' : 'Aktifleştir'}
        `;
        
        // Highlight updated card
        card.classList.add('slider-card-updated');
        setTimeout(() => {
            card.classList.remove('slider-card-updated');
        }, 2000);
    } else {
        // If card doesn't exist, add it
        addSliderToGrid(slider);
    }
}

// Remove slider from grid
function removeSliderFromGrid(sliderId) {
    const card = document.querySelector(`.slider-card[data-slider-id="${sliderId}"]`);
    
    if (card) {
        card.classList.add('slider-card-removing');
        
        setTimeout(() => {
            card.remove();
            
            // Update order badges
            updateOrderBadges();
            
            // Check if grid is empty
            const remainingCards = document.querySelectorAll('.slider-card');
            if (remainingCards.length === 0) {
                const sliderGrid = document.querySelector('.slider-grid');
                sliderGrid.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-images empty-icon"></i>
                        <h3 class="empty-title">Henüz Slider Yok</h3>
                        <p class="empty-text">İlk slider'ınızı oluşturmak için yukarıdaki butonu kullanın.</p>
                    </div>
                `;
            }
        }, 300);
    }
}

// Update order badges
function updateOrderBadges() {
    const cards = document.querySelectorAll('.slider-card');
    cards.forEach((card, index) => {
        const orderBadge = card.querySelector('.slider-order');
        if (orderBadge) {
            orderBadge.textContent = index + 1;
        }
    });
}

// Create slider card HTML
function createSliderCard(slider, index) {
    const imageHtml = slider.image_url 
        ? `<img src="${slider.image_url}" alt="${slider.tag_one}">`
        : `<div class="slider-image-placeholder"><i class="bi bi-image"></i></div>`;
    
    const tagOneHtml = slider.tag_one 
        ? `<div class="slider-tag">
               <span class="tag-label">Etiket 1</span>
               <span class="tag-text">${slider.tag_one}</span>
           </div>` 
        : '';
    
    const tagTwoHtml = slider.tag_two 
        ? `<div class="slider-tag">
               <span class="tag-label">Etiket 2</span>
               <span class="tag-text">${slider.tag_two}</span>
           </div>` 
        : '';
    
    return `
        <div class="slider-card slider-card-new" data-slider-id="${slider.id}" 
             data-tags="${(slider.tag_one + ' ' + slider.tag_two).toLowerCase()}" 
             data-description="${(slider.description || '').toLowerCase()}">
            <div class="slider-image">
                ${imageHtml}
                <span class="slider-order">${index + 1}</span>
                <span class="slider-status ${slider.status ? 'active' : 'inactive'}">
                    <i class="bi bi-${slider.status ? 'check' : 'x'}-circle"></i>
                    ${slider.status ? 'Aktif' : 'Pasif'}
                </span>
            </div>
            <div class="slider-content">
                <div class="slider-tags">
                    ${tagOneHtml}
                    ${tagTwoHtml}
                </div>
                <p class="slider-description">
                    ${slider.description || 'Açıklama bulunmuyor...'}
                </p>
                <div class="slider-actions">
                    <button class="action-btn edit" onclick="editSlider(${slider.id})">
                        <i class="bi bi-pencil"></i>
                        Düzenle
                    </button>
                    <button class="action-btn toggle" onclick="toggleSliderStatus(${slider.id})">
                        <i class="bi bi-${slider.status ? 'pause' : 'play'}"></i>
                        ${slider.status ? 'Pasifleştir' : 'Aktifleştir'}
                    </button>
                    <button class="action-btn delete" onclick="deleteSlider(${slider.id})">
                        <i class="bi bi-trash"></i>
                        Sil
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Update slider stats
function updateSliderStats() {
    fetch('/admin/sliders/stats', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('totalSliders').textContent = data.total;
        document.getElementById('activeSliders').textContent = data.active;
        document.getElementById('inactiveSliders').textContent = data.inactive;
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

// Initialize character counters
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('textarea[name="description"]').forEach(textarea => {
        addCharacterCounter(textarea);
    });
    
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