@extends('layouts.admin')

@section('title', 'Edit Question - A-plus CBT')
@section('page-title', 'Edit Question: #' . $question->id)
@section('mobile-title', 'Edit Question')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.questions.index') }}">Questions</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.questions.show', $question) }}">Question #{{ $question->id }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="admin-card">
    <form action="{{ route('admin.questions.update', $question) }}" method="POST" id="questionForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <div class="admin-card mb-4">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="mb-0">Question Details</h6>
                            <div class="badge bg-light text-dark">
                                ID: {{ $question->id }}
                            </div>
                        </div>
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
                                            {{ old('subject_id', $question->subject_id) == $subject->id ? 'selected' : '' }}>
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
                                    <option value="">Select a Topic</option>
                                    @foreach($topics as $topic)
                                    <option value="{{ $topic->id }}" 
                                            {{ old('topic_id', $question->topic_id) == $topic->id ? 'selected' : '' }}>
                                        {{ $topic->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('topic_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="question_text" class="form-label">Question Text *</label>
                                <textarea name="question_text" 
                                          id="question_text" 
                                          class="form-control @error('question_text') is-invalid @enderror" 
                                          rows="4" 
                                          required>{{ old('question_text', $question->question_text) }}</textarea>
                                @error('question_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label">Question Image</label>
                                
                                @if($question->image_path)
                                <div class="mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ Storage::url($question->image_path) }}" 
                                             alt="Current Image" 
                                             class="img-thumbnail" 
                                             style="max-width: 150px; max-height: 100px;">
                                        <div>
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="remove_question_image" 
                                                       id="remove_question_image" 
                                                       value="1">
                                                <label class="form-check-label text-danger" for="remove_question_image">
                                                    Remove current image
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                <input type="file" 
                                       name="question_image" 
                                       id="question_image" 
                                       class="form-control @error('question_image') is-invalid @enderror" 
                                       accept="image/*">
                                @error('question_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror>
                                <small class="text-muted">Leave empty to keep current image. Max 2MB. Supported: jpeg, png, jpg, gif, svg</small>
                                
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img id="previewImage" src="#" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="difficulty" class="form-label">Difficulty *</label>
                                <select name="difficulty" 
                                        id="difficulty" 
                                        class="form-select @error('difficulty') is-invalid @enderror" 
                                        required>
                                    <option value="">Select Difficulty</option>
                                    <option value="easy" {{ old('difficulty', $question->difficulty) == 'easy' ? 'selected' : '' }}>Easy</option>
                                    <option value="medium" {{ old('difficulty', $question->difficulty) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="hard" {{ old('difficulty', $question->difficulty) == 'hard' ? 'selected' : '' }}>Hard</option>
                                </select>
                                @error('difficulty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="marks" class="form-label">Marks *</label>
                                <input type="number" 
                                       name="marks" 
                                       id="marks" 
                                       class="form-control @error('marks') is-invalid @enderror" 
                                       value="{{ old('marks', $question->marks) }}" 
                                       min="1" 
                                       max="10" 
                                       required>
                                @error('marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="time_estimate" class="form-label">Time Estimate (seconds) *</label>
                                <input type="number" 
                                       name="time_estimate" 
                                       id="time_estimate" 
                                       class="form-control @error('time_estimate') is-invalid @enderror" 
                                       value="{{ old('time_estimate', $question->time_estimate) }}" 
                                       min="10" 
                                       max="300" 
                                       required>
                                @error('time_estimate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="explanation" class="form-label">Explanation (Optional)</label>
                                <textarea name="explanation" 
                                          id="explanation" 
                                          class="form-control @error('explanation') is-invalid @enderror" 
                                          rows="3">{{ old('explanation', $question->explanation) }}</textarea>
                                @error('explanation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Options Section -->
                <div class="admin-card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Options (JAMB Style - 4 Options Required)</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $optionLetters = ['A', 'B', 'C', 'D'];
                            $options = $question->options->keyBy('option_letter');
                            $oldOptions = old('options', []);
                        @endphp
                        
                        @foreach($optionLetters as $index => $letter)
                        @php
                            $option = $options->get($letter);
                            $optionText = old("options.{$index}.text", $option ? $option->option_text : '');
                            $isCorrect = old('correct_option', $option && $option->is_correct ? $index : '');
                        @endphp
                        
                        <div class="option-card mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Option {{ $letter }}</h6>
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="correct_option" 
                                           id="correct_option_{{ $index }}" 
                                           value="{{ $index }}"
                                           {{ $isCorrect == $index ? 'checked' : '' }}
                                           required>
                                    <label class="form-check-label" for="correct_option_{{ $index }}">
                                        Correct Answer
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Current Image -->
                            @if($option && $option->image_path)
                            <div class="mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ Storage::url($option->image_path) }}" 
                                         alt="Current Image" 
                                         class="img-thumbnail" 
                                         style="max-width: 100px; max-height: 80px;">
                                    <div>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="remove_option_image[{{ $index }}]" 
                                                   id="remove_option_image_{{ $index }}" 
                                                   value="1">
                                            <label class="form-check-label text-danger small" for="remove_option_image_{{ $index }}">
                                                Remove image
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <div class="row">
                                <div class="col-md-8 mb-2">
                                    <label for="option_text_{{ $index }}" class="form-label small">Option Text *</label>
                                    <textarea name="options[{{ $index }}][text]" 
                                              id="option_text_{{ $index }}" 
                                              class="form-control @error('options.' . $index . '.text') is-invalid @enderror" 
                                              rows="2" 
                                              required>{{ $optionText }}</textarea>
                                    @error('options.' . $index . '.text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror>
                                </div>
                                
                                <div class="col-md-4 mb-2">
                                    <label for="option_image_{{ $index }}" class="form-label small">New Image (Optional)</label>
                                    <input type="file" 
                                           name="options[{{ $index }}][image]" 
                                           id="option_image_{{ $index }}" 
                                           class="form-control @error('options.' . $index . '.image') is-invalid @enderror" 
                                           accept="image/*">
                                    @error('options.' . $index . '.image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror>
                                    
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
                        @enderror>
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
                                       {{ old('is_active', $question->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Question
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Times Answered</label>
                            <div class="h5">{{ $question->times_answered }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Success Rate</label>
                            <div class="h5">
                                @if($question->times_answered > 0)
                                {{ number_format($question->success_rate, 1) }}%
                                @else
                                0%
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-0">
                            <label class="form-label">Created</label>
                            <div class="text-muted small">
                                {{ $question->created_at ? $question->created_at->format('M d, Y \a\t h:i A') : 'N/A' }}
                            </div>
                        </div>
                        
                        @if($question->updated_at && $question->updated_at != $question->created_at)
                        <div class="mt-2">
                            <label class="form-label">Last Updated</label>
                            <div class="text-muted small">
                                {{ $question->updated_at->format('M d, Y \a\t h:i A') }}
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
                            <a href="{{ route('admin.questions.show', $question) }}" class="btn-admin btn-admin-secondary">
                                <i class="ri-eye-line me-2"></i> View Details
                            </a>
                            <a href="{{ route('admin.questions.create') }}?subject_id={{ $question->subject_id }}&topic_id={{ $question->topic_id }}" 
                               class="btn-admin btn-admin-secondary">
                                <i class="ri-add-line me-2"></i> Add Similar
                            </a>
                            <button type="button" 
                                    class="btn-admin btn-admin-danger" 
                                    onclick="confirmDelete('Question #{{ $question->id }}')">
                                <i class="ri-delete-bin-line me-2"></i> Delete Question
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <a href="{{ route('admin.questions.index') }}" class="btn-admin btn-admin-secondary w-100 w-md-auto">
                        <i class="ri-arrow-left-line me-2"></i> Back to List
                    </a>
                </div>
                
                <div class="col-md-6">
                    <div class="d-flex flex-column flex-md-row gap-2 justify-content-md-end">
                        <a href="{{ route('admin.questions.show', $question) }}" class="btn-admin btn-admin-secondary flex-fill flex-md-auto">
                            <i class="ri-close-line me-2"></i> Cancel
                        </a>
                        <button type="submit" class="btn-admin btn-admin-primary flex-fill flex-md-auto">
                            <i class="ri-save-line me-2"></i> Update Question
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <!-- Delete Form (hidden) -->
    <form id="deleteForm" action="{{ route('admin.questions.destroy', $question) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

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
        height: 200,
         license_key: 'gpl',
        menubar: false,
        plugins: 'lists link image table code help wordcount',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table | code help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, sans-serif; font-size: 14px; }',
        images_upload_url: '{{ route("admin.questions.upload-image") }}',
        images_upload_credentials: true,
        automatic_uploads: true,
        file_picker_types: 'image'



           //hidden_input: false,  // This prevents TinyMCE from hiding the textarea
    
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
        placeholder: 'Select a Topic (Optional)',
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
                    topicSelect.append('<option value="">Select a Topic (Optional)</option>');
                    
                    topics.forEach(function(topic) {
                        topicSelect.append(`<option value="${topic.id}">${topic.name}</option>`);
                    });
                    
                    // Select current topic if exists
                    const currentTopicId = '{{ $question->topic_id }}';
                    if (currentTopicId) {
                        topicSelect.val(currentTopicId).trigger('change');
                    }
                    
                    // Re-initialize Select2
                    topicSelect.select2({
                        placeholder: 'Select a Topic (Optional)',
                        allowClear: true
                    });
                }
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
    @foreach($optionLetters as $index => $letter)
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
    
    // Form validation
    const form = document.getElementById('questionForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const subjectId = document.getElementById('subject_id').value;
            const questionText = tinymce.get('question_text').getContent({format: 'text'}).trim();
            const difficulty = document.getElementById('difficulty').value;
            const correctOption = document.querySelector('input[name="correct_option"]:checked');
            
            // Check required fields
            if (!subjectId || !questionText || !difficulty || !correctOption) {
                e.preventDefault();
                alert('Please fill in all required fields (Subject, Question Text, Difficulty, and select Correct Answer).');
                return false;
            }
            
            // Check all options have text
            let allOptionsFilled = true;
            @foreach($optionLetters as $index => $letter)
            const optionText{{ $index }} = document.getElementById('option_text_{{ $index }}').value.trim();
            if (!optionText{{ $index }}) {
                allOptionsFilled = false;
            }
            @endforeach
            
            if (!allOptionsFilled) {
                e.preventDefault();
                alert('Please fill in text for all 4 options (A, B, C, D).');
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

function confirmDelete(questionText) {
    if (confirm(`Are you sure you want to delete ${questionText}? This will also delete all associated options and answers. This action cannot be undone.`)) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush