@extends('layouts.admin')

@section('title', 'Add Question - A-plus CBT')
@section('page-title', 'Add New Question')
@section('mobile-title', 'New Question')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.questions.index') }}">Questions</a></li>
<li class="breadcrumb-item active">Add Question</li>
@endsection

@section('content')
<div class="admin-card">
    <form action="{{ route('admin.questions.store') }}" method="POST" id="questionForm" enctype="multipart/form-data" novalidate>
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <div class="admin-card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Question Details</h6>
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
                                            {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
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
                                    <option value="">No Topic</option>
                                    @if(old('subject_id'))
                                        @php
                                            $topics = \App\Models\Topic::where('subject_id', old('subject_id'))
                                                                       ->active()
                                                                       ->get();
                                        @endphp
                                        @foreach($topics as $topic)
                                        <option value="{{ $topic->id }}" {{ old('topic_id') == $topic->id ? 'selected' : '' }}>
                                            {{ $topic->name }}
                                        </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('topic_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave empty for general questions</small>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="question_text" class="form-label">Question Text *</label>
                                <textarea name="question_text" 
                                          id="question_text" 
                                          class="form-control @error('question_text') is-invalid @enderror" 
                                          rows="4" 
                                          placeholder="Enter the question here...">{{ old('question_text') }}</textarea>
                                @error('question_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">You can use HTML formatting for tables, lists, etc.</small>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="question_image" class="form-label">Question Image (Optional)</label>
                                <input type="file" 
                                       name="question_image" 
                                       id="question_image" 
                                       class="form-control @error('question_image') is-invalid @enderror" 
                                       accept="image/*">
                                @error('question_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Max 2MB. Supported: jpeg, png, jpg, gif, svg</small>
                                
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img id="previewImage" src="#" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="marks" class="form-label">Marks *</label>
                                <input type="number" 
                                       name="marks" 
                                       id="marks" 
                                       class="form-control @error('marks') is-invalid @enderror" 
                                       value="{{ old('marks', 1) }}" 
                                       min="1" 
                                       max="10" 
                                       required>
                                @error('marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Score for this question (1-10)</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="time_estimate" class="form-label">Time Estimate (seconds) *</label>
                                <input type="number" 
                                       name="time_estimate" 
                                       id="time_estimate" 
                                       class="form-control @error('time_estimate') is-invalid @enderror" 
                                       value="{{ old('time_estimate', 60) }}" 
                                       min="10" 
                                       max="300" 
                                       required>
                                @error('time_estimate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Suggested time to answer (10-300 seconds)</small>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="explanation" class="form-label">Explanation (Optional)</label>
                                <textarea name="explanation" 
                                          id="explanation" 
                                          class="form-control @error('explanation') is-invalid @enderror" 
                                          rows="3" 
                                          placeholder="Explain why the correct answer is right...">{{ old('explanation') }}</textarea>
                                @error('explanation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Help students understand the reasoning</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Options Section -->
                <div class="admin-card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Options (JAMB Style - 5 Options Required)</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            <strong>JAMB Format:</strong> 5 options (A, B, C, D, E) with exactly 1 correct answer.
                        </div>
                        
                        @php
                            $optionLetters = ['A', 'B', 'C', 'D', 'E'];
                            $oldOptions = old('options', [
                                ['text' => '', 'image' => null],
                                ['text' => '', 'image' => null],
                                ['text' => '', 'image' => null],
                                ['text' => '', 'image' => null],
                                ['text' => '', 'image' => null]
                            ]);
                        @endphp
                        
                        @foreach($optionLetters as $index => $letter)
                        <div class="option-card mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Option {{ $letter }}</h6>
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="correct_option" 
                                           id="correct_option_{{ $index }}" 
                                           value="{{ $index }}"
                                           {{ old('correct_option') == $index ? 'checked' : '' }}
                                           required>
                                    <label class="form-check-label" for="correct_option_{{ $index }}">
                                        Correct Answer
                                    </label>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-8 mb-2">
                                    <label for="option_text_{{ $index }}" class="form-label small">Option Text *</label>
                                    <textarea name="options[{{ $index }}][text]" 
                                              id="option_text_{{ $index }}" 
                                              class="form-control @error('options.' . $index . '.text') is-invalid @enderror" 
                                              rows="2" 
                                              required 
                                              placeholder="Enter option {{ $letter }} text...">{{ $oldOptions[$index]['text'] ?? '' }}</textarea>
                                    @error('options.' . $index . '.text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mb-2">
                                    <label for="option_image_{{ $index }}" class="form-label small">Option Image (Optional)</label>
                                    <input type="file" 
                                           name="options[{{ $index }}][image]" 
                                           id="option_image_{{ $index }}" 
                                           class="form-control @error('options.' . $index . '.image') is-invalid @enderror" 
                                           accept="image/*">
                                    @error('options.' . $index . '.image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <div id="optionPreview_{{ $index }}" class="mt-2" style="display: none;">
                                        <img id="optionPreviewImage_{{ $index }}" src="#" alt="Preview" class="img-thumbnail" style="max-width: 100px; max-height: 80px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                        @error('correct_option')
                            <div class="alert alert-danger">
                                <i class="ri-error-warning-line me-2"></i>
                                {{ $message }}
                            </div>
                        @enderror
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
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Question
                                </label>
                            </div>
                            <small class="text-muted">Inactive questions won't be available in exams</small>
                        </div>
                        
                        <div class="alert alert-success">
                            <i class="ri-check-line me-2"></i>
                            <strong>Simplified:</strong> No difficulty levels, topics are optional.
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="ri-alert-line me-2"></i>
                            <strong>Important:</strong> Ensure you select the correct answer before saving.
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="ri-lightbulb-line me-2"></i>
                            <small>
                                <strong>Tip:</strong> Use clear, unambiguous language. 
                                Avoid "all of the above" or "none of the above" for JAMB-style questions.
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Preview Card -->
                <div class="admin-card">
                    <div class="card-header">
                        <h6 class="mb-0">Quick Preview</h6>
                    </div>
                    <div class="card-body">
                        <div id="previewContent" class="small text-muted">
                            <p>Question preview will appear here as you type...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <a href="{{ route('admin.questions.index') }}" class="btn-admin btn-admin-secondary w-100 w-md-auto">
                        <i class="ri-arrow-left-line me-2"></i> Cancel
                    </a>
                </div>
                
                <div class="col-md-6">
                    <div class="d-flex flex-column flex-md-row gap-2 justify-content-md-end">
                        <button type="reset" class="btn-admin btn-admin-secondary flex-fill flex-md-auto">
                            <i class="ri-restart-line me-2"></i> Reset
                        </button>
                        <button type="submit" class="btn-admin btn-admin-primary flex-fill flex-md-auto">
                            <i class="ri-save-line me-2"></i> Save Question
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<!-- TinyMCE CSS (will be loaded from CDN in script) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.option-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    background: #f8f9fa;
    transition: border-color 0.2s;
}

.option-card:hover {
    border-color: #14b8a6;
}

.form-check-input:checked {
    background-color: #14b8a6;
    border-color: #14b8a6;
}

#previewContent {
    min-height: 100px;
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 0.75rem;
    background: #f8f9fa;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .option-card {
        padding: 0.75rem;
    }
    
    .btn-admin {
        padding: 0.625rem 1rem;
        font-size: 14px;
    }
    
    .flex-fill {
        width: 100%;
    }
    
    .gap-2 {
        gap: 1rem !important;
    }
}

@media (min-width: 768px) {
    .flex-md-auto {
        width: auto !important;
        min-width: 140px;
    }
    
    .w-md-auto {
        width: auto !important;
    }
}
</style>
@endpush

@push('scripts')
<!-- TinyMCE -->
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE for question text
   tinymce.init({
    selector: '#question_text',
     license_key: 'gpl',
    height: 200,
    menubar: false,
    plugins: 'lists link image table code help wordcount',
    toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table | code help',
    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, sans-serif; font-size: 14px; }',
    // Add these lines for self-hosted:
    skin_url: '{{ asset("js/tinymce/skins/ui/oxide") }}',
    content_css: '{{ asset("js/tinymce/skins/content/default/content.css") }}',
    // Remove image upload for now (we'll add it back later)
    // images_upload_url: '{{ route("admin.questions.upload-image") }}',
    // images_upload_credentials: true,
    // automatic_uploads: true,
    // file_picker_types: 'image',
    setup: function(editor) {
        editor.on('change', function() {
            updatePreview();
        });
    }
    
});
    
    // Initialize Select2 for subject and topic
    $('#subject_id').select2({
        placeholder: 'Select a Subject',
        allowClear: true
    });
    
    $('#topic_id').select2({
        placeholder: 'No Topic (Optional)',
        allowClear: true
    });
    
    // Load topics when subject changes
    $('#subject_id').change(function() {
        const subjectId = $(this).val();
        const topicSelect = $('#topic_id');
        
        if (subjectId) {
            // Load topics via AJAX
            $.ajax({
                url: '{{ route("admin.questions.get-topics-by-subject", ":subjectId") }}'.replace(':subjectId', subjectId),
                method: 'GET',
                success: function(topics) {
                    topicSelect.empty();
                    topicSelect.append('<option value="">No Topic</option>');
                    
                    topics.forEach(function(topic) {
                        topicSelect.append(`<option value="${topic.id}">${topic.name}</option>`);
                    });
                    
                    // Re-initialize Select2
                    topicSelect.select2({
                        placeholder: 'No Topic (Optional)',
                        allowClear: true
                    });
                },
                error: function() {
                    topicSelect.empty();
                    topicSelect.append('<option value="">No Topic</option>');
                    topicSelect.select2({
                        placeholder: 'No Topic (Optional)',
                        allowClear: true
                    });
                }
            });
        } else {
            topicSelect.empty();
            topicSelect.append('<option value="">No Topic</option>');
            topicSelect.select2({
                placeholder: 'No Topic (Optional)',
                allowClear: true
            });
        }
    });
    
    // Image preview for question image
    $('#question_image').change(function() {
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
    
    // Image preview for option images
    @foreach(['A', 'B', 'C', 'D', 'E'] as $index => $letter)
    $('#option_image_{{ $index }}').change(function() {
        const file = this.files[0];
        const preview = $('#optionPreview_{{ $index }}');
        const previewImage = $('#optionPreviewImage_{{ $index }}');
        
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
    @endforeach
    
    // Update preview as user types
    function updatePreview() {
        const preview = $('#previewContent');
        
        // Check if TinyMCE is initialized before trying to get content
        const editor = tinymce.get('question_text');
        let questionText = '';
        
        if (editor) {
            questionText = editor.getContent();
        } else {
            questionText = document.getElementById('question_text').value;
        }
        
        if (questionText.trim()) {
            preview.html(questionText);
            preview.removeClass('text-muted');
        } else {
            preview.html('<p class="text-muted">Question preview will appear here as you type...</p>');
            preview.addClass('text-muted');
        }
    }
    
    // Update preview on option text changes
    @foreach(['A', 'B', 'C', 'D', 'E'] as $index => $letter)
    $('#option_text_{{ $index }}').on('input', function() {
        updatePreview();
    });
    @endforeach
    
    // Form validation
    const form = document.getElementById('questionForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission immediately
            
            // CRITICAL: Sync TinyMCE content back to textarea before validation
            if (tinymce.get('question_text')) {
                tinymce.triggerSave();
            }
            
            const subjectId = document.getElementById('subject_id').value.trim();
            const questionTextArea = document.getElementById('question_text');
            const questionText = questionTextArea.value.trim();
            const correctOption = document.querySelector('input[name="correct_option"]:checked');
            
            // Check required fields
            if (!subjectId) {
                alert('Please select a subject.');
                document.getElementById('subject_id').focus();
                return false;
            }
            
            if (!questionText) {
                alert('Please enter the question text.');
                return false;
            }
            
            if (!correctOption) {
                alert('Please select the correct answer option.');
                return false;
            }
            
            // Check all options have text
            let allOptionsFilled = true;
            let emptyOptionIndex = -1;
            for (let i = 0; i < 5; i++) {
                const optionText = document.getElementById('option_text_' + i).value.trim();
                if (!optionText) {
                    allOptionsFilled = false;
                    emptyOptionIndex = i;
                    break;
                }
            }
            
            if (!allOptionsFilled) {
                const letters = ['A', 'B', 'C', 'D', 'E'];
                alert(`Please fill in option ${letters[emptyOptionIndex]}. All 5 options are required.`);
                document.getElementById('option_text_' + emptyOptionIndex).focus();
                return false;
            }
            
            // All validation passed, now submit the form
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="ri-loader-4-line spin me-2"></i> Saving...';
            submitBtn.disabled = true;
            
            // Enable the form to actually submit
            form.removeEventListener('submit', arguments.callee);
            
            // Set a timer to re-enable button if something goes wrong
            const enableButtonTimeout = setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                alert('Form submission took too long. Please check your connection and try again.');
            }, 30000); // 30 seconds timeout
            
            // Listen for page unload (successful submission)
            const unloadHandler = () => {
                clearTimeout(enableButtonTimeout);
                window.removeEventListener('beforeunload', unloadHandler);
            };
            window.addEventListener('beforeunload', unloadHandler);
            
            // Submit the form
            form.submit();
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
@endpush