@extends('layouts.admin')

@section('title', 'Add Testimonial - A-plus CBT')
@section('page-title', 'Add New Testimonial')
@section('mobile-title', 'Add Testimonial')

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.testimonials.index') }}">Testimonials</a>
    </li>
    <li class="breadcrumb-item active">Add New</li>
@endsection

@section('page-actions')
    <div class="d-flex gap-2">
        <button type="submit" form="testimonialForm" class="btn-admin btn-admin-primary">
            <i class="ri-save-line me-2"></i> Save Testimonial
        </button>
        <a href="{{ route('admin.testimonials.index') }}" class="btn-admin btn-admin-secondary">
            <i class="ri-arrow-left-line me-2"></i> Cancel
        </a>
    </div>
@endsection

@section('content')
    <div class="admin-card">
        <div class="card-body">
            <form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data" id="testimonialForm">
                @csrf
                @include('admin.testimonials._form')
            </form>
        </div>
    </div>
@endsection