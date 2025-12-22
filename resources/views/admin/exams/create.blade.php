@extends('layouts.admin')

@section('title', 'Create New JAMB Exam - A-plus CBT')
@section('page-title', 'Create New Exam')
@section('mobile-title', 'New Exam')

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{ route('admin.exams.index') }}">Exams</a>
</li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2">
    <button type="submit" form="examForm" class="btn-admin btn-admin-primary">
        <i class="ri-save-line me-2"></i> Save Exam
    </button>
    <a href="{{ route('admin.exams.index') }}" class="btn-admin btn-admin-secondary">
        <i class="ri-arrow-left-line me-2"></i> Cancel
    </a>
</div>
@endsection

@section('content')
<div class="admin-card">
    <div class="card-body">
        <form action="{{ route('admin.exams.store') }}" method="POST" id="examForm">
            @csrf
            
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
                                               value="{{ old('name') }}" 
                                               required
                                               placeholder="e.g., JAMB UTME Mock Exam 2025">
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
                                               value="{{ old('code') }}" 
                                               required
                                               placeholder="e.g., JAMB-MOCK-2025-01">
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
                                                  rows="3"
                                                  placeholder="Enter exam description (optional)">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- JAMB Subject Configuration -->
                    <div class="admin-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">JAMB Subject Configuration</h5>
                            <p class="text-muted mb-0">Standard JAMB format: English (60 questions) + 3 other subjects (40 each) = 180 questions total</p>
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
                                                @foreach($subjects->filter(function($s) { return stripos($s->name, 'english') !== false; }) as $subject)
                                                    <option value="{{ $subject->id }}" 
                                                            {{ old('english_subject_id', $englishSubject->id ?? '') == $subject->id ? 'selected' : '' }}>
                                                        {{ $subject->name }} (Code: {{ $subject->code }})
                                                    </option>
                                                @endforeach
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
                                                   value="{{ old('english_question_count', 60) }}" 
                                                   min="1" 
                                                   max="100" 
                                                   required>
                                            <small class="text-muted">JAMB Standard: 60 questions</small>
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
                                    @for($i = 0; $i < 3; $i++)
                                    <div class="subject-row mb-3 p-3 border rounded">
                                        <div class="row g-3">
                                            <div class="col-md-7">
                                                <div class="form-group">
                                                    <label class="form-label required">Subject {{ $i + 1 }}</label>
                                                    <select name="other_subjects[{{ $i }}][subject_id]" 
                                                            class="form-select subject-select @error('other_subjects.'.$i.'.subject_id') is-invalid @enderror" 
                                                            required>
                                                        <option value="">-- Select Subject --</option>
                                                        @foreach($subjects->filter(function($s) { return stripos($s->name, 'english') === false; }) as $subject)
                                                            <option value="{{ $subject->id }}" 
                                                                    {{ old('other_subjects.'.$i.'.subject_id') == $subject->id ? 'selected' : '' }}>
                                                                {{ $subject->name }} (Code: {{ $subject->code }})
                                                            </option>
                                                        @endforeach
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
                                                           value="{{ old('other_subjects.'.$i.'.question_count', 40) }}" 
                                                           min="1" 
                                                           max="100" 
                                                           required>
                                                    <small class="text-muted">JAMB Standard: 40 questions</small>
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
                                       value="{{ old('duration_minutes', 120) }}" 
                                       min="1" 
                                       max="240" 
                                       required>
                                <small class="text-muted">Standard JAMB: 120 minutes (2 hours)</small>
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
                                       value="{{ old('passing_score', 50) }}" 
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
                                       value="{{ old('max_attempts', 0) }}" 
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
                                       {{ old('shuffle_questions', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="shuffle_questions">
                                    Shuffle Questions
                                </label>
                                <small class="text-muted d-block">Display questions in random order</small>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="shuffle_options" 
                                       name="shuffle_options" 
                                       value="1" 
                                       {{ old('shuffle_options', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="shuffle_options">
                                    Shuffle Options
                                </label>
                                <small class="text-muted d-block">Display answer options in random order</small>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="show_results_immediately" 
                                       name="show_results_immediately" 
                                       value="1" 
                                       {{ old('show_results_immediately', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_results_immediately">
                                    Show Results Immediately
                                </label>
                                <small class="text-muted d-block">Display results right after submission</small>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Status
                                </label>
                                <small class="text-muted d-block">Enable or disable this exam</small>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="admin-card">
                        <div class="card-header">
                            <h5 class="mb-0">Quick Stats</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Questions:</span>
                                <span class="fw-medium" id="totalQuestionsCount">180</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Duration:</span>
                                <span class="fw-medium" id="totalDuration">2 hours</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Passing Score:</span>
                                <span class="fw-medium" id="passingScoreDisplay">50%</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">JAMB Standard:</span>
                                <span class="badge bg-success">✓ Match</span>
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
        
        // Check if matches JAMB standard (180)
        const jambMatch = total === 180;
        $('#totalQuestionsCount').toggleClass('text-success', jambMatch);
        $('#totalQuestionsCount').toggleClass('text-danger', !jambMatch);
    }
    
    // Calculate total duration
    function calculateTotalDuration() {
        const minutes = parseInt($('#duration_minutes').val()) || 0;
        const hours = Math.floor(minutes / 60);
        const remainingMinutes = minutes % 60;
        
        let durationText = '';
        if (hours > 0) {
            durationText += hours + ' hour' + (hours > 1 ? 's' : '');
        }
        if (remainingMinutes > 0) {
            if (hours > 0) durationText += ' ';
            durationText += remainingMinutes + ' minute' + (remainingMinutes > 1 ? 's' : '');
        }
        
        $('#totalDuration').text(durationText || '0 minutes');
        
        // Check if matches JAMB standard (120 minutes)
        const jambMatch = minutes === 120;
        $('#totalDuration').toggleClass('text-success', jambMatch);
        $('#totalDuration').toggleClass('text-danger', !jambMatch);
    }
    
    // Update passing score display
    function updatePassingScore() {
        const score = parseInt($('#passing_score').val()) || 0;
        $('#passingScoreDisplay').text(score + '%');
    }
    
    // Update JAMB standard match
    function updateJambStandardMatch() {
        const totalQuestions = parseInt($('#totalQuestionsCount').text());
        const duration = parseInt($('#duration_minutes').val()) || 0;
        
        const jambMatch = totalQuestions === 180 && duration === 120;
        $('.badge.bg-success').toggle(jambMatch);
        $('.badge.bg-warning').toggle(!jambMatch);
    }
    
    // Initialize calculations
    calculateTotalQuestions();
    calculateTotalDuration();
    updatePassingScore();
    updateJambStandardMatch();
    
    // Add event listeners
    $('#english_question_count, #duration_minutes, #passing_score').on('input', function() {
        calculateTotalQuestions();
        calculateTotalDuration();
        updatePassingScore();
        updateJambStandardMatch();
    });
    
    $('input[name^="other_subjects"]').on('input', function() {
        calculateTotalQuestions();
        updateJambStandardMatch();
    });
    
    // Prevent duplicate subject selection
    $('.subject-select').change(function() {
        const selectedSubjects = [];
        $('.subject-select').each(function() {
            const value = $(this).val();
            if (value && selectedSubjects.includes(value)) {
                alert('This subject has already been selected. Please choose a different subject.');
                $(this).val('');
                return false;
            }
            if (value) {
                selectedSubjects.push(value);
            }
        });
    });
    
    // Form validation
    $('#examForm').submit(function(e) {
        // Check for duplicate subjects
        const selectedSubjects = [];
        let hasDuplicates = false;
        
        $('.subject-select').each(function() {
            const value = $(this).val();
            if (value) {
                if (selectedSubjects.includes(value)) {
                    hasDuplicates = true;
                    $(this).addClass('is-invalid');
                } else {
                    selectedSubjects.push(value);
                    $(this).removeClass('is-invalid');
                }
            }
        });
        
        if (hasDuplicates) {
            e.preventDefault();
            alert('Please select different subjects for each subject slot.');
            return false;
        }
        
        // Ensure exactly 3 other subjects are selected
        const selectedCount = selectedSubjects.length;
        if (selectedCount < 3) {
            e.preventDefault();
            alert('Please select exactly 3 subjects for the other subjects section.');
            return false;
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

.badge.bg-success, .badge.bg-warning {
    display: none;
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