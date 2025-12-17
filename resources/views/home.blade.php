@extends('layouts.app')

@section('title', 'A-plus CBT - JAMB-Style Computer-Based Testing for Students')

@section('content')
    @include('components.hero')
    @include('components.features')
    @include('components.how-it-works')
    @include('components.subjects')
    @include('components.testimonials')
    @include('components.statistics')
    @include('components.faq')
    @include('components.contact')
    @include('components.cta')
@endsection