<!-- View Product Modal (Dynamic) -->
<div class="modal fade" id="viewProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-box-seam-fill me-2"></i>
                    Ürün Detayları
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <!-- Ürün Görselleri -->
                <div class="form-section">
                    <h6 class="form-section-title">
                        <i class="bi bi-images"></i>
                        Ürün Görselleri
                    </h6>
                    <div class="product-images-container">
                        <div id="viewImagesContainer">
                            <!-- Images will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Ürün Bilgileri -->
                <div class="form-section">
                    <h6 class="form-section-title">
                        <i class="bi bi-box-seam"></i>
                        Temel Bilgiler
                    </h6>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label">Ürün Adı</label>
                                <div class="form-control-static" id="viewProductName"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">SKU</label>
                                <div class="form-control-static" id="viewSku"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Kategori</label>
                                <div class="form-control-static" id="viewCategory"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Mağaza</label>
                                <div class="form-control-static" id="viewStoreName"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fiyat ve Stok -->
                <div class="form-section">
                    <h6 class="form-section-title">
                        <i class="bi bi-currency-dollar"></i>
                        Fiyat ve Stok Bilgileri
                    </h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Normal Fiyat</label>
                                <div class="form-control-static" id="viewPrice"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">İndirimli Fiyat</label>
                                <div class="form-control-static" id="viewDiscountPrice"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Stok Durumu</label>
                                <div class="form-control-static" id="viewStock"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ek Bilgiler -->
                <div class="form-section">
                    <h6 class="form-section-title">
                        <i class="bi bi-info-circle"></i>
                        Ek Bilgiler
                    </h6>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Açıklama</label>
                                <div class="form-control-static" id="viewDescription"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Durum</label>
                                <div class="form-control-static" id="viewStatus"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Eklenme Tarihi</label>
                                <div class="form-control-static" id="viewCreatedAt"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="viewFeaturedInfo" style="display:none;" class="info-message">
                    <i class="bi bi-star-fill"></i>
                    <div class="info-message-content">
                        <div class="info-message-title">Öne Çıkan Ürün</div>
                        <div class="info-message-text">
                            Bu ürün öne çıkan ürünler arasında gösterilmektedir.
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>
                    Yeni Ürün Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">×</button>
            </div>
            <form id="addProductForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Sol Kolon -->
                        <div class="col-md-6">
                            <!-- Temel Bilgiler -->
                            <div class="form-section">
                                <h6 class="form-section-title">
                                    <i class="bi bi-info-circle"></i>
                                    Temel Bilgiler
                                </h6>
                                <div class="form-group">
                                    <label class="form-label">Ürün Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">SKU Kodu <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="sku" required>
                                    <small class="text-muted">Benzersiz ürün kodu</small>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-control" name="category_id" required>
                                        <option value="">Kategori Seçin</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Açıklama <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="description" rows="4" required></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Sağ Kolon -->
                        <div class="col-md-6">
                            <!-- Fiyat ve Stok -->
                            <div class="form-section">
                                <h6 class="form-section-title">
                                    <i class="bi bi-currency-dollar"></i>
                                    Fiyat ve Stok
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Fiyat (₺) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="price" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">İndirimli Fiyat (₺)</label>
                                            <input type="number" class="form-control" name="discount_price" step="0.01" min="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Stok Miktarı <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="stock" min="0" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Durum <span class="text-danger">*</span></label>
                                            <select class="form-control" name="status" required>
                                                <option value="active">Aktif</option>
                                                <option value="pending">Onay Bekliyor</option>
                                                <option value="inactive">Pasif</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Görseller -->
                            <div class="form-section">
                                <h6 class="form-section-title">
                                    <i class="bi bi-image"></i>
                                    Görseller
                                </h6>
                                <div class="form-group">
                                    <label class="form-label">Ana Görsel</label>
                                    <input type="file" class="form-control" name="image" accept="image/*">
                                    <small class="text-muted">Max: 2MB, Format: JPG, PNG, GIF</small>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Galeri Görselleri</label>
                                    <input type="file" class="form-control" name="gallery_images[]" accept="image/*" multiple>
                                    <small class="text-muted">Birden fazla görsel seçebilirsiniz</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Ürün Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal - Glass Morphism Style -->
<div class="modal fade glass-modal" id="editProductModal" tabindex="-1" data-bs-backdrop="true" data-bs-keyboard="true" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content glass-card">
            <div class="modal-header glass-header">
                <h5 class="modal-title gradient-text" id="editProductModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>
                    Ürünü Düzenle
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editProductForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="editProductId" name="product_id">
                <div class="modal-body">
                    <div class="product-edit-info mb-3">
                        <span class="badge glass-badge">
                            <i class="bi bi-box-seam me-1"></i>
                            <span id="editProductNameBadge"></span>
                        </span>
                    </div>
                    
                    <div class="row g-4">
                        <!-- Sol Kolon -->
                        <div class="col-lg-6">
                            <!-- Temel Bilgiler -->
                            <div class="glass-section">
                                <h6 class="section-title glass-title">
                                    <i class="bi bi-info-circle-fill"></i>
                                    Temel Bilgiler
                                </h6>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control glass-input" id="editName" name="name" placeholder="Ürün Adı" required>
                                    <label for="editName">Ürün Adı <span class="text-danger">*</span></label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control glass-input" id="editSku" name="sku" placeholder="SKU" required>
                                    <label for="editSku">SKU Kodu <span class="text-danger">*</span></label>
                                </div>
                                <div class="form-floating mb-3">
                                    <select class="form-select glass-select" id="editCategoryId" name="category_id" required>
                                        <option value="">Seçiniz...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="editCategoryId">Kategori <span class="text-danger">*</span></label>
                                </div>
                                <div class="form-floating">
                                    <textarea class="form-control glass-input" id="editDescription" name="description" placeholder="Açıklama" style="height: 100px" required></textarea>
                                    <label for="editDescription">Açıklama <span class="text-danger">*</span></label>
                                </div>
                            </div>
                        </div>

                        <!-- Sağ Kolon -->
                        <div class="col-lg-6">
                            <!-- Fiyat ve Stok -->
                            <div class="glass-section mb-4">
                                <h6 class="section-title glass-title">
                                    <i class="bi bi-currency-dollar"></i>
                                    Fiyat ve Stok
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="number" class="form-control glass-input" id="editPrice" name="price" step="0.01" min="0" placeholder="0.00" required>
                                            <label for="editPrice">Fiyat (₺) <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="number" class="form-control glass-input" id="editDiscountPrice" name="discount_price" step="0.01" min="0" placeholder="0.00">
                                            <label for="editDiscountPrice">İndirimli Fiyat (₺)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="number" class="form-control glass-input" id="editStock" name="stock" min="0" placeholder="0" required>
                                            <label for="editStock">Stok Miktarı <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select glass-select" id="editStatus" name="status" required>
                                                <option value="active">Aktif</option>
                                                <option value="pending">Onay Bekliyor</option>
                                                <option value="inactive">Pasif</option>
                                            </select>
                                            <label for="editStatus">Durum <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Görseller -->
                            <div class="glass-section">
                                <h6 class="section-title glass-title">
                                    <i class="bi bi-image-fill"></i>
                                    Ürün Görseli
                                </h6>
                                <div class="current-image-container" id="currentProductImage">
                                    <!-- Current image will be shown here -->
                                </div>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="form-control glass-file-input" id="editImage" name="image" accept="image/*">
                                    <label for="editImage" class="file-upload-label">
                                        <i class="bi bi-cloud-upload"></i>
                                        <span>Yeni görsel yükle</span>
                                    </label>
                                    <small class="text-muted d-block mt-2">Max: 2MB • JPG, PNG, GIF</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer glass-footer">
                    <button type="button" class="btn btn-glass-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>
                        İptal
                    </button>
                    <button type="submit" class="btn btn-glass-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Değişiklikleri Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

class ProductManager {
    constructor() {
        this.currentEditProductId = null;
        this.currentViewProductId = null;
        this.initEventListeners();
    }

    initEventListeners() {
        
        // Add Product Form Submit
        const addForm = document.getElementById('addProductForm');
        if (addForm) {
            addForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.createProduct(e.target);
            });
        }
        
        // Edit Product Form Submit
        const editForm = document.getElementById('editProductForm');
        if (editForm) {
            editForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.updateProduct(e.target);
            });
        }

        // Delete buttons - Handle more carefully
        try {
            document.querySelectorAll('[onclick^="deleteProduct"]').forEach(btn => {
                const onclickAttr = btn.getAttribute('onclick');
                if (onclickAttr) {
                    const match = onclickAttr.match(/\d+/);
                    if (match) {
                        const productId = match[0];
                        btn.onclick = null;
                        btn.addEventListener('click', () => this.deleteProduct(productId));
                    }
                }
            });
        } catch (e) {
            console.error('Error setting up delete buttons:', e);
        }
    }

    // Create Product
    async createProduct(form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Ekleniyor...';

            const response = await fetch('/admin/products/ajax-store', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
                form.reset();
                this.showAlert('success', data.message || 'Ürün başarıyla eklendi!');
                
                // Sayfayı yenile
                setTimeout(() => location.reload(), 1500);
            } else {
                if (data.errors) {
                    this.handleErrors(data.errors);
                    this.showAlert('danger', 'Lütfen formdaki hataları düzeltin.');
                } else {
                    this.showAlert('danger', data.message || 'Ürün eklenirken bir hata oluştu!');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            this.showAlert('danger', 'Beklenmeyen bir hata oluştu!');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Ürün Ekle';
        }
    }

    // View Product Details
    async viewProduct(productId) {
        try {
            const response = await fetch(`/admin/products/${productId}/edit-ajax`, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                this.currentViewProductId = productId;
                window.currentViewProductId = productId; // Global access for modal buttons
                this.populateViewModal(data.product);
                
                const viewModal = new bootstrap.Modal(document.getElementById('viewProductModal'));
                viewModal.show();
            } else {
                this.showAlert('danger', data.message || 'Ürün bilgileri yüklenemedi!');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showAlert('danger', 'Beklenmeyen bir hata oluştu!');
        }
    }

    // Populate View Modal
    populateViewModal(product) {
        // Görseller
        const imagesContainer = document.getElementById('viewImagesContainer');
        if (product.image || (product.images && product.images.length > 0)) {
            let imagesHtml = '<div class="product-images-list">';
            
            // Ana görsel
            if (product.image) {
                const mainImgSrc = `/storage/${product.image}`;
                imagesHtml += `
                    <div class="product-image-item main-product-image" onclick="window.showImageLightbox('${mainImgSrc}', '${product.name} - Ana Görsel')">
                        <div class="image-label">Ana Görsel</div>
                        <img src="${mainImgSrc}" alt="${product.name}" class="img-fluid">
                        <div class="image-zoom-hint">
                            <i class="bi bi-zoom-in"></i> Büyütmek için tıklayın
                        </div>
                    </div>
                `;
            }
            
            // Galeri görselleri
            if (product.images && product.images.length > 0) {
                imagesHtml += `
                    <div class="gallery-images-section">
                        <div class="image-label">Galeri Görselleri (${product.images.length})</div>
                        <div class="gallery-images-list">
                `;
                product.images.forEach((img, index) => {
                    const imgSrc = `/storage/${img.image || img}`;
                    imagesHtml += `
                        <div class="product-image-item" onclick="window.showImageLightbox('${imgSrc}', '${product.name} - ${index + 1}')">
                            <div class="image-number">${index + 1}</div>
                            <img src="${imgSrc}" alt="${product.name} - ${index + 1}" class="img-fluid">
                        </div>
                    `;
                });
                imagesHtml += '</div></div>';
            }
            
            imagesHtml += '</div>';
            imagesContainer.innerHTML = imagesHtml;
        } else {
            imagesContainer.innerHTML = `
                <div class="no-image-placeholder">
                    <i class="bi bi-image"></i>
                    <p>Bu ürün için görsel bulunmuyor</p>
                </div>
            `;
        }
        
        // Temel bilgiler
        document.getElementById('viewProductName').textContent = product.name;
        document.getElementById('viewSku').textContent = product.sku || '-';
        document.getElementById('viewCategory').textContent = product.category?.name || 'Kategori Yok';
        document.getElementById('viewStoreName').textContent = product.store?.name || 'Mağaza Yok';
        
        // Fiyat bilgileri
        document.getElementById('viewPrice').textContent = `₺${this.formatPrice(product.price)}`;
        
        if (product.discount_price && product.price > product.discount_price) {
            const discountPercent = Math.round(((product.price - product.discount_price) / product.price) * 100);
            document.getElementById('viewDiscountPrice').innerHTML = `
                ₺${this.formatPrice(product.discount_price)}
                <span class="badge bg-danger ms-2">-${discountPercent}%</span>
            `;
        } else {
            document.getElementById('viewDiscountPrice').textContent = '-';
        }
        
        // Stok durumu
        const stock = product.stock || 0;
        const stockClass = stock > 10 ? 'in-stock' : (stock > 0 ? 'low-stock' : 'out-of-stock');
        document.getElementById('viewStock').innerHTML = `
            <span class="stock-badge ${stockClass}">
                ${stock} adet
            </span>
        `;
        
        // Açıklama
        document.getElementById('viewDescription').textContent = product.description || 'Açıklama bulunmuyor';
        
        // Durum
        const statusHtml = product.status === 'active' 
            ? '<span class="status-badge active"><i class="bi bi-check-circle me-1"></i>Aktif</span>'
            : product.status === 'pending'
            ? '<span class="status-badge pending"><i class="bi bi-clock me-1"></i>Onay Bekliyor</span>'
            : '<span class="status-badge inactive"><i class="bi bi-x-circle me-1"></i>Pasif</span>';
        document.getElementById('viewStatus').innerHTML = statusHtml;
        
        // Eklenme tarihi
        if (product.created_at) {
            document.getElementById('viewCreatedAt').textContent = new Date(product.created_at).toLocaleString('tr-TR');
        }
        
        // Öne çıkan ürün kontrolü
        if (product.is_featured) {
            document.getElementById('viewFeaturedInfo').style.display = 'block';
        } else {
            document.getElementById('viewFeaturedInfo').style.display = 'none';
        }
    }

    // Format price helper
    formatPrice(price) {
        return new Intl.NumberFormat('tr-TR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(price);
    }

    // Load Product for Edit
    async loadProductForEdit(productId) {
        console.log('loadProductForEdit called with ID:', productId);
        try {
            const response = await fetch(`/admin/products/${productId}/edit-ajax`, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();
            console.log('Product data received:', data);

            if (response.ok && data.success) {
                this.currentEditProductId = productId;
                this.populateEditForm(data.product);
                
                // Manual modal show approach
                const modalElement = document.getElementById('editProductModal');
                console.log('Modal element found:', modalElement);
                
                if (!modalElement) {
                    console.error('Edit modal element not found!');
                    return;
                }
                
                // Clean up any existing backdrops
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                
                // Reset modal classes
                modalElement.classList.remove('show');
                modalElement.style.display = 'none';
                modalElement.setAttribute('aria-hidden', 'true');
                modalElement.removeAttribute('aria-modal');
                modalElement.removeAttribute('role');
                
                // Force a reflow
                void modalElement.offsetHeight;
                
                // Show modal manually
                setTimeout(() => {
                    // Add backdrop
                    const backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade';
                    document.body.appendChild(backdrop);
                    
                    // Force reflow for animation
                    void backdrop.offsetHeight;
                    backdrop.classList.add('show');
                    
                    // Show modal
                    modalElement.style.display = 'block';
                    modalElement.setAttribute('aria-modal', 'true');
                    modalElement.setAttribute('role', 'dialog');
                    modalElement.classList.add('show');
                    modalElement.setAttribute('aria-hidden', 'false');
                    
                    // Add modal-open to body
                    document.body.classList.add('modal-open');
                    document.body.style.overflow = 'hidden';
                    
                    // Focus management
                    const firstInput = modalElement.querySelector('input, select, textarea');
                    if (firstInput) {
                        firstInput.focus();
                    }
                    
                    console.log('Modal shown manually');
                }, 10);
                
                // Setup close handlers
                this.setupModalCloseHandlers(modalElement);
                
            } else {
                this.showAlert('danger', data.message || 'Ürün bilgileri yüklenemedi!');
            }
        } catch (error) {
            console.error('Error in loadProductForEdit:', error);
            this.showAlert('danger', 'Beklenmeyen bir hata oluştu!');
        }
    }
    
    // Setup modal close handlers
    setupModalCloseHandlers(modalElement) {
        // Close button handler
        const closeButtons = modalElement.querySelectorAll('[data-bs-dismiss="modal"]');
        closeButtons.forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                this.closeEditModal();
            };
        });
        
        // Backdrop click handler
        modalElement.onclick = (e) => {
            if (e.target === modalElement) {
                this.closeEditModal();
            }
        };
        
        // Escape key handler
        const escHandler = (e) => {
            if (e.key === 'Escape' && modalElement.classList.contains('show')) {
                this.closeEditModal();
                document.removeEventListener('keydown', escHandler);
            }
        };
        document.addEventListener('keydown', escHandler);
    }

    // Close Edit Modal
    closeEditModal() {
        const modalElement = document.getElementById('editProductModal');
        
        if (modalElement) {
            // Remove show class and hide modal
            modalElement.classList.remove('show');
            modalElement.style.display = 'none';
            modalElement.setAttribute('aria-hidden', 'true');
            modalElement.removeAttribute('aria-modal');
            modalElement.removeAttribute('role');
        }
        
        // Remove backdrop
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        
        // Remove modal-open from body
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        console.log('Edit modal closed');
    }
    
    // Update Product
    async updateProduct(form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Güncelleniyor...';

            const response = await fetch(`/admin/products/${this.currentEditProductId}/ajax-update`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Close modal using our custom method
                this.closeEditModal();
                this.showAlert('success', data.message || 'Ürün başarıyla güncellendi!');
                
                // Sayfayı yenile
                setTimeout(() => location.reload(), 1500);
            } else {
                if (data.errors) {
                    this.handleErrors(data.errors);
                    this.showAlert('danger', 'Lütfen formdaki hataları düzeltin.');
                } else {
                    this.showAlert('danger', data.message || 'Ürün güncellenirken bir hata oluştu!');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            this.showAlert('danger', 'Beklenmeyen bir hata oluştu!');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Değişiklikleri Kaydet';
        }
    }

    // Delete Product
    async deleteProduct(productId) {
        if (!confirm('Bu ürünü silmek istediğinizden emin misiniz?')) {
            return;
        }

        try {
            const response = await fetch(`/admin/products/${productId}/ajax-delete`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                this.showAlert('success', data.message || 'Ürün başarıyla silindi!');
                
                // Sayfayı yenile
                setTimeout(() => location.reload(), 1500);
            } else {
                this.showAlert('danger', data.message || 'Ürün silinemedi!');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showAlert('danger', 'Beklenmeyen bir hata oluştu!');
        }
    }

    // Populate Edit Form
    populateEditForm(product) {
        document.getElementById('editProductNameBadge').textContent = product.name;
        document.getElementById('editProductId').value = product.id;
        document.getElementById('editName').value = product.name || '';
        document.getElementById('editSku').value = product.sku || '';
        document.getElementById('editCategoryId').value = product.category_id || '';
        document.getElementById('editDescription').value = product.description || '';
        document.getElementById('editPrice').value = product.price || '';
        document.getElementById('editDiscountPrice').value = product.discount_price || '';
        document.getElementById('editStock').value = product.stock || '';
        document.getElementById('editStatus').value = product.status || 'active';
        
        // Show current image
        const imageContainer = document.getElementById('currentProductImage');
        if (product.image) {
            imageContainer.innerHTML = `
                <div class="current-image-preview">
                    <img src="/storage/${product.image}" alt="${product.name}" class="img-fluid rounded">
                    <div class="current-image-label">Mevcut Görsel</div>
                </div>
            `;
        } else {
            imageContainer.innerHTML = `
                <div class="no-image-placeholder">
                    <i class="bi bi-image"></i>
                    <p>Görsel yok</p>
                </div>
            `;
        }
    }

    // Handle Validation Errors
    handleErrors(errors) {
        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.remove();
        });
        
        // Show new errors
        Object.keys(errors).forEach(field => {
            const activeModal = document.querySelector('.modal.show');
            let input = null;
            
            if (activeModal) {
                input = activeModal.querySelector(`[name="${field}"]`);
            }
            
            if (input) {
                input.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = errors[field][0];
                input.parentElement.appendChild(feedback);
            }
        });
    }

    // Show Alert
    showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999;" role="alert">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}-fill me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', alertHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            document.querySelector('.alert')?.remove();
        }, 5000);
    }
}

// Initialize Product Manager immediately
try {
    window.productManager = new ProductManager();
} catch (error) {
    console.error('Failed to initialize ProductManager:', error);
    alert('ProductManager başlatılamadı: ' + error.message);
}

// Override placeholder functions with real implementations
window.viewProduct = function(productId) {
    if (window.productManager) {
        window.productManager.viewProduct(productId);
    } else {
        // Try to reinitialize
        try {
            window.productManager = new ProductManager();
            window.productManager.viewProduct(productId);
        } catch (e) {
            console.error('Failed to initialize ProductManager:', e);
        }
    }
};

window.editProduct = function(productId) {
    if (window.productManager) {
        window.productManager.loadProductForEdit(productId);
    } else {
        // Try to reinitialize
        try {
            window.productManager = new ProductManager();
            window.productManager.loadProductForEdit(productId);
        } catch (e) {
            console.error('Failed to initialize ProductManager:', e);
        }
    }
};

window.loadProductForEdit = function(productId) {
    if (window.productManager) {
        window.productManager.loadProductForEdit(productId);
    } else {
        console.error('ProductManager is undefined!');
    }
};

window.deleteProduct = function(productId) {
    if (window.productManager) {
        window.productManager.deleteProduct(productId);
    } else {
        console.error('ProductManager is undefined!');
    }
};

// Image Lightbox function
window.showImageLightbox = function(imageSrc, title) {
    // Create lightbox modal if it doesn't exist
    let lightbox = document.getElementById('imageLightboxModal');
    if (!lightbox) {
        const lightboxHtml = `
            <div class="modal fade" id="imageLightboxModal" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content bg-transparent border-0">
                        <div class="modal-header border-0">
                            <h5 class="modal-title text-white" id="lightboxTitle"></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center p-0">
                            <img id="lightboxImage" src="" alt="" class="img-fluid" style="max-height: 80vh; object-fit: contain;">
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', lightboxHtml);
        lightbox = document.getElementById('imageLightboxModal');
    }
    
    // Set image and title
    document.getElementById('lightboxImage').src = imageSrc;
    document.getElementById('lightboxImage').alt = title;
    document.getElementById('lightboxTitle').textContent = title;
    
    // Show modal
    const modal = new bootstrap.Modal(lightbox);
    modal.show();
};
</script>

<style>
.page-header-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.page-header-left .page-title {
    font-size: 28px;
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 8px;
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin: 0;
}

.breadcrumb-item {
    font-size: 14px;
}

.breadcrumb-item a {
    color: #6B7280;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    color: #A90000;
}

.breadcrumb-item.active {
    color: #9CA3AF;
}

.btn-primary {
    background: linear-gradient(135deg, #A90000, #C1121F);
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(169, 0, 0, 0.2);
}

.form-section {
    background: rgba(240, 248, 255, 0.3);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.form-section-title {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-section-title i {
    color: #A90000;
}

.form-group {
    margin-bottom: 15px;
}

.form-label {
    display: block;
    font-weight: 500;
    color: #374151;
    margin-bottom: 5px;
    font-size: 13px;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    background: rgba(255, 255, 255, 0.8);
    border: 2px solid rgba(0, 0, 0, 0.08);
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    background: white;
    border-color: #A90000;
    box-shadow: 0 0 0 4px rgba(169, 0, 0, 0.1);
}

.invalid-feedback {
    display: block;
    color: #EF4444;
    font-size: 12px;
    margin-top: 4px;
}

.is-invalid {
    border-color: #EF4444 !important;
}

/* View Product Modal Styles */
.bg-gradient {
    background: linear-gradient(135deg, #A90000, #C1121F);
}

.modal-title.text-white {
    color: white !important;
}

.btn-close-white {
    filter: invert(1);
}

.main-image-container {
    position: relative;
    background: #f8f9fa;
    border-radius: 12px;
    overflow: hidden;
    min-height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.main-image-container img {
    max-width: 100%;
    max-height: 400px;
    object-fit: contain;
}

.discount-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: linear-gradient(135deg, #EF4444, #DC2626);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.gallery-images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 10px;
}

.gallery-thumb {
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
    aspect-ratio: 1;
    cursor: pointer;
    transition: all 0.3s ease;
}

.gallery-thumb:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.gallery-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.category-badge {
    display: inline-block;
    background: rgba(169, 0, 0, 0.1);
    color: #A90000;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.product-title {
    font-size: 24px;
    font-weight: 700;
    color: #1F2937;
    margin: 0;
}

.old-price {
    text-decoration: line-through;
    color: #9CA3AF;
    font-size: 18px;
    margin-right: 10px;
}

.current-price {
    color: #A90000;
    font-size: 28px;
    font-weight: 700;
}

.info-box {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.info-label {
    display: block;
    font-size: 12px;
    color: #6B7280;
    margin-bottom: 8px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.status-badge.active {
    background: rgba(16, 185, 129, 0.1);
    color: #10B981;
}

.status-badge.pending {
    background: rgba(245, 158, 11, 0.1);
    color: #F59E0B;
}

.status-badge.inactive {
    background: rgba(239, 68, 68, 0.1);
    color: #EF4444;
}

.stock-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.stock-count {
    font-size: 16px;
    font-weight: 600;
    color: #1F2937;
}

.stock-progress {
    flex: 1;
    height: 8px;
    background: #E5E7EB;
    border-radius: 4px;
    overflow: hidden;
}

.stock-progress-fill {
    height: 100%;
    transition: all 0.3s ease;
}

.stock-progress-fill.success {
    background: #10B981;
}

.stock-progress-fill.warning {
    background: #F59E0B;
}

.stock-progress-fill.danger {
    background: #EF4444;
}

.section-title {
    font-size: 16px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-title i {
    color: #A90000;
}

.description-content {
    color: #4B5563;
    line-height: 1.6;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.store-details {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.store-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.stats-grid {
    gap: 15px;
}

.stat-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    transition: all 0.3s ease;
}

.stat-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.stat-box i {
    font-size: 24px;
    color: #A90000;
    display: block;
    margin-bottom: 10px;
}

.stat-value {
    display: block;
    font-size: 20px;
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 12px;
    color: #6B7280;
    text-transform: uppercase;
}

.additional-info {
    padding-top: 20px;
    border-top: 1px solid #E5E7EB;
}

/* Product images styles */
.product-images-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    min-height: 200px;
}

.product-images-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.product-image-item {
    position: relative;
    background: white;
    border-radius: 8px;
    padding: 10px;
    border: 1px solid #e5e7eb;
    text-align: center;
    transition: all 0.3s ease;
    cursor: zoom-in;
}

.product-image-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.main-product-image {
    border: 2px solid #A90000;
}

.product-image-item img {
    max-width: 100%;
    max-height: 300px;
    object-fit: contain;
    border-radius: 4px;
    transition: transform 0.3s ease;
}

.product-image-item:hover img {
    transform: scale(1.05);
}

.image-zoom-hint {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-image-item:hover .image-zoom-hint {
    opacity: 1;
}

.image-label {
    font-size: 12px;
    font-weight: 600;
    color: #6B7280;
    text-transform: uppercase;
    margin-bottom: 10px;
    padding: 4px 8px;
    background: rgba(169, 0, 0, 0.05);
    border-radius: 4px;
    display: inline-block;
}

.image-number {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #A90000;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
}

.gallery-images-section {
    margin-top: 10px;
}

.gallery-images-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 10px;
}

.no-image-placeholder {
    text-align: center;
    padding: 40px;
    color: #9ca3af;
}

.no-image-placeholder i {
    font-size: 48px;
    display: block;
    margin-bottom: 10px;
}

.stock-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.stock-badge.in-stock {
    background: rgba(16, 185, 129, 0.1);
    color: #10B981;
}

.stock-badge.low-stock {
    background: rgba(245, 158, 11, 0.1);
    color: #F59E0B;
}

.stock-badge.out-of-stock {
    background: rgba(239, 68, 68, 0.1);
    color: #EF4444;
}

.info-message {
    background: rgba(139, 92, 246, 0.1);
    border-left: 4px solid #8B5CF6;
    padding: 15px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 15px;
    margin-top: 20px;
}

.info-message i {
    color: #8B5CF6;
    font-size: 24px;
}

.info-message-title {
    font-weight: 600;
    color: #374151;
    margin-bottom: 4px;
}

.info-message-text {
    color: #6B7280;
    font-size: 13px;
}

/* Edit Modal specific */
#editProductModal {
    position: fixed !important;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    overflow-y: auto;
    z-index: 1050 !important;
}

#editProductModal.show {
    display: block !important;
    opacity: 1 !important;
}

#editProductModal .modal-dialog {
    position: relative;
    width: auto;
    max-width: 900px;
    margin: 1.75rem auto;
    pointer-events: none;
}

#editProductModal .modal-content {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
    pointer-events: auto;
    background-color: #fff;
    background-clip: padding-box;
    border: none;
    border-radius: 16px;
    outline: 0;
}

#editProductModal .modal-header {
    background: linear-gradient(135deg, #A90000, #C1121F);
    color: white;
    border-bottom: none;
    padding: 20px 30px;
}

#editProductModal .modal-body {
    padding: 30px;
}

/* Add Modal specific */
#addProductModal .modal-dialog {
    max-width: 900px;
}

#addProductModal .modal-content {
    border: none;
    border-radius: 16px;
}

#addProductModal .modal-header {
    background: linear-gradient(135deg, #10B981, #059669);
    color: white;
    border-bottom: none;
    padding: 20px 30px;
}

#addProductModal .modal-body {
    padding: 30px;
}

/* Fix modal backdrop */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
}

/* Fix modal animations */
.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
}

.modal.show .modal-dialog {
    transform: none;
}

/* Lightbox Modal Styles */
#imageLightboxModal {
    z-index: 9999;
}

#imageLightboxModal .modal-backdrop {
    background-color: rgba(0, 0, 0, 0.9);
    z-index: 9998;
}

#imageLightboxModal .modal-content {
    background: transparent !important;
    box-shadow: none;
}

#imageLightboxModal .modal-header {
    position: absolute;
    top: 0;
    right: 0;
    z-index: 1;
    padding: 20px;
}

#imageLightboxModal .modal-body {
    padding: 20px;
}

#imageLightboxModal img {
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
}

/* Glass Morphism Styles for Edit Modal */
#editProductModal {
    z-index: 1055 !important;
}

#editProductModal.show {
    display: block !important;
}

.glass-modal .modal-dialog {
    z-index: 1060 !important;
}

.glass-modal .modal-content {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    z-index: 1061 !important;
}

.glass-card {
    background: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    border-radius: 24px !important;
}

.glass-header {
    background: linear-gradient(135deg, rgba(169, 0, 0, 0.95), rgba(193, 18, 31, 0.95)) !important;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    border-radius: 24px 24px 0 0 !important;
    padding: 24px !important;
}

.gradient-text {
    background: linear-gradient(135deg, #ffffff, #f0f0f0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 700;
    font-size: 20px;
}

.glass-badge {
    background: rgba(169, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(169, 0, 0, 0.2);
    color: #A90000;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
}

.glass-section {
    background: rgba(240, 248, 255, 0.5);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 20px;
}

.glass-title {
    color: #1F2937;
    font-weight: 600;
    font-size: 15px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.glass-title i {
    color: #A90000;
}

.glass-input,
.glass-select {
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 2px solid rgba(0, 0, 0, 0.05) !important;
    border-radius: 12px !important;
    transition: all 0.3s ease !important;
}

.glass-input:focus,
.glass-select:focus {
    background: rgba(255, 255, 255, 1) !important;
    border-color: #A90000 !important;
    box-shadow: 0 0 0 4px rgba(169, 0, 0, 0.1) !important;
}

.form-floating > label {
    color: #6B7280;
}

.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label,
.form-floating > .form-select ~ label {
    color: #A90000;
    font-weight: 500;
}

.glass-file-input {
    display: none;
}

.file-upload-wrapper {
    position: relative;
}

.file-upload-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.8);
    border: 2px dashed rgba(169, 0, 0, 0.3);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.file-upload-label:hover {
    background: rgba(255, 255, 255, 1);
    border-color: #A90000;
    transform: translateY(-2px);
}

.file-upload-label i {
    font-size: 24px;
    color: #A90000;
}

.current-image-preview {
    position: relative;
    margin-bottom: 15px;
}

.current-image-preview img {
    width: 100%;
    max-height: 200px;
    object-fit: cover;
    border-radius: 12px;
    border: 2px solid rgba(169, 0, 0, 0.1);
}

.current-image-label {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(169, 0, 0, 0.9);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.glass-footer {
    background: rgba(249, 250, 251, 0.8);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    border-radius: 0 0 24px 24px;
    padding: 20px 24px;
}

.btn-glass-secondary {
    background: rgba(107, 114, 128, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(107, 114, 128, 0.2);
    color: #6B7280;
    padding: 10px 20px;
    border-radius: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-glass-secondary:hover {
    background: rgba(107, 114, 128, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.btn-glass-primary {
    background: linear-gradient(135deg, rgba(169, 0, 0, 0.9), rgba(193, 18, 31, 0.9));
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(169, 0, 0, 0.3);
    color: white;
    padding: 10px 20px;
    border-radius: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-glass-primary:hover {
    background: linear-gradient(135deg, #A90000, #C1121F);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(169, 0, 0, 0.3);
    color: white;
}

.btn-close-white {
    filter: brightness(0) invert(1);
    opacity: 0.8;
}

.btn-close-white:hover {
    opacity: 1;
}

.product-edit-info {
    padding: 15px;
    background: rgba(169, 0, 0, 0.05);
    border-radius: 12px;
    border-left: 4px solid #A90000;
}

/* Modal Override Fixes */
.modal-backdrop {
    z-index: 1050 !important;
}

.modal-backdrop.show {
    opacity: 0.5 !important;
}

#editProductModal.fade {
    transition: opacity 0.15s linear;
}

#editProductModal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
    transform: translate(0, -50px);
}

#editProductModal.show .modal-dialog {
    transform: none;
}

/* Force modal visibility when shown */
body.modal-open #editProductModal.show {
    display: block !important;
    padding-right: 0 !important;
}

/* Ensure dialog is centered */
#editProductModal .modal-dialog {
    display: flex;
    align-items: center;
    min-height: calc(100% - 3.5rem);
}

/* Remove any conflicting styles */
#editProductModal.modal {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1055;
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    overflow-y: auto;
    outline: 0;
}
</style>