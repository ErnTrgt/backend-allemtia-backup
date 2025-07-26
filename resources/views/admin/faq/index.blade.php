@extends('layouts.layout')

@section('title', 'SSS Yönetimi')

@section('content')
<div class="pd-ltr-20 xs-pd-20-10">
    <div class="min-height-200px">
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title">
                        <h4>SSS Yönetimi</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Ana Sayfa</a></li>
                            <li class="breadcrumb-item active" aria-current="page">SSS'ler</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 col-sm-12 text-right">
                    <button class="btn btn-success" data-toggle="modal" data-target="#addFaqModal">
                        + Yeni SSS Ekle
                    </button>
                </div>
            </div>
        </div>

        <div class="card-box mb-30">
            <div class="pd-20">
                <h4 class="text-blue h4">SSS Listesi</h4>
            </div>
            <div class="pb-20">
                <table class="data-table table stripe hover nowrap dt-responsive" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Soru</th>
                            <th>Cevap</th>
                            <th>Durum</th>
                            <th class="datatable-nosort">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($faqs as $faq)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ Str::limit($faq->title, 60) }}</td>
                            <td>{{ Str::limit(strip_tags($faq->content), 100) }}</td>
                            <td>
                                <span class="badge {{ $faq->status ? 'badge-success' : 'badge-danger' }}">
                                    {{ $faq->status ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                                        href="#" role="button" data-toggle="dropdown">
                                        <i class="dw dw-more"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                        <a class="dropdown-item" data-toggle="modal"
                                            data-target="#editFaqModal{{ $faq->id }}" href="#">
                                            <i class="dw dw-edit2"></i> Düzenle
                                        </a>
                                        <a class="dropdown-item" href="#"
                                            onclick="event.preventDefault(); document.getElementById('toggle-faq-{{ $faq->id }}').submit();">
                                            <i class="dw {{ $faq->status ? 'dw-ban' : 'dw-check' }}"></i>
                                            {{ $faq->status ? 'Pasifleştir' : 'Aktifleştir' }}
                                        </a>
                                        <form id="toggle-faq-{{ $faq->id }}"
                                            action="{{ route('admin.faq.toggle', $faq->id) }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        <a class="dropdown-item text-danger" href="#"
                                            onclick="event.preventDefault(); document.getElementById('delete-faq-{{ $faq->id }}').submit();">
                                            <i class="dw dw-delete-3"></i> Sil
                                        </a>
                                        <form id="delete-faq-{{ $faq->id }}"
                                            action="{{ route('admin.faq.delete', $faq->id) }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit FAQ Modal -->
                        <div class="modal fade" id="editFaqModal{{ $faq->id }}" tabindex="-1"
                            role="dialog" aria-labelledby="editFaqModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header bg-gradient-primary text-white border-0">
                                        <h4 class="modal-title font-weight-bold">
                                            <i class="dw dw-question mr-2"></i>SSS Düzenle
                                            <small class="ml-2 opacity-75">#{{ $faq->id }}</small>
                                        </h4>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.faq.update', $faq->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <!-- Soru Bölümü -->
                                            <div class="form-section mb-4">
                                                <h6 class="text-primary font-weight-bold mb-3">
                                                    <i class="dw dw-help mr-2"></i>Soru Bilgileri
                                                </h6>
                                                <div class="form-group">
                                                    <label class="font-weight-semibold text-dark">
                                                        <i class="dw dw-question mr-1 text-info"></i>Soru Metni
                                                    </label>
                                                    <input type="text" name="title" class="form-control form-control-lg border-2"
                                                           value="{{ $faq->title }}" required 
                                                           placeholder="Müşterilerinizin sık sorduğu soruyu yazın...">
                                                    <small class="text-muted">Net ve anlaşılır bir soru yazın</small>
                                                    <div class="char-counter-container mt-1"></div>
                                                </div>
                                            </div>

                                            <!-- Cevap Bölümü -->
                                            <div class="form-section">
                                                <h6 class="text-success font-weight-bold mb-3">
                                                    <i class="dw dw-chat mr-2"></i>Cevap Bilgileri
                                                </h6>
                                                <div class="form-group">
                                                    <label class="font-weight-semibold text-dark">
                                                        <i class="dw dw-text-width mr-1 text-success"></i>Detaylı Cevap
                                                    </label>
                                                    <div class="textarea-container">
                                                        <textarea name="content" rows="8" 
                                                                  class="form-control form-control-lg border-2"
                                                                  placeholder="Soruya detaylı ve açıklayıcı bir cevap yazın...">{{ $faq->content }}</textarea>
                                                        <div class="textarea-tools">
                                                            <small class="text-muted">
                                                                <i class="dw dw-info mr-1"></i>
                                                                Açık ve anlaşılır bir dille yazın. HTML etiketleri kullanabilirsiniz.
                                                            </small>
                                                            <div class="char-counter-container mt-1"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Önizleme Alanı -->
                                            <div class="preview-section mt-4">
                                                <div class="alert alert-light border-2 border-primary">
                                                    <h6 class="text-primary font-weight-bold mb-2">
                                                        <i class="dw dw-eye mr-2"></i>SSS Önizleme
                                                    </h6>
                                                    <div class="faq-preview">
                                                        <div class="preview-question">
                                                            <strong class="text-dark">S:</strong> 
                                                            <span id="questionPreview{{ $faq->id }}">{{ $faq->title }}</span>
                                                        </div>
                                                        <div class="preview-answer mt-2">
                                                            <strong class="text-success">C:</strong> 
                                                            <span id="answerPreview{{ $faq->id }}">{{ Str::limit(strip_tags($faq->content), 100) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- İpucu -->
                                            <div class="alert alert-info border-0 mt-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="dw dw-lightbulb text-info mr-2" style="font-size: 18px;"></i>
                                                    <div>
                                                        <strong>İpucu:</strong> 
                                                        İyi bir SSS, müşterilerinizin destek talebini azaltır ve memnuniyeti artırır.
                                                        Cevaplarınızı mümkün olduğunca detaylı yazın.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light border-0 justify-content-between">
                                            <button type="button" class="btn btn-light btn-lg px-4" data-dismiss="modal">
                                                <i class="dw dw-cancel mr-2"></i>İptal
                                            </button>
                                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                                <i class="dw dw-save mr-2"></i>Değişiklikleri Kaydet
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Edit Modal End -->
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add FAQ Modal -->
<div class="modal fade" id="addFaqModal" tabindex="-1" role="dialog" aria-labelledby="addFaqModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-success text-white border-0">
                <h4 class="modal-title font-weight-bold">
                    <i class="dw dw-add mr-2"></i>Yeni SSS Oluştur
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.faq.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <!-- Soru Bölümü -->
                    <div class="form-section mb-4">
                        <h6 class="text-primary font-weight-bold mb-3">
                            <i class="dw dw-help mr-2"></i>Soru Bilgileri
                        </h6>
                        <div class="form-group">
                            <label class="font-weight-semibold text-dark">
                                <i class="dw dw-question mr-1 text-info"></i>Soru Metni
                            </label>
                            <input type="text" name="title" class="form-control form-control-lg border-2" required 
                                   placeholder="Müşterilerinizin sık sorduğu soruyu yazın...">
                            <small class="text-muted">Net ve anlaşılır bir soru yazın</small>
                            <div class="char-counter-container mt-1"></div>
                        </div>
                    </div>

                    <!-- Cevap Bölümü -->
                    <div class="form-section mb-4">
                        <h6 class="text-success font-weight-bold mb-3">
                            <i class="dw dw-chat mr-2"></i>Cevap Bilgileri
                        </h6>
                        <div class="form-group">
                            <label class="font-weight-semibold text-dark">
                                <i class="dw dw-text-width mr-1 text-success"></i>Detaylı Cevap
                            </label>
                            <div class="textarea-container">
                                <textarea name="content" rows="8" 
                                          class="form-control form-control-lg border-2"
                                          placeholder="Soruya detaylı ve açıklayıcı bir cevap yazın..."></textarea>
                                <div class="textarea-tools">
                                    <small class="text-muted">
                                        <i class="dw dw-info mr-1"></i>
                                        Açık ve anlaşılır bir dille yazın. HTML etiketleri kullanabilirsiniz.
                                    </small>
                                    <div class="char-counter-container mt-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Önizleme Alanı -->
                    <div class="preview-section">
                        <div class="alert alert-light border-2 border-success">
                            <h6 class="text-success font-weight-bold mb-2">
                                <i class="dw dw-eye mr-2"></i>SSS Önizleme
                            </h6>
                            <div class="faq-preview">
                                <div class="preview-question">
                                    <strong class="text-dark">S:</strong> 
                                    <span id="newQuestionPreview" class="text-muted">Soru yazıldıkça burada görünecek...</span>
                                </div>
                                <div class="preview-answer mt-2">
                                    <strong class="text-success">C:</strong> 
                                    <span id="newAnswerPreview" class="text-muted">Cevap yazıldıkça burada görünecek...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Örnek SSS'ler -->
                    <div class="examples-section mt-4">
                        <div class="alert alert-warning border-0">
                            <h6 class="text-warning font-weight-bold mb-2">
                                <i class="dw dw-lightbulb mr-2"></i>Örnek SSS Konuları
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="mb-0 text-muted">
                                        <li>• Kargo süresi ne kadar?</li>
                                        <li>• İade şartları nelerdir?</li>
                                        <li>• Hangi ödeme yöntemleri kabul edilir?</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="mb-0 text-muted">
                                        <li>• Ürün garantisi var mı?</li>
                                        <li>• Toplu sipariş indirimi var mı?</li>
                                        <li>• Müşteri hizmetleri nasıl ulaşılır?</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bilgilendirme -->
                    <div class="alert alert-success border-0 mt-3">
                        <div class="d-flex align-items-center">
                            <i class="dw dw-lightbulb text-success mr-2" style="font-size: 18px;"></i>
                            <div>
                                <strong>İpucu:</strong> 
                                SSS oluşturduktan sonra "Aktif" duruma getirmeyi unutmayın. 
                                İyi yazılmış SSS'ler müşteri memnuniyetini artırır.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 justify-content-between">
                    <button type="button" class="btn btn-light btn-lg px-4" data-dismiss="modal">
                        <i class="dw dw-cancel mr-2"></i>İptal
                    </button>
                    <button type="submit" class="btn btn-success btn-lg px-4">
                        <i class="dw dw-add mr-2"></i>SSS Oluştur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<style>
/* Modal Geliştirmeleri */
.modal-xl {
    max-width: 1000px;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.form-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    border-left: 4px solid #007bff;
}

.form-control-lg {
    /* height: calc(2.5rem + 2px); */
    padding: 0.75rem 1rem;
    font-size: 1.1rem;
    min-height:50px
}

.border-2 {
    border-width: 2px !important;
    transition: all 0.3s ease;
}

.border-2:focus {
    border-color: #007bff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

.font-weight-semibold {
    font-weight: 600;
}

.alert {
    border-radius: 10px;
}

/* Textarea Container */
.textarea-container {
    position: relative;
}

.textarea-tools {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 5px;
}

/* Character Counter */
.char-counter-container {
    text-align: right;
}

.char-counter {
    font-size: 12px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 12px;
    background: #e9ecef;
    color: #6c757d;
    display: inline-block;
}

.char-counter.warning {
    background: #fff3cd;
    color: #856404;
}

.char-counter.danger {
    background: #f8d7da;
    color: #721c24;
}

/* Preview Section */
.preview-section {
    background: #ffffff;
    border-radius: 10px;
    padding: 0;
}

.faq-preview {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.preview-question {
    padding: 8px 0;
    border-bottom: 1px solid #e9ecef;
}

.preview-answer {
    padding: 8px 0;
    line-height: 1.6;
}

/* Examples Section */
.examples-section ul {
    list-style: none;
    padding-left: 0;
}

.examples-section li {
    margin-bottom: 3px;
    color: #6c757d;
}

/* Badge ve Button Geliştirmeleri */
.btn-lg {
    padding: 12px 24px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-lg:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Form Section Headers */
.form-section h6 {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 8px;
    margin-bottom: 20px;
}

/* Modal Shadow */
.modal-content {
    box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
}

/* Opacity utility */
.opacity-75 {
    opacity: 0.75;
}

/* Live Preview Styling */
#newQuestionPreview,
#newAnswerPreview {
    transition: all 0.3s ease;
}

#newQuestionPreview:not(.text-muted),
#newAnswerPreview:not(.text-muted) {
    color: #495057 !important;
}

/* Responsive */
@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
        margin: 10px auto;
    }
    
    .form-section {
        padding: 15px;
    }
    
    .btn-lg {
        padding: 10px 20px;
        font-size: 14px;
    }
    
    .textarea-tools {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .char-counter-container {
        text-align: left;
        width: 100%;
    }
    
    .examples-section .row {
        margin: 0;
    }
    
    .examples-section .col-md-6 {
        padding: 0;
        margin-bottom: 10px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter function
    function addCharacterCounter(element, maxLength = 500) {
        const container = element.parentNode.querySelector('.char-counter-container');
        if (!container) return;
        
        function updateCounter() {
            const currentLength = element.value.length;
            const remaining = maxLength - currentLength;
            
            // Create or update counter
            let counter = container.querySelector('.char-counter');
            if (!counter) {
                counter = document.createElement('span');
                counter.className = 'char-counter';
                container.appendChild(counter);
            }
            
            counter.textContent = `${currentLength}/${maxLength} karakter`;
            
            // Update counter styling
            counter.className = 'char-counter';
            if (remaining < 50) {
                counter.classList.add('warning');
            }
            if (remaining < 0) {
                counter.classList.add('danger');
            }
        }
        
        element.addEventListener('input', updateCounter);
        updateCounter(); // Initial call
    }

    // Live preview function
    function setupLivePreview(questionInput, answerInput, questionPreview, answerPreview) {
        function updatePreview() {
            const questionText = questionInput.value.trim();
            const answerText = answerInput.value.trim();
            
            // Update question preview
            if (questionText) {
                questionPreview.textContent = questionText;
                questionPreview.classList.remove('text-muted');
            } else {
                questionPreview.textContent = 'Soru yazıldıkça burada görünecek...';
                questionPreview.classList.add('text-muted');
            }
            
            // Update answer preview
            if (answerText) {
                // Strip HTML tags and limit length
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
        updatePreview(); // Initial call
    }

    // Setup for Add Modal
    const addQuestionInput = document.querySelector('#addFaqModal input[name="title"]');
    const addAnswerInput = document.querySelector('#addFaqModal textarea[name="content"]');
    const newQuestionPreview = document.getElementById('newQuestionPreview');
    const newAnswerPreview = document.getElementById('newAnswerPreview');
    
    if (addQuestionInput && addAnswerInput && newQuestionPreview && newAnswerPreview) {
        addCharacterCounter(addQuestionInput, 200);
        addCharacterCounter(addAnswerInput, 1000);
        setupLivePreview(addQuestionInput, addAnswerInput, newQuestionPreview, newAnswerPreview);
    }

    // Setup for Edit Modals
    document.querySelectorAll('[id^="editFaqModal"]').forEach(function(modal) {
        const faqId = modal.id.replace('editFaqModal', '');
        const questionInput = modal.querySelector('input[name="title"]');
        const answerInput = modal.querySelector('textarea[name="content"]');
        const questionPreview = document.getElementById('questionPreview' + faqId);
        const answerPreview = document.getElementById('answerPreview' + faqId);
        
        if (questionInput && answerInput) {
            addCharacterCounter(questionInput, 200);
            addCharacterCounter(answerInput, 1000);
            
            if (questionPreview && answerPreview) {
                setupLivePreview(questionInput, answerInput, questionPreview, answerPreview);
            }
        }
    });

    // Form validation feedback
    document.querySelectorAll('.border-2').forEach(function(input) {
        input.addEventListener('invalid', function() {
            this.style.borderColor = '#dc3545';
        });
        
        input.addEventListener('input', function() {
            if (this.checkValidity()) {
                this.style.borderColor = '#28a745';
            } else if (this.value.length > 0) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#ced4da';
            }
        });
    });

    // Auto-resize textareas
    document.querySelectorAll('textarea').forEach(function(textarea) {
        function autoResize() {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 300) + 'px';
        }
        
        textarea.addEventListener('input', autoResize);
        autoResize(); // Initial call
    });

    // Quick suggestion clicks for new FAQ
    document.querySelectorAll('.examples-section li').forEach(function(item) {
        item.style.cursor = 'pointer';
        item.addEventListener('click', function() {
            const questionText = this.textContent.replace('• ', '');
            if (addQuestionInput && !addQuestionInput.value.trim()) {
                addQuestionInput.value = questionText;
                addQuestionInput.dispatchEvent(new Event('input'));
                addQuestionInput.focus();
            }
        });
        
        item.addEventListener('mouseenter', function() {
            this.style.color = '#007bff';
            this.style.textDecoration = 'underline';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.color = '#6c757d';
            this.style.textDecoration = 'none';
        });
    });
});
</script>