@extends('layouts.admin')

@section('title', 'Edit Subject - A-plus CBT')
@section('page-title', 'Edit Subject: ' . $subject->name)
@section('mobile-title', 'Edit Subject')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.subjects.index') }}">Subjects</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.subjects.show', $subject) }}">{{ $subject->name }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="admin-card">
    <form action="{{ route('admin.subjects.update', $subject) }}" method="POST" id="subjectForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <div class="admin-card mb-4">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="mb-0">Subject Information</h6>
                            <div class="badge bg-light text-dark">
                                ID: {{ $subject->id }}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Subject Name *</label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $subject->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Subject Code *</label>
                                <input type="text" 
                                       name="code" 
                                       id="code" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code', $subject->code) }}" 
                                       required 
                                       style="text-transform:uppercase">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" 
                                          id="description" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          rows="3">{{ old('description', $subject->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Subject Stats -->
                <div class="admin-card">
                    <div class="card-header">
                        <h6 class="mb-0">Subject Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <div class="h4 mb-1">{{ $subject->topics_count ?? 0 }}</div>
                                    <small class="text-muted">Topics</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <div class="h4 mb-1">{{ $subject->questions_count ?? 0 }}</div>
                                    <small class="text-muted">Questions</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <div class="h4 mb-1">0</div>
                                    <small class="text-muted">Exams</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <div class="h4 mb-1">0</div>
                                    <small class="text-muted">Attempts</small>
                                </div>
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
                                   value="{{ old('order', $subject->order) }}" 
                                   min="0">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="icon_class" class="form-label">Icon Class</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    @if($subject->icon_class)
                                    <i class="{{ $subject->icon_class }}"></i>
                                    @else
                                    <i class="ri-palette-line"></i>
                                    @endif
                                </span>
                                <input type="text" 
                                       name="icon_class" 
                                       id="icon_class" 
                                       class="form-control @error('icon_class') is-invalid @enderror" 
                                       value="{{ old('icon_class', $subject->icon_class) }}">
                            </div>
                            @error('icon_class')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="is_active" 
                                       id="is_active" 
                                       value="1" 
                                       {{ old('is_active', $subject->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Subject
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-0">
                            <label class="form-label">Created</label>
                            <div class="text-muted small">
                                {{ $subject->created_at ? $subject->created_at->format('M d, Y \a\t h:i A') : 'N/A' }}
                            </div>
                        </div>
                        
                        @if($subject->updated_at && $subject->updated_at != $subject->created_at)
                        <div class="mt-2">
                            <label class="form-label">Last Updated</label>
                            <div class="text-muted small">
                                {{ $subject->updated_at->format('M d, Y \a\t h:i A') }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="admin-card">
                    <div class="card-header">
                        <h6 class="mb-0">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.subjects.show', $subject) }}" class="btn-admin btn-admin-secondary">
                                <i class="ri-eye-line me-2"></i> View Details
                            </a>
                            <button type="button" 
                                    class="btn-admin btn-admin-danger" 
                                    onclick="confirmDelete('{{ $subject->name }}')">
                                <i class="ri-delete-bin-line me-2"></i> Delete Subject
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="{{ route('admin.subjects.index') }}" class="btn-admin btn-admin-secondary">
                <i class="ri-arrow-left-line me-2"></i> Back to List
            </a>
            
            <div class="d-flex gap-2">
                <a href="{{ route('admin.subjects.show', $subject) }}" class="btn-admin btn-admin-secondary">
                    <i class="ri-close-line me-2"></i> Cancel
                </a>
                <button type="submit" class="btn-admin btn-admin-primary">
                    <i class="ri-save-line me-2"></i> Update Subject
                </button>
            </div>
        </div>
    </form>
    
    <!-- Delete Form (hidden) -->
    <form id="deleteForm" action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(subjectName) {
    if (confirm(`Are you sure you want to delete "${subjectName}"? This will also delete all associated topics and questions. This action cannot be undone.`)) {
        document.getElementById('deleteForm').submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-uppercase subject code
    const codeInput = document.getElementById('code');
    if (codeInput) {
        codeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
    
    // Form validation
    const form = document.getElementById('subjectForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const code = document.getElementById('code').value.trim();
            
            if (!name || !code) {
                e.preventDefault();
                alert('Subject Name and Code are required.');
                return false;
            }
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="ri-loader-4-line spin me-2"></i> Updating...';
            submitBtn.disabled = true;
        });
    }
});
</script>
@endpush