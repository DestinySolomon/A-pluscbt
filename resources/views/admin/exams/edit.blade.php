@extends('layouts.admin')

@section('title', 'Edit Exam - A-plus CBT')
@section('page-title', 'Edit Exam')
@section('mobile-title', 'Edit: ' . Str::limit($exam->name, 15))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{ route('admin.exams.index') }}">Exams</a>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('admin.exams.show', $exam->id) }}">{{ Str::limit($exam->name, 15) }}</a>
</li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2">
    <button type="submit" form="examForm" class="btn-admin btn-admin-primary">
        <i class="ri-save-line me-2"></i> Save Changes
    </button>
    <a href="{{ route('admin.exams.show', $exam->id) }}" class="btn-admin btn-admin-secondary">
        <i class="ri-arrow-left-line me-2"></i> Cancel
    </a>
</div>
@endsection

@section('content')
<div class="admin-card">
    <div class="card-body">
        <form action="{{ route('admin.exams.update', $exam->id) }}" method="POST" id="examForm">
            @csrf
            @method('PUT')
            
            <!-- Hidden fields for required data -->
            <input type="hidden" name="type" value="{{ $exam->type }}">
            
            <div class="row">
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="admin-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Basic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label required">Exam Name</label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $exam->name) }}" 
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="code" class="form-label required">Exam Code</label>
                                        <input type="text" 
                                               class="form-control @error('code') is-invalid @enderror" 
                                               id="code" 
                                               name="code" 
                                               value="{{ old('code', $exam->code) }}" 
                                               required>
                                        <small class="text-muted">Unique identifier for the exam</small>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" 
                                                  name="description" 
                                                  rows="3">{{ old('description', $exam->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subject Configuration -->
                    <div class="admin-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">JAMB Subject Configuration</h5>
                            <p class="text-muted mb-0">Standard JAMB format: English (60 questions) + 3 other subjects (40 each)</p>
                        </div>
                        <div class="card-body">
                            <!-- English Subject -->
                            <div class="mb-4">
                                <h6 class="mb-3 text-primary">
                                    <i class="ri-book-line me-2"></i>English Language (Compulsory)
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="english_subject_id" class="form-label required">Select English Subject</label>
                                            <select class="form-select @error('english_subject_id') is-invalid @enderror" 
                                                    id="english_subject_id" 
                                                    name="english_subject_id" 
                                                    required>
                                                <option value="">-- Select English Subject --</option>
                                                @if($englishSubjects && count($englishSubjects) > 0)
                                                    @foreach($englishSubjects as $subject)
                                                        <option value="{{ $subject->id }}" 
                                                                {{ old('english_subject_id', $englishSubject->id ?? '') == $subject->id ? 'selected' : '' }}>
                                                            {{ $subject->name }} (Code: {{ $subject->code }})
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option value="">No English subjects found</option>
                                                @endif
                                            </select>
                                            @error('english_subject_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="english_question_count" class="form-label required">Questions</label>
                                            <input type="number" 
                                                   class="form-control @error('english_question_count') is-invalid @enderror" 
                                                   id="english_question_count" 
                                                   name="english_question_count" 
                                                   value="{{ old('english_question_count', $englishSubject->pivot->question_count ?? 60) }}" 
                                                   min="1" 
                                                   max="100" 
                                                   required>
                                            @error('english_question_count')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Other Subjects -->
                            <div>
                                <h6 class="mb-3 text-primary">
                                    <i class="ri-book-2-line me-2"></i>Other Subjects (Select 3)
                                </h6>
                                <div id="otherSubjectsContainer">
                                    @php
                                        // Ensure we always have exactly 3 subject slots
                                        $otherSubjectsCount = max(count($otherExamSubjects), 3);
                                    @endphp
                                    @for($i = 0; $i < $otherSubjectsCount; $i++)
                                    <div class="subject-row mb-3 p-3 border rounded">
                                        <div class="row g-3">
                                            <div class="col-md-7">
                                                <div class="form-group">
                                                    <label class="form-label required">Subject {{ $i + 1 }}</label>
                                                    <select name="other_subjects[{{ $i }}][subject_id]" 
                                                            class="form-select subject-select @error('other_subjects.'.$i.'.subject_id') is-invalid @enderror" 
                                                            required>
                                                        <option value="">-- Select Subject --</option>
                                                        @if($otherSubjects && count($otherSubjects) > 0)
                                                            @foreach($otherSubjects as $subject)
                                                                <option value="{{ $subject->id }}" 
                                                                        {{ old('other_subjects.'.$i.'.subject_id', $otherExamSubjects[$i]->id ?? '') == $subject->id ? 'selected' : '' }}>
                                                                    {{ $subject->name }} (Code: {{ $subject->code }})
                                                                </option>
                                                            @endforeach
                                                        @else
                                                            <option value="">No subjects found</option>
                                                        @endif
                                                    </select>
                                                    @error('other_subjects.'.$i.'.subject_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="form-label required">Questions</label>
                                                    <input type="number" 
                                                           name="other_subjects[{{ $i }}][question_count]" 
                                                           class="form-control @error('other_subjects.'.$i.'.question_count') is-invalid @enderror" 
                                                           value="{{ old('other_subjects.'.$i.'.question_count', $otherExamSubjects[$i]->pivot->question_count ?? 40) }}" 
                                                           min="1" 
                                                           max="100" 
                                                           required>
                                                    @error('other_subjects.'.$i.'.question_count')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Exam Settings -->
                    <div class="admin-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Exam Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="duration_minutes" class="form-label required">Duration (Minutes)</label>
                                <input type="number" 
                                       class="form-control @error('duration_minutes') is-invalid @enderror" 
                                       id="duration_minutes" 
                                       name="duration_minutes" 
                                       value="{{ old('duration_minutes', $exam->duration_minutes) }}" 
                                       min="1" 
                                       max="240" 
                                       required>
                                @error('duration_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="passing_score" class="form-label required">Passing Score (%)</label>
                                <input type="number" 
                                       class="form-control @error('passing_score') is-invalid @enderror" 
                                       id="passing_score" 
                                       name="passing_score" 
                                       value="{{ old('passing_score', $exam->passing_score) }}" 
                                       min="0" 
                                       max="100" 
                                       required>
                                @error('passing_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="max_attempts" class="form-label">Maximum Attempts</label>
                                <input type="number" 
                                       class="form-control @error('max_attempts') is-invalid @enderror" 
                                       id="max_attempts" 
                                       name="max_attempts" 
                                       value="{{ old('max_attempts', $exam->max_attempts) }}" 
                                       min="0">
                                <small class="text-muted">0 = unlimited attempts</small>
                                @error('max_attempts')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Behavior Settings -->
                    <div class="admin-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Behavior Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="shuffle_questions" 
                                       name="shuffle_questions" 
                                       value="1" 
                                       {{ old('shuffle_questions', $exam->shuffle_questions) ? 'checked' : '' }}>
                                <label class="form-check-label" for="shuffle_questions">
                                    Shuffle Questions
                                </label>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="shuffle_options" 
                                       name="shuffle_options" 
                                       value="1" 
                                       {{ old('shuffle_options', $exam->shuffle_options) ? 'checked' : '' }}>
                                <label class="form-check-label" for="shuffle_options">
                                    Shuffle Options
                                </label>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="show_results_immediately" 
                                       name="show_results_immediately" 
                                       value="1" 
                                       {{ old('show_results_immediately', $exam->show_results_immediately) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_results_immediately">
                                    Show Results Immediately
                                </label>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $exam->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Status
                                </label>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_published" 
                                       name="is_published" 
                                       value="1" 
                                       {{ old('is_published', $exam->is_published) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_published">
                                    Published Status
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="admin-card">
                        <div class="card-header">
                            <h5 class="mb-0">Exam Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Questions:</span>
                                <span class="fw-medium" id="totalQuestionsCount">
                                    {{ $exam->total_questions }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Duration:</span>
                                <span class="fw-medium">{{ $exam->duration_minutes }} min</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Status:</span>
                                <span>
                                    @if($exam->is_published)
                                        <span class="badge bg-success">Published</span>
                                    @else
                                        <span class="badge bg-warning">Draft</span>
                                    @endif
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Attempts:</span>
                                <span class="fw-medium">{{ $exam->attempts_count }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Created:</span>
                                <span class="text-muted">{{ $exam->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Calculate total questions
    function calculateTotalQuestions() {
        let total = parseInt($('#english_question_count').val()) || 0;
        
        $('input[name^="other_subjects"]').each(function() {
            if ($(this).attr('name').includes('question_count')) {
                total += parseInt($(this).val()) || 0;
            }
        });
        
        $('#totalQuestionsCount').text(total);
    }
    
    // Initialize calculation
    calculateTotalQuestions();
    
    // Add event listeners
    $('#english_question_count').on('input', calculateTotalQuestions);
    $('input[name^="other_subjects"]').on('input', calculateTotalQuestions);
    
    // Prevent duplicate subject selection
    $('.subject-select').change(function() {
        const selectedSubjects = [];
        let hasDuplicates = false;
        
        // Add English subject first
        const englishSubjectId = $('#english_subject_id').val();
        if (englishSubjectId) {
            selectedSubjects.push(englishSubjectId);
        }
        
        // Check other subjects
        $('.subject-select').each(function() {
            const value = $(this).val();
            if (value) {
                if (selectedSubjects.includes(value)) {
                    hasDuplicates = true;
                    $(this).addClass('is-invalid');
                    $(this).siblings('.invalid-feedback').remove();
                    $(this).after('<div class="invalid-feedback">This subject has already been selected.</div>');
                } else {
                    selectedSubjects.push(value);
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.invalid-feedback').remove();
                }
            }
        });
        
        if (hasDuplicates) {
            return false;
        }
    });
    
    // Form validation
    $('#examForm').submit(function(e) {
        // Reset all error states
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        // Check for duplicate subjects
        const selectedSubjects = [];
        let hasDuplicates = false;
        let errorMessage = '';
        
        // Add English subject first
        const englishSubjectId = $('#english_subject_id').val();
        if (!englishSubjectId) {
            $('#english_subject_id').addClass('is-invalid');
            $('#english_subject_id').after('<div class="invalid-feedback">Please select an English subject.</div>');
            errorMessage = 'Please select an English subject.';
            e.preventDefault();
            return false;
        }
        selectedSubjects.push(englishSubjectId);
        
        // Check other subjects
        const otherSubjects = [];
        $('.subject-select').each(function() {
            const value = $(this).val();
            if (value) {
                if (selectedSubjects.includes(value)) {
                    hasDuplicates = true;
                    $(this).addClass('is-invalid');
                    $(this).after('<div class="invalid-feedback">This subject has already been selected.</div>');
                } else {
                    selectedSubjects.push(value);
                    otherSubjects.push(value);
                }
            }
        });
        
        // Ensure exactly 3 other subjects are selected
        if (otherSubjects.length < 3) {
            errorMessage = 'Please select exactly 3 subjects for the other subjects section.';
            $('.subject-select').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    $(this).after('<div class="invalid-feedback">Please select a subject.</div>');
                }
            });
        }
        
        if (hasDuplicates) {
            errorMessage = 'Please select different subjects for each subject slot.';
        }
        
        if (errorMessage) {
            e.preventDefault();
            alert(errorMessage);
            return false;
        }
        
        // Check total questions = 180 for JAMB exams
        const examType = $('input[name="type"]').val();
        if (examType === 'full_jamb') {
            const totalQuestions = parseInt($('#totalQuestionsCount').text());
            if (totalQuestions !== 180) {
                e.preventDefault();
                alert('JAMB exams must have exactly 180 questions total. Current total: ' + totalQuestions);
                return false;
            }
        }
        
        return true;
    });
});
</script>
<style>
.required::after {
    content: " *";
    color: #dc3545;
}

.subject-row {
    background-color: #f8f9fa;
    transition: border-color 0.2s;
}

.subject-row:hover {
    border-color: var(--primary-color);
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.form-switch .form-check-input {
    width: 2.5em;
    height: 1.25em;
}

.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000;
}
</style>
@endpush