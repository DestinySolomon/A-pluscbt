@extends('layouts.admin')

@section('title', 'Edit Testimonial - A-plus CBT')
@section('page-title', 'Edit Testimonial')
@section('mobile-title', 'Edit: ' . Str::limit($testimonial->student_name, 15))

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.testimonials.index') }}">Testimonials</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.testimonials.show', $testimonial->id) }}">
            {{ Str::limit($testimonial->student_name, 15) }}
        </a>
    </li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-actions')
    <div class="d-flex gap-2">
        <button type="submit" form="testimonialForm" class="btn-admin btn-admin-primary">
            <i class="ri-save-line me-2"></i> Save Changes
        </button>
        <a href="{{ route('admin.testimonials.show', $testimonial->id) }}" class="btn-admin btn-admin-secondary">
            <i class="ri-arrow-left-line me-2"></i> Cancel
        </a>
    </div>
@endsection

@section('content')
    <div class="admin-card">
        <div class="card-body">
            <form action="{{ route('admin.testimonials.update', $testimonial->id) }}" method="POST" enctype="multipart/form-data" id="testimonialForm">
                @csrf
                @method('PUT')
                @include('admin.testimonials._form')
            </form>
        </div>
    </div>
@endsection