<section class="py-5">
    <div class="container py-5">
        <div class="row g-4 text-center">
            @php
                $stats = [
                    ['icon' => 'ri-user-line', 'value' => '5,000+', 'label' => 'Active Students'],
                    ['icon' => 'ri-file-list-line', 'value' => '10,000+', 'label' => 'Practice Questions'],
                    ['icon' => 'ri-book-open-line', 'value' => '8+', 'label' => 'Subjects Available'],
                    ['icon' => 'ri-trophy-line', 'value' => '92%', 'label' => 'Success Rate'],
                ];
            @endphp
            
            @foreach($stats as $stat)
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <i class="{{ $stat['icon'] }} text-primary fs-1 mb-3"></i>
                    <h3 class="fw-bold mb-1">{{ $stat['value'] }}</h3>
                    <p class="text-muted mb-0">{{ $stat['label'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>