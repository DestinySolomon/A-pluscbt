@extends('layouts.admin')

@section('title', 'Edit Passage - A-plus CBT')
@section('page-title', 'Edit Passage')
@section('mobile-title', 'Edit Passage')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.passages.index') }}">Passages</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.passages.show', $passage) }}">{{ $passage->title ?? 'Passage' }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="admin-card">
    <form action="{{ route('admin.passages.update', $passage) }}" method="POST" enctype="multipart/form-data" id="passageForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <div class="admin-card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Passage Content</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="subject_id" class="form-label">Subject *</label>
                                <select name="subject_id" 
                                        id="subject_id" 
                                        class="form-select @error('subject_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" 
                                            {{ old('subject_id', $passage->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }} ({{ $subject->code }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="topic_id" class="form-label">Topic (Optional)</label>
                                <select name="topic_id" 
                                        id="topic_id" 
                                        class="form-select @error('topic_id') is-invalid @enderror">
                                    <option value="">Select Topic (Optional)</option>
                                    @foreach($topics as $topic)
                                    <option value="{{ $topic->id }}" 
                                            {{ old('topic_id', $passage->topic_id) == $topic->id ? 'selected' : '' }}>
                                        {{ $topic->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('topic_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="title" class="form-label">Passage Title (Optional)</label>
                                <input type="text" 
                                       name="title" 
                                       id="title" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title', $passage->title) }}" 
                                       placeholder="e.g., Foreign Language Learning, PASSAGE A">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="content" class="form-label">Passage Content *</label>
                                <textarea name="content" 
                                          id="content" 
                                          class="form-control @error('content') is-invalid @enderror" 
                                          rows="8">{{ old('content', $passage->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="instruction" class="form-label">Instruction (Optional)</label>
                                <input type="text" 
                                       name="instruction" 
                                       id="instruction" 
                                       class="form-control @error('instruction') is-invalid @enderror" 
                                       value="{{ old('instruction', $passage->instruction) }}" 
                                       placeholder="e.g., Read each passage and answer the questions that follow">
                                @error('instruction')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="source" class="form-label">Source/Reference (Optional)</label>
                                <input type="text" 
                                       name="source" 
                                       id="source" 
                                       class="form-control @error('source') is-invalid @enderror" 
                                       value="{{ old('source', $passage->source) }}" 
                                       placeholder="e.g., USE OF ENGLISH 1979, JAMB 2020">
                                @error('source')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Passage Image</label>
                                
                                @if($passage->image_path)
                                <div class="mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ Storage::url($passage->image_path) }}" 
                                             alt="Current Image" 
                                             class="img-thumbnail" 
                                             style="max-width: 150px;">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="remove_image" 
                                                   id="remove_image" 
                                                   value="1">
                                            <label class="form-check-label text-danger" for="remove_image">
                                                Remove current image
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                <input type="file" 
                                       name="passage_image" 
                                       id="passage_image" 
                                       class="form-control @error('passage_image') is-invalid @enderror" 
                                       accept="image/*">
                                @error('passage_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img id="previewImage" src="#" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
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
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="is_active" 
                                       id="is_active" 
                                       value="1" 
                                       {{ old('is_active', $passage->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Passage
                                </label>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <label class="form-label">Questions Count</label>
                            <div class="h4">{{ $passage->questions_count }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Created</label>
                            <div class="text-muted small">{{ $passage->created_at->format('M d, Y h:i A') }}</div>
                        </div>
                        
                        @if($passage->updated_at != $passage->created_at)
                        <div class="mb-3">
                            <label class="form-label">Last Updated</label>
                            <div class="text-muted small">{{ $passage->updated_at->format('M d, Y h:i A') }}</div>
                        </div>
                        @endif
                        
                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            <strong>Note:</strong> Editing this passage will affect all questions linked to it.
                        </div>
                    </div>
                </div>
                
                <div class="admin-card">
                    <div class="card-header">
                        <h6 class="mb-0">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.passages.show', $passage) }}" class="btn-admin btn-admin-secondary">
                                <i class="ri-eye-line me-2"></i> View Passage
                            </a>
                            <a href="{{ route('admin.passages.create') }}?subject_id={{ $passage->subject_id }}" 
                               class="btn-admin btn-admin-secondary">
                                <i class="ri-add-line me-2"></i> Add New Passage
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <a href="{{ route('admin.passages.show', $passage) }}" class="btn-admin btn-admin-secondary w-100 w-md-auto">
                        <i class="ri-arrow-left-line me-2"></i> Cancel
                    </a>
                </div>
                
                <div class="col-md-6">
                    <div class="d-flex flex-column flex-md-row gap-2 justify-content-md-end">
                        <button type="submit" class="btn-admin btn-admin-primary flex-fill flex-md-auto">
                            <i class="ri-save-line me-2"></i> Update Passage
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/YOUR_TINY_API_KEY/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
$(document).ready(function() {
    // Initialize TinyMCE
    tinymce.init({
        selector: '#content',
        height: 400,
        plugins: 'lists link image table code help wordcount',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, sans-serif; font-size: 15px; line-height: 1.6; }'
    });
    
    // Image preview
    $('#passage_image').change(function() {
        const file = this.files[0];
        const preview = $('#imagePreview');
        const previewImage = $('#previewImage');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.attr('src', e.target.result);
                preview.show();
            }
            reader.readAsDataURL(file);
        } else {
            preview.hide();
        }
    });
});
</script>
@endpush