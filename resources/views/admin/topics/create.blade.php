@extends('layouts.admin')

@section('title', 'Add Topic - A-plus CBT')
@section('page-title', 'Add New Topic')
@section('mobile-title', 'New Topic')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.topics.index') }}">Topics</a></li>
<li class="breadcrumb-item active">Add Topic</li>
@endsection

@section('content')
<div class="admin-card">
    <form action="{{ route('admin.topics.store') }}" method="POST" id="topicForm">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <div class="admin-card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Topic Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="subject_id" class="form-label">Subject *</label>
                                <select name="subject_id" 
                                        id="subject_id" 
                                        class="form-select @error('subject_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Select a Subject</option>
                                    @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" 
                                            {{ old('subject_id') == $subject->id ? 'selected' : '' }}
                                            data-code="{{ $subject->code }}">
                                        {{ $subject->name }} ({{ $subject->code }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Select the subject this topic belongs to</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Topic Name *</label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" 
                                       required 
                                       placeholder="e.g., Algebra, Comprehension, Organic Chemistry">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Name of the topic as it appears in the syllabus</small>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" 
                                          id="description" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          rows="3" 
                                          placeholder="Brief description of what this topic covers...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Optional: Describe the scope of this topic</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="syllabus_ref" class="form-label">Syllabus Reference</label>
                                <input type="text" 
                                       name="syllabus_ref" 
                                       id="syllabus_ref" 
                                       class="form-control @error('syllabus_ref') is-invalid @enderror" 
                                       value="{{ old('syllabus_ref') }}" 
                                       placeholder="e.g., JAMB Syllabus Ref: 1.1">
                                @error('syllabus_ref')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Optional: JAMB syllabus reference code</small>
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
                            <label for="syllabus_order" class="form-label">Syllabus Order</label>
                            <input type="number" 
                                   name="syllabus_order" 
                                   id="syllabus_order" 
                                   class="form-control @error('syllabus_order') is-invalid @enderror" 
                                   value="{{ old('syllabus_order', 0) }}" 
                                   min="0">
                            @error('syllabus_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Lower numbers appear first in the syllabus</small>
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
                                    Active Topic
                                </label>
                            </div>
                            <small class="text-muted">Inactive topics won't be available for question creation</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            <small>
                                <strong>Note:</strong> Topics must belong to a subject. 
                                If you don't see the subject you need, create it first in the 
                                <a href="{{ route('admin.subjects.create') }}" class="alert-link">Subjects section</a>.
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Subject Quick Stats -->
                <div class="admin-card">
                    <div class="card-header">
                        <h6 class="mb-0">Subject Information</h6>
                    </div>
                    <div class="card-body">
                        <div id="subject-info" class="text-center py-3">
                            <div class="text-muted">
                                <i class="ri-book-line" style="font-size: 36px;"></i>
                                <p class="mt-2 mb-0">Select a subject to see details</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="{{ route('admin.topics.index') }}" class="btn-admin btn-admin-secondary">
                <i class="ri-arrow-left-line me-2"></i> Cancel
            </a>
            
            <div class="d-flex gap-2">
                <button type="reset" class="btn-admin btn-admin-secondary">
                    <i class="ri-restart-line me-2"></i> Reset
                </button>
                <button type="submit" class="btn-admin btn-admin-primary">
                    <i class="ri-save-line me-2"></i> Save Topic
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const subjectSelect = document.getElementById('subject_id');
    const subjectInfo = document.getElementById('subject-info');
    
    // Function to load subject information
    function loadSubjectInfo(subjectId) {
        if (!subjectId) {
            subjectInfo.innerHTML = `
                <div class="text-muted">
                    <i class="ri-book-line" style="font-size: 36px;"></i>
                    <p class="mt-2 mb-0">Select a subject to see details</p>
                </div>`;
            return;
        }
        
        // Show loading
        subjectInfo.innerHTML = `
            <div class="text-center">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Loading subject info...</p>
            </div>`;
        
        // Fetch subject details (you could make this AJAX or preload data)
        // For now, we'll just show the selected subject name
        const selectedOption = subjectSelect.options[subjectSelect.selectedIndex];
        const subjectName = selectedOption.text;
        const subjectCode = selectedOption.getAttribute('data-code');
        
        subjectInfo.innerHTML = `
            <div>
                <h6 class="mb-1">${subjectName}</h6>
                <div class="badge bg-secondary mb-3">${subjectCode}</div>
                <div class="text-muted small">
                    <div class="mb-1">Topics will be added to this subject</div>
                    <div>Make sure this is the correct subject</div>
                </div>
            </div>`;
    }
    
    // Load subject info on page load if subject is already selected
    if (subjectSelect.value) {
        loadSubjectInfo(subjectSelect.value);
    }
    
    // Update subject info when selection changes
    subjectSelect.addEventListener('change', function() {
        loadSubjectInfo(this.value);
    });
    
    // Form validation
    const form = document.getElementById('topicForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const subjectId = document.getElementById('subject_id').value;
            const name = document.getElementById('name').value.trim();
            
            if (!subjectId || !name) {
                e.preventDefault();
                alert('Please fill in all required fields (Subject and Topic Name).');
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
.form-check.form-switch .form-check-input {
    width: 2.5em;
    height: 1.25em;
}

.form-check.form-switch .form-check-input:checked {
    background-color: #14b8a6;
    border-color: #14b8a6;
}

.alert-info {
    background-color: rgba(20, 184, 166, 0.1);
    border-color: rgba(20, 184, 166, 0.2);
    color: #0d9488;
}

.alert-info .alert-link {
    color: #0f766e;
    text-decoration: underline;
}
</style>
@endpush