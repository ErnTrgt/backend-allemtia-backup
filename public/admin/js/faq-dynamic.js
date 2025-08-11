// FAQ Dynamic Management System
class FaqManager {
    constructor() {
        this.initializeElements();
        this.bindEvents();
        this.initializeSearch();
        this.updateStats();
    }

    initializeElements() {
        this.searchInput = document.getElementById('faqSearch');
        this.faqContainer = document.querySelector('.faq-accordion');
        this.addModal = new bootstrap.Modal(document.getElementById('addFaqModal'));
        this.editModal = new bootstrap.Modal(document.getElementById('editFaqModal'));
        this.currentEditId = null;
    }

    bindEvents() {
        // Form submissions
        document.getElementById('addFaqForm').addEventListener('submit', (e) => this.handleAdd(e));
        document.getElementById('editFaqForm').addEventListener('submit', (e) => this.handleEdit(e));

        // Preview functionality
        this.setupLivePreview();

        // Character counters
        this.setupCharacterCounters();
    }

    initializeSearch() {
        let searchTimer;
        this.searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimer);
            const query = e.target.value.toLowerCase();
            
            searchTimer = setTimeout(() => {
                const items = document.querySelectorAll('.faq-item');
                
                items.forEach(item => {
                    const question = item.dataset.question || '';
                    const content = item.dataset.content || '';
                    
                    if (question.includes(query) || content.includes(query)) {
                        item.style.display = '';
                        item.classList.remove('hidden');
                    } else {
                        item.style.display = 'none';
                        item.classList.add('hidden');
                    }
                });

                // Show empty state if no results
                const visibleItems = document.querySelectorAll('.faq-item:not(.hidden)');
                let emptyState = document.querySelector('.empty-search-state');
                
                if (visibleItems.length === 0 && query.length > 0) {
                    if (!emptyState) {
                        emptyState = this.createEmptySearchState();
                        this.faqContainer.appendChild(emptyState);
                    }
                    emptyState.style.display = 'block';
                } else if (emptyState) {
                    emptyState.style.display = 'none';
                }
            }, 300);
        });
    }

    createEmptySearchState() {
        const div = document.createElement('div');
        div.className = 'empty-search-state';
        div.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-search text-muted" style="font-size: 48px;"></i>
                <h5 class="mt-3 text-muted">Arama sonucu bulunamadı</h5>
                <p class="text-muted">Farklı kelimelerle tekrar deneyin</p>
            </div>
        `;
        return div;
    }

    async handleAdd(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        this.showLoadingToast('S.S.S ekleniyor...');
        
        try {
            const response = await fetch('/admin/faqs/store', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccessToast(data.message || 'S.S.S başarıyla eklendi!');
                this.addModal.hide();
                e.target.reset();
                this.addFaqToDOM(data.faq);
                this.updateStats();
            } else {
                this.showErrorToast(data.message || 'S.S.S eklenirken hata oluştu!');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showErrorToast('Bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }

    async handleEdit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const faqId = this.currentEditId;
        
        if (!faqId) return;
        
        this.showLoadingToast('S.S.S güncelleniyor...');
        
        try {
            const response = await fetch(`/admin/faqs/${faqId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-HTTP-Method-Override': 'PUT',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccessToast(data.message || 'S.S.S başarıyla güncellendi!');
                this.editModal.hide();
                this.updateFaqInDOM(data.faq);
                this.updateStats();
            } else {
                this.showErrorToast(data.message || 'S.S.S güncellenirken hata oluştu!');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showErrorToast('Bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }

    addFaqToDOM(faq) {
        // Remove empty state if exists
        const emptyState = document.querySelector('.empty-state');
        if (emptyState) {
            emptyState.remove();
        }

        const faqHtml = this.createFaqItemHTML(faq);
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = faqHtml;
        const newItem = tempDiv.firstElementChild;
        
        // Add animation class
        newItem.classList.add('faq-item-new');
        
        // Prepend to container
        this.faqContainer.insertBefore(newItem, this.faqContainer.firstChild);
        
        // Trigger animation
        setTimeout(() => {
            newItem.classList.remove('faq-item-new');
        }, 100);
    }

    updateFaqInDOM(faq) {
        const existingItem = document.querySelector(`.faq-item[data-faq-id="${faq.id}"]`);
        if (existingItem) {
            const newHtml = this.createFaqItemHTML(faq);
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = newHtml;
            const newItem = tempDiv.firstElementChild;
            
            // Add update animation
            existingItem.classList.add('faq-item-updating');
            
            setTimeout(() => {
                existingItem.replaceWith(newItem);
                newItem.classList.add('faq-item-updated');
                setTimeout(() => {
                    newItem.classList.remove('faq-item-updated');
                }, 2000);
            }, 300);
        }
    }

    createFaqItemHTML(faq) {
        const statusClass = faq.status ? 'active' : 'inactive';
        const statusIcon = faq.status ? 'check' : 'x';
        const statusText = faq.status ? 'Aktif' : 'Pasif';
        const toggleText = faq.status ? 'Pasifleştir' : 'Aktifleştir';
        const toggleIcon = faq.status ? 'pause' : 'play';
        
        return `
            <div class="faq-item" data-faq-id="${faq.id}" data-question="${faq.title.toLowerCase()}" data-content="${faq.content.toLowerCase()}">
                <div class="faq-question" onclick="faqManager.toggleFaq(this)">
                    <div class="faq-question-content">
                        <div class="faq-question-icon">
                            <i class="bi bi-patch-question"></i>
                        </div>
                        <div class="faq-question-text">
                            ${this.escapeHtml(faq.title)}
                        </div>
                    </div>
                    <div class="faq-question-meta">
                        <span class="faq-status ${statusClass}">
                            <i class="bi bi-${statusIcon}-circle"></i>
                            ${statusText}
                        </span>
                        <span class="faq-expand-icon">
                            <i class="bi bi-chevron-down"></i>
                        </span>
                    </div>
                </div>
                
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <div class="faq-answer-text">
                            ${faq.content}
                        </div>
                        
                        <div class="faq-actions">
                            <button class="action-btn edit" onclick="faqManager.editFaq(${faq.id})">
                                <i class="bi bi-pencil"></i>
                                Düzenle
                            </button>
                            
                            <button class="action-btn toggle" onclick="faqManager.toggleStatus(${faq.id})">
                                <i class="bi bi-${toggleIcon}"></i>
                                ${toggleText}
                            </button>
                            
                            <button class="action-btn delete" onclick="faqManager.deleteFaq(${faq.id})">
                                <i class="bi bi-trash"></i>
                                Sil
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    toggleFaq(element) {
        const faqItem = element.closest('.faq-item');
        faqItem.classList.toggle('expanded');
    }

    async editFaq(id) {
        this.showLoadingToast('S.S.S bilgileri yükleniyor...');
        
        try {
            const response = await fetch(`/admin/faqs/${id}/edit-ajax`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.currentEditId = id;
                
                // Populate form
                document.getElementById('editTitle').value = data.faq.title || '';
                document.getElementById('editContent').value = data.faq.content || '';
                document.getElementById('editCategory').value = data.faq.category || '';
                document.getElementById('editStatus').value = data.faq.status ? '1' : '0';
                
                // Update preview
                this.updateEditPreview();
                
                // Show modal
                this.editModal.show();
                
                // Hide loading toast
                this.hideToast();
            } else {
                this.showErrorToast(data.message || 'S.S.S bilgileri yüklenemedi!');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showErrorToast('Bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }

    async toggleStatus(id) {
        this.showLoadingToast('Durum değiştiriliyor...');
        
        try {
            const response = await fetch(`/admin/faqs/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-HTTP-Method-Override': 'PUT',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({})
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccessToast(data.message || 'Durum başarıyla değiştirildi!');
                this.updateFaqInDOM(data.faq);
                this.updateStats();
            } else {
                this.showErrorToast(data.message || 'Durum değiştirilemedi!');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showErrorToast('Bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }

    async deleteFaq(id) {
        if (!confirm('Bu S.S.S\'yi silmek istediğinizden emin misiniz?')) {
            return;
        }
        
        this.showLoadingToast('S.S.S siliniyor...');
        
        try {
            const response = await fetch(`/admin/faqs/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccessToast(data.message || 'S.S.S başarıyla silindi!');
                this.removeFaqFromDOM(id);
                this.updateStats();
            } else {
                this.showErrorToast(data.message || 'S.S.S silinemedi!');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showErrorToast('Bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }

    removeFaqFromDOM(id) {
        const item = document.querySelector(`.faq-item[data-faq-id="${id}"]`);
        if (item) {
            item.classList.add('faq-item-removing');
            setTimeout(() => {
                item.remove();
                
                // Check if container is empty
                const remainingItems = document.querySelectorAll('.faq-item');
                if (remainingItems.length === 0) {
                    this.showEmptyState();
                }
            }, 300);
        }
    }

    showEmptyState() {
        const emptyHtml = `
            <div class="empty-state">
                <i class="bi bi-question-circle empty-icon"></i>
                <h3 class="empty-title">Henüz S.S.S Yok</h3>
                <p class="empty-text">İlk S.S.S'nizi oluşturmak için yukarıdaki butonu kullanın.</p>
            </div>
        `;
        this.faqContainer.innerHTML = emptyHtml;
    }

    async updateStats() {
        try {
            const response = await fetch('/admin/faqs/stats', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                // Update stat values with animation
                this.animateValue('totalFaqs', data.stats.total);
                this.animateValue('activeFaqs', data.stats.active);
                this.animateValue('inactiveFaqs', data.stats.inactive);
                this.animateValue('totalCategories', data.stats.categories);
            }
        } catch (error) {
            console.error('Error updating stats:', error);
        }
    }

    animateValue(elementId, newValue) {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        const currentValue = parseInt(element.textContent) || 0;
        const increment = (newValue - currentValue) / 20;
        let current = currentValue;
        
        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= newValue) || (increment < 0 && current <= newValue)) {
                element.textContent = newValue;
                clearInterval(timer);
                
                // Add pulse animation
                element.parentElement.classList.add('stat-updated');
                setTimeout(() => {
                    element.parentElement.classList.remove('stat-updated');
                }, 1000);
            } else {
                element.textContent = Math.round(current);
            }
        }, 30);
    }

    setupLivePreview() {
        // Add modal preview
        const addTitle = document.getElementById('newQuestionInput');
        const addContent = document.getElementById('newAnswerInput');
        const addQuestionPreview = document.getElementById('newQuestionPreview');
        const addAnswerPreview = document.getElementById('newAnswerPreview');
        
        if (addTitle && addContent) {
            addTitle.addEventListener('input', () => {
                addQuestionPreview.textContent = addTitle.value || 'Soru yazıldıkça burada görünecek...';
                addQuestionPreview.classList.toggle('text-muted', !addTitle.value);
            });
            
            addContent.addEventListener('input', () => {
                const text = addContent.value || 'Cevap yazıldıkça burada görünecek...';
                const cleanText = text.replace(/<[^>]*>/g, '');
                const limitedText = cleanText.length > 150 ? cleanText.substring(0, 150) + '...' : cleanText;
                addAnswerPreview.textContent = limitedText;
                addAnswerPreview.classList.toggle('text-muted', !addContent.value);
            });
        }
        
        // Edit modal preview
        const editTitle = document.getElementById('editTitle');
        const editContent = document.getElementById('editContent');
        const editQuestionPreview = document.getElementById('editQuestionPreview');
        const editAnswerPreview = document.getElementById('editAnswerPreview');
        
        if (editTitle && editContent) {
            editTitle.addEventListener('input', () => this.updateEditPreview());
            editContent.addEventListener('input', () => this.updateEditPreview());
        }
    }

    updateEditPreview() {
        const editTitle = document.getElementById('editTitle');
        const editContent = document.getElementById('editContent');
        const editQuestionPreview = document.getElementById('editQuestionPreview');
        const editAnswerPreview = document.getElementById('editAnswerPreview');
        
        if (editTitle && editQuestionPreview) {
            editQuestionPreview.textContent = editTitle.value || 'Soru yazıldıkça burada görünecek...';
            editQuestionPreview.classList.toggle('text-muted', !editTitle.value);
        }
        
        if (editContent && editAnswerPreview) {
            const text = editContent.value || 'Cevap yazıldıkça burada görünecek...';
            const cleanText = text.replace(/<[^>]*>/g, '');
            const limitedText = cleanText.length > 150 ? cleanText.substring(0, 150) + '...' : cleanText;
            editAnswerPreview.textContent = limitedText;
            editAnswerPreview.classList.toggle('text-muted', !editContent.value);
        }
    }

    setupCharacterCounters() {
        this.addCharacterCounter(document.getElementById('newQuestionInput'), 200);
        this.addCharacterCounter(document.getElementById('newAnswerInput'), 1000);
        this.addCharacterCounter(document.getElementById('editTitle'), 200);
        this.addCharacterCounter(document.getElementById('editContent'), 1000);
    }

    addCharacterCounter(element, maxLength) {
        if (!element) return;
        
        const container = element.closest('.form-group');
        if (!container) return;
        
        let counterContainer = container.querySelector('.char-counter-container');
        if (!counterContainer) {
            counterContainer = document.createElement('div');
            counterContainer.className = 'char-counter-container';
            container.appendChild(counterContainer);
        }
        
        function updateCounter() {
            const currentLength = element.value.length;
            const remaining = maxLength - currentLength;
            
            let counter = counterContainer.querySelector('.char-counter');
            if (!counter) {
                counter = document.createElement('span');
                counter.className = 'char-counter';
                counter.style.cssText = 'font-size: 12px; color: #6b7280; float: right; margin-top: 4px;';
                counterContainer.appendChild(counter);
            }
            
            counter.textContent = `${currentLength}/${maxLength} karakter`;
            
            // Change color based on remaining
            if (remaining < 50) {
                counter.style.color = '#F59E0B';
            } else if (remaining < 0) {
                counter.style.color = '#EF4444';
            } else {
                counter.style.color = '#6b7280';
            }
        }
        
        element.addEventListener('input', updateCounter);
        updateCounter();
    }

    showLoadingToast(message) {
        this.showToast(message, 'info', true);
    }

    showSuccessToast(message) {
        this.showToast(message, 'success');
    }

    showErrorToast(message) {
        this.showToast(message, 'error');
    }

    showToast(message, type = 'info', loading = false) {
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.toast');
        existingToasts.forEach(toast => toast.remove());
        
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'primary'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${loading ? '<span class="spinner-border spinner-border-sm me-2" role="status"></span>' : ''}
                        ${message}
                    </div>
                    ${!loading ? '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' : ''}
                </div>
            </div>
        `;
        
        const toastContainer = document.querySelector('.toast-container') || this.createToastContainer();
        toastContainer.innerHTML = toastHtml;
        
        const toastElement = toastContainer.querySelector('.toast');
        const toast = new bootstrap.Toast(toastElement, {
            autohide: !loading,
            delay: 3000
        });
        
        toast.show();
        
        if (loading) {
            this.currentLoadingToast = toast;
        }
    }

    hideToast() {
        if (this.currentLoadingToast) {
            this.currentLoadingToast.hide();
            this.currentLoadingToast = null;
        }
    }

    createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(container);
        return container;
    }

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    showAddModal() {
        // Reset form
        document.getElementById('addFaqForm').reset();
        
        // Reset previews
        document.getElementById('newQuestionPreview').textContent = 'Soru yazıldıkça burada görünecek...';
        document.getElementById('newQuestionPreview').classList.add('text-muted');
        document.getElementById('newAnswerPreview').textContent = 'Cevap yazıldıkça burada görünecek...';
        document.getElementById('newAnswerPreview').classList.add('text-muted');
        
        // Show modal
        this.addModal.show();
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    window.faqManager = new FaqManager();
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    .faq-item-new {
        animation: slideIn 0.5s ease;
    }
    
    .faq-item-removing {
        animation: fadeOut 0.3s ease;
    }
    
    .faq-item-updating {
        opacity: 0.5;
        transition: opacity 0.3s ease;
    }
    
    .faq-item-updated {
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3) !important;
        transition: box-shadow 1s ease;
    }
    
    .stat-updated {
        animation: pulse 0.5s ease;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: scale(1);
        }
        to {
            opacity: 0;
            transform: scale(0.9);
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    
    .faq-item.expanded .faq-expand-icon i {
        transform: rotate(180deg);
    }
    
    .faq-item .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }
    
    .faq-item.expanded .faq-answer {
        max-height: 1000px;
    }
`;
document.head.appendChild(style);