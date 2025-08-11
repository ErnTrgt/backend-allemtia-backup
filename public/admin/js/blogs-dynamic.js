// Blog Management Dynamic JavaScript

// Search functionality
let searchTimer;
document.getElementById('blogSearch').addEventListener('input', function(e) {
    clearTimeout(searchTimer);
    const query = e.target.value.toLowerCase();
    
    searchTimer = setTimeout(() => {
        const cards = document.querySelectorAll('.blog-card');
        
        cards.forEach(card => {
            const title = card.dataset.title;
            const author = card.dataset.author;
            
            if (title.includes(query) || author.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }, 300);
});

// Status filter
document.querySelectorAll('.filter-pill').forEach(pill => {
    pill.addEventListener('click', function() {
        // Update active state
        document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        
        const status = this.dataset.status;
        const cards = document.querySelectorAll('.blog-card');
        
        cards.forEach(card => {
            if (status === 'all') {
                card.style.display = '';
            } else if (status === 'active' && card.dataset.status === 'active') {
                card.style.display = '';
            } else if (status === 'inactive' && card.dataset.status === 'inactive') {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

// Image upload for new blog
const imageUploadArea = document.getElementById('imageUploadArea');
const blogImageInput = document.getElementById('blogImage');
const previewImage = document.getElementById('previewImage');

imageUploadArea.addEventListener('click', () => {
    blogImageInput.click();
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

blogImageInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        handleImageFile(this.files[0]);
    }
});

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
    blogImageInput.files = dataTransfer.files;
}

function removeImage() {
    blogImageInput.value = '';
    previewImage.src = '';
    imageUploadArea.classList.remove('has-image');
    imageUploadArea.querySelector('.image-preview').style.display = 'none';
}

// Edit Blog Modal Functions
const editImageUploadArea = document.getElementById('editImageUploadArea');
const editBlogImageInput = document.getElementById('editBlogImage');
const editPreviewImage = document.getElementById('editPreviewImage');

editImageUploadArea.addEventListener('click', () => {
    editBlogImageInput.click();
});

editBlogImageInput.addEventListener('change', function() {
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

function removeEditImage() {
    editBlogImageInput.value = '';
    editPreviewImage.src = '';
    editImageUploadArea.classList.remove('has-image');
    editImageUploadArea.querySelector('.image-preview').style.display = 'none';
}

// Show Add Blog Modal
function showAddBlogModal() {
    // Reset form
    document.getElementById('addBlogForm').reset();
    removeImage();
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('addBlogModal'));
    modal.show();
}

// Handle Blog Add with AJAX
function handleBlogAdd(event) {
    event.preventDefault();
    
    const form = document.getElementById('addBlogForm');
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Ekleniyor...';
    
    fetch('/admin/blogs', {
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
            const modal = bootstrap.Modal.getInstance(document.getElementById('addBlogModal'));
            if (modal) modal.hide();
            
            // Add blog to grid
            addBlogToGrid(data.blog);
            
            // Update stats
            updateBlogStats();
            
            // Show success toast
            showSuccessToast('Blog başarıyla eklendi!');
            
            // Reset form
            form.reset();
            removeImage();
        } else {
            showErrorToast(data.message || 'Blog eklenirken hata oluştu!');
        }
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Blog eklenirken hata oluştu!');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

// Edit Blog Function
function editBlog(blogId) {
    showLoadingToast('Blog bilgileri yükleniyor...');
    
    fetch(`/admin/blogs/${blogId}/edit-ajax`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const blog = data.blog;
            
            // Set form action
            document.getElementById('editBlogForm').action = `/admin/blogs/${blog.id}`;
            document.getElementById('editBlogId').value = blog.id;
            
            // Fill form fields
            document.getElementById('editTitle').value = blog.title;
            document.getElementById('editAuthor').value = blog.author;
            document.getElementById('editDate').value = blog.date;
            document.getElementById('editContent').value = blog.content || '';
            document.getElementById('editStatus').value = blog.status ? '1' : '0';
            
            // Handle image
            if (blog.blog_img_url) {
                editPreviewImage.src = blog.blog_img_url;
                editImageUploadArea.classList.add('has-image');
                editImageUploadArea.querySelector('.image-preview').style.display = 'flex';
            } else {
                editImageUploadArea.classList.remove('has-image');
                editImageUploadArea.querySelector('.image-preview').style.display = 'none';
            }
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editBlogModal'));
            modal.show();
            
            hideLoadingToast();
        } else {
            showErrorToast(data.message || 'Blog bilgileri yüklenemedi!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Blog bilgileri yüklenirken hata oluştu!');
    });
}

// Handle Blog Edit with AJAX
function handleBlogEdit(event) {
    event.preventDefault();
    
    const form = document.getElementById('editBlogForm');
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
            const modal = bootstrap.Modal.getInstance(document.getElementById('editBlogModal'));
            if (modal) modal.hide();
            
            // Update blog in grid
            updateBlogInGrid(data.blog);
            
            // Update stats
            updateBlogStats();
            
            // Show success toast
            showSuccessToast('Blog başarıyla güncellendi!');
        } else {
            showErrorToast(data.message || 'Blog güncellenirken hata oluştu!');
        }
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Blog güncellenirken hata oluştu!');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

// Toggle Blog Status
function toggleBlogStatus(blogId, newStatus) {
    const message = newStatus ? 'Bu blogu yayınlamak istediğinizden emin misiniz?' : 'Bu blogu taslağa almak istediğinizden emin misiniz?';
    
    if (confirm(message)) {
        showLoadingToast('İşleminiz gerçekleştiriliyor...');
        
        fetch('/admin/blogs/change-status', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                id: blogId,
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            hideLoadingToast();
            
            if (data.success) {
                // Update blog in grid
                updateBlogInGrid(data.blog);
                
                // Update stats
                updateBlogStats();
                
                // Show success toast
                showSuccessToast(data.message || 'Blog durumu güncellendi!');
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
}

// Delete Blog
function deleteBlog(blogId) {
    if (confirm('Bu blogu silmek istediğinizden emin misiniz?')) {
        showLoadingToast('Blog siliniyor...');
        
        fetch(`/admin/blogs/${blogId}`, {
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
                // Remove blog from grid
                removeBlogFromGrid(blogId);
                
                // Update stats
                updateBlogStats();
                
                // Show success toast
                showSuccessToast('Blog başarıyla silindi!');
            } else {
                showErrorToast(data.message || 'Blog silinirken hata oluştu!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            hideLoadingToast();
            showErrorToast('Blog silinirken hata oluştu!');
        });
    }
}

// Add blog to grid
function addBlogToGrid(blog) {
    // Remove empty state if exists
    const emptyState = document.querySelector('.empty-state');
    if (emptyState) {
        emptyState.remove();
    }
    
    const blogsGrid = document.getElementById('blogsGrid');
    const blogCard = createBlogCard(blog);
    
    blogsGrid.insertAdjacentHTML('afterbegin', blogCard);
    
    // Add animation
    const newCard = blogsGrid.querySelector('.blog-card:first-child');
    newCard.classList.add('blog-card-new');
}

// Update blog in grid
function updateBlogInGrid(blog) {
    const card = document.querySelector(`.blog-card[data-blog-id="${blog.id}"]`);
    
    if (card) {
        // Update data attributes
        card.dataset.status = blog.status ? 'active' : 'inactive';
        card.dataset.title = blog.title.toLowerCase();
        card.dataset.author = blog.author.toLowerCase();
        
        // Update image
        const imageContainer = card.querySelector('.blog-image');
        if (blog.blog_img_url) {
            const img = imageContainer.querySelector('img');
            if (img) {
                img.src = blog.blog_img_url;
            } else {
                const placeholder = imageContainer.querySelector('.blog-image-placeholder');
                if (placeholder) {
                    placeholder.outerHTML = `<img src="${blog.blog_img_url}" alt="${blog.title}">`;
                }
            }
        }
        
        // Update status badge
        const statusBadge = card.querySelector('.blog-status');
        statusBadge.className = `blog-status ${blog.status ? 'active' : 'inactive'}`;
        statusBadge.innerHTML = `
            <i class="bi bi-${blog.status ? 'check' : 'x'}-circle"></i>
            ${blog.status ? 'Yayında' : 'Taslak'}
        `;
        
        // Update quick actions
        const toggleBtn = card.querySelector('.blog-quick-actions button:nth-child(2)');
        toggleBtn.onclick = () => toggleBlogStatus(blog.id, blog.status ? 0 : 1);
        toggleBtn.title = blog.status ? 'Taslağa Al' : 'Yayınla';
        toggleBtn.innerHTML = `<i class="bi bi-${blog.status ? 'pause' : 'play'}-circle"></i>`;
        
        // Update meta info
        const authorSpan = card.querySelector('.blog-meta-item:nth-child(1) span');
        if (authorSpan) authorSpan.textContent = blog.author;
        
        const dateSpan = card.querySelector('.blog-meta-item:nth-child(2) span');
        if (dateSpan) dateSpan.textContent = new Date(blog.date).toLocaleDateString('tr-TR');
        
        // Update title
        const titleEl = card.querySelector('.blog-title');
        if (titleEl) titleEl.textContent = blog.title;
        
        // Update excerpt
        const excerptEl = card.querySelector('.blog-excerpt');
        if (excerptEl && blog.content) {
            const excerpt = blog.content.replace(/<[^>]*>/g, '').substring(0, 120) + '...';
            excerptEl.textContent = excerpt;
        }
        
        // Highlight updated card
        card.classList.add('blog-card-updated');
        setTimeout(() => {
            card.classList.remove('blog-card-updated');
        }, 2000);
    } else {
        // If card doesn't exist, add it
        addBlogToGrid(blog);
    }
}

// Remove blog from grid
function removeBlogFromGrid(blogId) {
    const card = document.querySelector(`.blog-card[data-blog-id="${blogId}"]`);
    
    if (card) {
        card.classList.add('blog-card-removing');
        
        setTimeout(() => {
            card.remove();
            
            // Check if grid is empty
            const remainingCards = document.querySelectorAll('.blog-card');
            if (remainingCards.length === 0) {
                const blogsGrid = document.getElementById('blogsGrid');
                blogsGrid.innerHTML = `
                    <div class="empty-state" id="emptyState">
                        <i class="bi bi-newspaper empty-icon"></i>
                        <h3 class="empty-title">Henüz Blog Yok</h3>
                        <p class="empty-text">İlk blogunuzu oluşturmak için yukarıdaki butonu kullanın.</p>
                    </div>
                `;
            }
        }, 300);
    }
}

// Create blog card HTML
function createBlogCard(blog) {
    const imageHtml = blog.blog_img_url 
        ? `<img src="${blog.blog_img_url}" alt="${blog.title}">`
        : `<div class="blog-image-placeholder"><i class="bi bi-image"></i></div>`;
    
    const excerpt = blog.content 
        ? blog.content.replace(/<[^>]*>/g, '').substring(0, 120) + '...'
        : 'Blog içeriği için kısa açıklama...';
    
    const viewsHtml = blog.views 
        ? `<div class="blog-meta-item"><i class="bi bi-eye"></i><span>${blog.views}</span></div>`
        : '';
    
    return `
        <div class="blog-card blog-card-new" data-blog-id="${blog.id}" 
             data-status="${blog.status ? 'active' : 'inactive'}" 
             data-title="${blog.title.toLowerCase()}" 
             data-author="${blog.author.toLowerCase()}">
            <div class="blog-image">
                ${imageHtml}
                <span class="blog-status ${blog.status ? 'active' : 'inactive'}">
                    <i class="bi bi-${blog.status ? 'check' : 'x'}-circle"></i>
                    ${blog.status ? 'Yayında' : 'Taslak'}
                </span>
                <div class="blog-quick-actions">
                    <button class="quick-action-btn" onclick="editBlog(${blog.id})" title="Düzenle">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="quick-action-btn" onclick="toggleBlogStatus(${blog.id}, ${blog.status ? 0 : 1})" 
                            title="${blog.status ? 'Taslağa Al' : 'Yayınla'}">
                        <i class="bi bi-${blog.status ? 'pause' : 'play'}-circle"></i>
                    </button>
                    <button class="quick-action-btn danger" onclick="deleteBlog(${blog.id})" title="Sil">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="blog-content">
                <div class="blog-meta">
                    <div class="blog-meta-item">
                        <i class="bi bi-person"></i>
                        <span>${blog.author}</span>
                    </div>
                    <div class="blog-meta-item">
                        <i class="bi bi-calendar3"></i>
                        <span>${new Date(blog.date).toLocaleDateString('tr-TR')}</span>
                    </div>
                    ${viewsHtml}
                </div>
                <h3 class="blog-title">${blog.title}</h3>
                <p class="blog-excerpt">${excerpt}</p>
            </div>
        </div>
    `;
}

// Update blog stats
function updateBlogStats() {
    fetch('/admin/blogs/stats', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('totalBlogs').textContent = data.total;
        document.getElementById('activeBlogs').textContent = data.active;
        document.getElementById('inactiveBlogs').textContent = data.inactive;
        document.getElementById('totalViews').textContent = data.views.toLocaleString('tr-TR');
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