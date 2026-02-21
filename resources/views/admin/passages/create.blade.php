@extends('layouts.admin')

@section('title', 'Add Passage - A-plus CBT')
@section('page-title', 'Add New Comprehension Passage')
@section('mobile-title', 'New Passage')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.passages.index') }}">Passages</a></li>
<li class="breadcrumb-item active">Add Passage</li>
@endsection

@section('content')
<div class="admin-card">
    <form action="{{ route('admin.passages.store') }}" method="POST" enctype="multipart/form-data" id="passageForm">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Main Passage Card -->
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
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
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
                                       value="{{ old('title') }}" 
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
                                          rows="8" 
                                          placeholder="Enter the comprehension passage here...">{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">You can use HTML formatting for paragraphs, lists, etc.</small>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="instruction" class="form-label">Instruction (Optional)</label>
                                <input type="text" 
                                       name="instruction" 
                                       id="instruction" 
                                       class="form-control @error('instruction') is-invalid @enderror" 
                                       value="{{ old('instruction') }}" 
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
                                       value="{{ old('source') }}" 
                                       placeholder="e.g., USE OF ENGLISH 1979, JAMB 2020">
                                @error('source')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="passage_image" class="form-label">Passage Image (Optional)</label>
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
                
                <!-- Questions Section (Optional for now) -->
                <div class="admin-card mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">Add Questions (Optional - You can add later)</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            You can add questions to this passage after creating it. Questions will automatically be linked to this passage.
                        </div>
                        
                        <div class="text-center py-3">
                            <i class="ri-questionnaire-line text-muted" style="font-size: 48px;"></i>
                            <p class="text-muted mt-2">Questions can be added from the passage detail page</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Settings Card -->
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
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Passage
                                </label>
                            </div>
                            <small class="text-muted">Inactive passages won't be available in exams</small>
                        </div>
                        
                        <hr>
                        
                        <div class="alert alert-warning">
                            <i class="ri-lightbulb-line me-2"></i>
                            <strong>Tips:</strong>
                            <ul class="mb-0 mt-2 small">
                                <li>Use clear, readable passages</li>
                                <li>Include the source/year for reference</li>
                                <li>You can add images to illustrate the passage</li>
                                <li>Add questions after creating the passage</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Preview Card -->
                <div class="admin-card">
                    <div class="card-header">
                        <h6 class="mb-0">Quick Preview</h6>
                    </div>
                    <div class="card-body">
                        <div id="previewContent" class="small text-muted">
                            <p>Passage preview will appear here as you type...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <a href="{{ route('admin.passages.index') }}" class="btn-admin btn-admin-secondary w-100 w-md-auto">
                        <i class="ri-arrow-left-line me-2"></i> Cancel
                    </a>
                </div>
                
                <div class="col-md-6">
                    <div class="d-flex flex-column flex-md-row gap-2 justify-content-md-end">
                        <button type="reset" class="btn-admin btn-admin-secondary flex-fill flex-md-auto">
                            <i class="ri-restart-line me-2"></i> Reset
                        </button>
                        <button type="submit" class="btn-admin btn-admin-primary flex-fill flex-md-auto">
                            <i class="ri-save-line me-2"></i> Save Passage
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
#previewContent {
    min-height: 150px;
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 1rem;
    background: #f8f9fa;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>

<script>
$(document).ready(function() {
    // Initialize TinyMCE
    tinymce.init({
        selector: '#content',
        license_key: 'gpl',
        height: 400,
        menubar: false,
        plugins: 'lists link image table code help wordcount',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table | code help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, sans-serif; font-size: 15px; line-height: 1.6; }',
        skin_url: '{{ asset("js/tinymce/skins/ui/oxide") }}',
        content_css: '{{ asset("js/tinymce/skins/content/default/content.css") }}',
        setup: function(editor) {
            editor.on('change', function() {
                updatePreview();
            });
        }
    });
    
    // Initialize Select2
    $('#subject_id').select2({
        placeholder: 'Select Subject',
        allowClear: true
    });
    
    $('#topic_id').select2({
        placeholder: 'Select Topic (Optional)',
        allowClear: true
    });
    
    // Load topics when subject changes
    $('#subject_id').change(function() {
        const subjectId = $(this).val();
        const topicSelect = $('#topic_id');
        
        if (subjectId) {
            $.ajax({
                url: '{{ route("admin.passages.get-topics-by-subject", ":subjectId") }}'.replace(':subjectId', subjectId),
                method: 'GET',
                success: function(topics) {
                    topicSelect.empty().append('<option value="">Select Topic (Optional)</option>');
                    topics.forEach(function(topic) {
                        topicSelect.append(`<option value="${topic.id}">${topic.name}</option>`);
                    });
                    topicSelect.trigger('change');
                }
            });
        } else {
            topicSelect.empty().append('<option value="">Select Topic (Optional)</option>');
        }
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
    
    // Update preview
    function updatePreview() {
        const content = tinymce.get('content').getContent();
        const preview = $('#previewContent');
        
        if (content.trim()) {
            preview.html(content);
            preview.removeClass('text-muted');
        } else {
            preview.html('<p class="text-muted">Passage preview will appear here as you type...</p>');
        }
    }
    
    // Form validation
    $('#passageForm').submit(function(e) {
        if (!tinymce.get('content').getContent().trim()) {
            e.preventDefault();
            alert('Please enter the passage content.');
            tinymce.get('content').focus();
            return false;
        }
    });
});
</script>
@endpush