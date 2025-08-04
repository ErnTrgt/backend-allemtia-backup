@extends('layouts.admin')

@section('title', 'S.S.S Yönetimi')
@section('header-title', 'Sıkça Sorulan Sorular')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/faq.css') }}">
@endpush

@section('content')
<!-- Page Actions -->
<div class="page-actions">
    <div class="page-actions-left">
        <!-- Search -->
        <div class="search-wrapper">
            <i class="bi bi-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Soru ara..." id="faqSearch">
        </div>
    </div>
    
    <!-- Add New FAQ -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFaqModal" 
            style="background: var(--primary-red); border-color: var(--primary-red);">
        <i class="bi bi-plus-circle"></i>
        Yeni S.S.S Ekle
    </button>
</div>

<!-- FAQ Stats -->
<div class="faq-stats">
    <!-- Total FAQs -->
    <div class="stat-card">
        <div class="stat-icon">
            <i class="bi bi-question-circle"></i>
        </div>
        <div class="stat-value">{{ $faqs->total() }}</div>
        <div class="stat-label">Toplam S.S.S</div>
    </div>
    
    <!-- Active FAQs -->
    <div class="stat-card active">
        <div class="stat-icon">
            <i class="bi bi-check-circle"></i>
        </div>
        <div class="stat-value">{{ $faqs->filter(function($f) { return $f->status == true; })->count() }}</div>
        <div class="stat-label">Aktif S.S.S</div>
    </div>
    
    <!-- Inactive FAQs -->
    <div class="stat-card inactive">
        <div class="stat-icon">
            <i class="bi bi-x-circle"></i>
        </div>
        <div class="stat-value">{{ $faqs->filter(function($f) { return $f->status == false; })->count() }}</div>
        <div class="stat-label">Pasif S.S.S</div>
    </div>
    
    <!-- Categories -->
    <div class="stat-card">
        <div class="stat-icon">
            <i class="bi bi-tags"></i>
        </div>
        <div class="stat-value">{{ $faqs->pluck('category')->unique()->count() }}</div>
        <div class="stat-label">Kategori</div>
    </div>
</div>

<!-- FAQ Container -->
<div class="faq-container">
    <div class="faq-header">
        <h3 class="faq-title">S.S.S Listesi</h3>
    </div>
    
    <div class="faq-accordion">
        @forelse($faqs as $faq)
            <div class="faq-item" data-question="{{ strtolower($faq->title) }}">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <div class="faq-question-content">
                        <div class="faq-question-icon">
                            <i class="bi bi-patch-question"></i>
                        </div>
                        <div class="faq-question-text">
                            {{ $faq->title }}
                        </div>
                    </div>
                    <div class="faq-question-meta">
                        <span class="faq-status {{ $faq->status ? 'active' : 'inactive' }}">
                            <i class="bi bi-{{ $faq->status ? 'check' : 'x' }}-circle"></i>
                            {{ $faq->status ? 'Aktif' : 'Pasif' }}
                        </span>
                        <span class="faq-expand-icon">
                            <i class="bi bi-chevron-down"></i>
                        </span>
                    </div>
                </div>
                
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <div class="faq-answer-text">
                            {!! $faq->content !!}
                        </div>
                        
                        <div class="faq-actions">
                            <button class="action-btn edit" data-bs-toggle="modal" 
                                    data-bs-target="#editFaqModal{{ $faq->id }}">
                                <i class="bi bi-pencil"></i>
                                Düzenle
                            </button>
                            
                            <form action="{{ route('admin.faq.toggle', $faq->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="action-btn toggle">
                                    <i class="bi bi-{{ $faq->status ? 'pause' : 'play' }}"></i>
                                    {{ $faq->status ? 'Pasifleştir' : 'Aktifleştir' }}
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.faq.delete', $faq->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Bu S.S.S\'yi silmek istediğinizden emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete">
                                    <i class="bi bi-trash"></i>
                                    Sil
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="bi bi-question-circle empty-icon"></i>
                <h3 class="empty-title">Henüz S.S.S Yok</h3>
                <p class="empty-text">İlk S.S.S'nizi oluşturmak için yukarıdaki butonu kullanın.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Pagination -->
@if($faqs instanceof \Illuminate\Pagination\LengthAwarePaginator && $faqs->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $faqs->links('components.admin-pagination') }}
</div>
@endif

<!-- Add FAQ Modal -->
<div class="modal fade" id="addFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                    <i class="bi bi-plus-circle me-2" style="color: #A90000;"></i>
                    Yeni S.S.S Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
            </div>
            <form action="{{ route('admin.faq.store') }}" method="POST">
                @csrf
                <div class="modal-body" style="padding: 24px;">
                    <!-- Question Section -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-question-circle" style="color: #A90000;"></i>
                            Soru Bilgileri
                        </h6>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Soru</label>
                            <input type="text" name="title" class="form-control" required 
                                   style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;"
                                   placeholder="Müşterilerinizin sık sorduğu soruyu yazın..."
                                   id="newQuestionInput">
                            <div class="char-counter-container"></div>
                        </div>
                    </div>
                    
                    <!-- Answer Section -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-chat-dots" style="color: #A90000;"></i>
                            Cevap
                        </h6>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Detaylı Cevap</label>
                            <textarea name="content" class="form-control" rows="6" 
                                      style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;"
                                      placeholder="Soruya detaylı ve açıklayıcı bir cevap yazın..."
                                      id="newAnswerInput"></textarea>
                            <div class="char-counter-container"></div>
                        </div>
                    </div>
                    
                    <!-- Preview -->
                    <div class="preview-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="preview-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-eye" style="color: #A90000;"></i>
                            Önizleme
                        </h6>
                        <div class="faq-preview" style="background: rgba(255, 255, 255, 0.6); border-radius: 8px; padding: 16px;">
                            <div class="preview-question" style="margin-bottom: 12px;">
                                <strong style="color: #A90000;">S:</strong> <span id="newQuestionPreview" class="text-muted">Soru yazıldıkça burada görünecek...</span>
                            </div>
                            <div class="preview-answer">
                                <strong style="color: #A90000;">C:</strong> <span id="newAnswerPreview" class="text-muted">Cevap yazıldıkça burada görünecek...</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-message" style="display: flex; align-items: flex-start; gap: 12px; padding: 16px; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 12px;">
                        <i class="bi bi-info-circle-fill" style="color: #3B82F6; font-size: 20px; flex-shrink: 0;"></i>
                        <div class="info-message-content" style="flex: 1;">
                            <div class="info-message-title" style="font-weight: 600; color: #1f2937; margin-bottom: 2px;">İpucu</div>
                            <div class="info-message-text" style="color: #4b5563; font-size: 14px;">
                                S.S.S'ler müşteri destek taleplerini azaltır. Net ve anlaşılır cevaplar yazın.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: linear-gradient(135deg, rgba(0, 0, 0, 0.02) 0%, rgba(0, 0, 0, 0.04) 100%); border-top: 1px solid rgba(0, 0, 0, 0.05); padding: 20px 24px; gap: 16px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, rgba(0, 0, 0, 0.05) 0%, rgba(0, 0, 0, 0.08) 100%); color: #374151; border: 1px solid rgba(0, 0, 0, 0.1); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">İptal</button>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, #A90000 0%, #C1121F 100%); color: white; border: none; box-shadow: 0 4px 16px rgba(169, 0, 0, 0.25); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">
                        <i class="bi bi-check-lg me-1"></i>
                        S.S.S Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit FAQ Modals -->
@foreach($faqs as $faq)
<div class="modal fade" id="editFaqModal{{ $faq->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                    <i class="bi bi-pencil me-2" style="color: #A90000;"></i>
                    S.S.S Düzenle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
            </div>
            <form action="{{ route('admin.faq.update', $faq->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" style="padding: 24px;">
                    <!-- Question Section -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-question-circle" style="color: #A90000;"></i>
                            Soru Bilgileri
                        </h6>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Soru</label>
                            <input type="text" name="title" class="form-control" required 
                                   style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;"
                                   value="{{ $faq->title }}"
                                   id="questionInput{{ $faq->id }}">
                            <div class="char-counter-container"></div>
                        </div>
                    </div>
                    
                    <!-- Answer Section -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-chat-dots" style="color: #A90000;"></i>
                            Cevap
                        </h6>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Detaylı Cevap</label>
                            <textarea name="content" class="form-control" rows="6"
                                      style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;"
                                      id="answerInput{{ $faq->id }}">{{ $faq->content }}</textarea>
                            <div class="char-counter-container"></div>
                        </div>
                    </div>
                    
                    <!-- Preview -->
                    <div class="preview-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="preview-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-eye" style="color: #A90000;"></i>
                            Önizleme
                        </h6>
                        <div class="faq-preview" style="background: rgba(255, 255, 255, 0.6); border-radius: 8px; padding: 16px;">
                            <div class="preview-question" style="margin-bottom: 12px;">
                                <strong style="color: #A90000;">S:</strong> <span id="questionPreview{{ $faq->id }}">{{ $faq->title }}</span>
                            </div>
                            <div class="preview-answer">
                                <strong style="color: #A90000;">C:</strong> <span id="answerPreview{{ $faq->id }}">{{ Str::limit(strip_tags($faq->content), 150) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 0; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-toggle-on" style="color: #A90000;"></i>
                            Durum
                        </h6>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">S.S.S Durumu</label>
                            <select name="status" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                <option value="1" {{ $faq->status ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ !$faq->status ? 'selected' : '' }}>Pasif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: linear-gradient(135deg, rgba(0, 0, 0, 0.02) 0%, rgba(0, 0, 0, 0.04) 100%); border-top: 1px solid rgba(0, 0, 0, 0.05); padding: 20px 24px; gap: 16px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, rgba(0, 0, 0, 0.05) 0%, rgba(0, 0, 0, 0.08) 100%); color: #374151; border: 1px solid rgba(0, 0, 0, 0.1); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">İptal</button>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, #A90000 0%, #C1121F 100%); color: white; border: none; box-shadow: 0 4px 16px rgba(169, 0, 0, 0.25); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">
                        <i class="bi bi-check-lg me-1"></i>
                        Değişiklikleri Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
// Search functionality
let searchTimer;
document.getElementById('faqSearch').addEventListener('input', function(e) {
    clearTimeout(searchTimer);
    const query = e.target.value.toLowerCase();
    
    searchTimer = setTimeout(() => {
        const items = document.querySelectorAll('.faq-item');
        
        items.forEach(item => {
            const question = item.dataset.question;
            
            if (question.includes(query)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }, 300);
});

// Toggle FAQ
function toggleFaq(element) {
    const faqItem = element.closest('.faq-item');
    faqItem.classList.toggle('expanded');
}

// Character counter
function addCharacterCounter(element, maxLength = 500) {
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
        if (remaining < 50) {
            counter.classList.add('warning');
        }
        if (remaining < 0) {
            counter.classList.add('danger');
        }
    }
    
    element.addEventListener('input', updateCounter);
    updateCounter();
}

// Live preview
function setupLivePreview(questionInput, answerInput, questionPreview, answerPreview) {
    function updatePreview() {
        const questionText = questionInput.value.trim();
        const answerText = answerInput.value.trim();
        
        if (questionText) {
            questionPreview.textContent = questionText;
            questionPreview.classList.remove('text-muted');
        } else {
            questionPreview.textContent = 'Soru yazıldıkça burada görünecek...';
            questionPreview.classList.add('text-muted');
        }
        
        if (answerText) {
            const cleanText = answerText.replace(/<[^>]*>/g, '');
            const limitedText = cleanText.length > 150 ? cleanText.substring(0, 150) + '...' : cleanText;
            answerPreview.textContent = limitedText;
            answerPreview.classList.remove('text-muted');
        } else {
            answerPreview.textContent = 'Cevap yazıldıkça burada görünecek...';
            answerPreview.classList.add('text-muted');
        }
    }
    
    questionInput.addEventListener('input', updatePreview);
    answerInput.addEventListener('input', updatePreview);
    updatePreview();
}

// Initialize for add modal
const newQuestionInput = document.getElementById('newQuestionInput');
const newAnswerInput = document.getElementById('newAnswerInput');
const newQuestionPreview = document.getElementById('newQuestionPreview');
const newAnswerPreview = document.getElementById('newAnswerPreview');

if (newQuestionInput && newAnswerInput) {
    addCharacterCounter(newQuestionInput, 200);
    addCharacterCounter(newAnswerInput, 1000);
    setupLivePreview(newQuestionInput, newAnswerInput, newQuestionPreview, newAnswerPreview);
}

// Initialize for edit modals
@foreach($faqs as $faq)
(function() {
    const questionInput = document.getElementById('questionInput{{ $faq->id }}');
    const answerInput = document.getElementById('answerInput{{ $faq->id }}');
    const questionPreview = document.getElementById('questionPreview{{ $faq->id }}');
    const answerPreview = document.getElementById('answerPreview{{ $faq->id }}');
    
    if (questionInput && answerInput) {
        addCharacterCounter(questionInput, 200);
        addCharacterCounter(answerInput, 1000);
        setupLivePreview(questionInput, answerInput, questionPreview, answerPreview);
    }
})();
@endforeach

// Show success/error messages
@if(session('success'))
    AdminPanel.showToast('{{ session('success') }}', 'success');
@endif

@if(session('error'))
    AdminPanel.showToast('{{ session('error') }}', 'error');
@endif
</script>
@endpush