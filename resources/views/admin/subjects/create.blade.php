@extends('layouts.admin')

@section('title', 'Add Subject - A-plus CBT')
@section('page-title', 'Add New Subject')
@section('mobile-title', 'New Subject')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.subjects.index') }}">Subjects</a></li>
<li class="breadcrumb-item active">Add Subject</li>
@endsection

@section('content')
<div class="admin-card">
    <form action="{{ route('admin.subjects.store') }}" method="POST" id="subjectForm">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <div class="admin-card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Subject Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Subject Name *</label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" 
                                       required 
                                       placeholder="e.g., English Language">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Full name of the subject</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Subject Code *</label>
                                <input type="text" 
                                       name="code" 
                                       id="code" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code') }}" 
                                       required 
                                       placeholder="e.g., ENG"
                                       style="text-transform:uppercase">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Short code (3-4 letters)</small>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" 
                                          id="description" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          rows="3" 
                                          placeholder="Brief description of the subject...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Optional: Describe what this subject covers</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="admin-card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="order" class="form-label">Display Order</label>
                            <input type="number" 
                                   name="order" 
                                   id="order" 
                                   class="form-control @error('order') is-invalid @enderror" 
                                   value="{{ old('order', 0) }}" 
                                   min="0">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Lower numbers appear first</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="icon_class" class="form-label">Icon Class</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ri-palette-line"></i>
                                </span>
                                <input type="text" 
                                       name="icon_class" 
                                       id="icon_class" 
                                       class="form-control @error('icon_class') is-invalid @enderror" 
                                       value="{{ old('icon_class') }}" 
                                       placeholder="ri-book-line">
                            </div>
                            @error('icon_class')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Remix Icon class (optional)</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="is_active" 
                                       id="is_active" 
                                       value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Subject
                                </label>
                            </div>
                            <small class="text-muted">Inactive subjects won't be available to students</small>
                        </div>
                    </div>
                </div>
                
                <div class="admin-card">
                    <div class="card-body">
                        <h6 class="mb-3">Available Icons</h6>
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <button type="button" class="icon-select btn btn-outline-secondary w-100" data-icon="ri-book-line">
                                    <i class="ri-book-line"></i>
                                </button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="icon-select btn btn-outline-secondary w-100" data-icon="ri-calculator-line">
                                    <i class="ri-calculator-line"></i>
                                </button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="icon-select btn btn-outline-secondary w-100" data-icon="ri-flask-line">
                                    <i class="ri-flask-line"></i>
                                </button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="icon-select btn btn-outline-secondary w-100" data-icon="ri-test-tube-line">
                                    <i class="ri-test-tube-line"></i>
                                </button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="icon-select btn btn-outline-secondary w-100" data-icon="ri-microscope-line">
                                    <i class="ri-microscope-line"></i>
                                </button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="icon-select btn btn-outline-secondary w-100" data-icon="ri-globe-line">
                                    <i class="ri-globe-line"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Click an icon to select it</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="{{ route('admin.subjects.index') }}" class="btn-admin btn-admin-secondary">
                <i class="ri-arrow-left-line me-2"></i> Cancel
            </a>
            
            <div class="d-flex gap-2">
                <button type="reset" class="btn-admin btn-admin-secondary">
                    <i class="ri-restart-line me-2"></i> Reset
                </button>
                <button type="submit" class="btn-admin btn-admin-primary">
                    <i class="ri-save-line me-2"></i> Save Subject
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-uppercase subject code
    const codeInput = document.getElementById('code');
    if (codeInput) {
        codeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
    
    // Icon selection
    document.querySelectorAll('.icon-select').forEach(button => {
        button.addEventListener('click', function() {
            const iconClass = this.getAttribute('data-icon');
            document.getElementById('icon_class').value = iconClass;
            
            // Update active state
            document.querySelectorAll('.icon-select').forEach(btn => {
                btn.classList.remove('active', 'btn-primary');
                btn.classList.add('btn-outline-secondary');
            });
            
            this.classList.remove('btn-outline-secondary');
            this.classList.add('active', 'btn-primary');
        });
    });
    
    // Form validation
    const form = document.getElementById('subjectForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const code = document.getElementById('code').value.trim();
            
            if (!name || !code) {
                e.preventDefault();
                alert('Please fill in all required fields (Subject Name and Code).');
                return false;
            }
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="ri-loader-4-line spin me-2"></i> Saving...';
            submitBtn.disabled = true;
            
            // Re-enable button after 5 seconds (in case submission fails)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });
    }
});

// Spinner animation
const style = document.createElement('style');
style.textContent = `
.spin {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
`;
document.head.appendChild(style);
</script>

<style>
.icon-select {
    padding: 0.5rem;
    font-size: 1.25rem;
    transition: all 0.2s;
}

.icon-select:hover {
    transform: translateY(-2px);
}

.icon-select.active {
    background-color: #14b8a6 !important;
    border-color: #14b8a6 !important;
    color: white !important;
}

.form-check.form-switch .form-check-input {
    width: 2.5em;
    height: 1.25em;
}

.form-check.form-switch .form-check-input:checked {
    background-color: #14b8a6;
    border-color: #14b8a6;
}
</style>
@endpush