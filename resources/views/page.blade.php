@extends('layouts.app')

@section('title', $page['title'] . ' | Raven Travel')

@section('body')
    @include('partials.navbar')

    <section class="page-header" style="padding-top: 120px;">
        <div class="section__container">
            <p class="section-subtitle">Raven Travel</p>
            <h1 class="section-title">{{ $page['title'] }}</h1>
        </div>
    </section>

    <section class="destinations-section" style="padding: 60px 0 80px;">
        <div class="section__container" style="max-width: 800px;">
            @if($slug === 'faq')
                <div class="faq-list">
                    @foreach($page['sections'] as $i => $faq)
                        <details class="faq-item" @if($i === 0) open @endif>
                            <summary class="faq-question">
                                <span>{{ $faq['q'] }}</span>
                                <i class="ri-arrow-down-s-line"></i>
                            </summary>
                            <div class="faq-answer">
                                <p>{{ $faq['a'] }}</p>
                            </div>
                        </details>
                    @endforeach
                </div>
            @else
                <div class="page-content">
                    {!! $page['content'] !!}
                </div>
            @endif
        </div>
    </section>

    @include('partials.footer')
@endsection
