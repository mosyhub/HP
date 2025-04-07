@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Reviews for {{ $product->name }}</h2>
                <div>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Back to Products
                    </a>
                    
                    @php
                        $userReview = $reviews->where('user_id', Auth::id())->first();
                    @endphp
                    
                    @if($userReview)
                        <a href="{{ route('reviews.edit', $userReview->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Your Review
                        </a>
                    @else
                        <a href="{{ route('reviews.create', $product->id) }}" class="btn btn-success">
                            <i class="fas fa-star"></i> Write a Review
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4">
            @if ($product->images->isNotEmpty())
                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                     class="img-fluid rounded" alt="{{ $product->name }}">
            @else
                <img src="{{ asset('images/no-image.png') }}" class="img-fluid rounded" 
                     alt="No Image Available">
            @endif
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">{{ $product->name }}</h3>
                    <p class="card-text">{{ $product->description }}</p>
                    <h4 class="text-primary">${{ number_format($product->price, 2) }}</h4>
                    
                    @php
                        $avgRating = $reviews->avg('rating') ?? 0;
                        $reviewCount = $reviews->count();
                        
                        $fiveStars = $reviews->where('rating', 5)->count();
                        $fourStars = $reviews->where('rating', 4)->count();
                        $threeStars = $reviews->where('rating', 3)->count();
                        $twoStars = $reviews->where('rating', 2)->count();
                        $oneStar = $reviews->where('rating', 1)->count();
                        
                        $fiveStarPercentage = $reviewCount > 0 ? ($fiveStars / $reviewCount) * 100 : 0;
                        $fourStarPercentage = $reviewCount > 0 ? ($fourStars / $reviewCount) * 100 : 0;
                        $threeStarPercentage = $reviewCount > 0 ? ($threeStars / $reviewCount) * 100 : 0;
                        $twoStarPercentage = $reviewCount > 0 ? ($twoStars / $reviewCount) * 100 : 0;
                        $oneStarPercentage = $reviewCount > 0 ? ($oneStar / $reviewCount) * 100 : 0;
                    @endphp
                    
                    <div class="rating-overview mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="me-2 mb-0">{{ number_format($avgRating, 1) }}</h5>
                            <div>
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $avgRating)
                                        <i class="fas fa-star text-warning"></i>
                                    @elseif($i <= $avgRating + 0.5)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    @else
                                        <i class="far fa-star text-warning"></i>
                                    @endif
                                @endfor
                                <div class="text-muted small">{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</div>
                            </div>
                        </div>
                        
                        <div class="rating-bars">
                            <div class="d-flex align-items-center mb-1">
                                <div class="me-2" style="width: 60px;">5 stars</div>
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $fiveStarPercentage }}%;"></div>
                                </div>
                                <div class="ms-2 text-muted small" style="width: 40px;">{{ $fiveStars }}</div>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <div class="me-2" style="width: 60px;">4 stars</div>
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $fourStarPercentage }}%;"></div>
                                </div>
                                <div class="ms-2 text-muted small" style="width: 40px;">{{ $fourStars }}</div>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <div class="me-2" style="width: 60px;">3 stars</div>
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $threeStarPercentage }}%;"></div>
                                </div>
                                <div class="ms-2 text-muted small" style="width: 40px;">{{ $threeStars }}</div>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <div class="me-2" style="width: 60px;">2 stars</div>
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $twoStarPercentage }}%;"></div>
                                </div>
                                <div class="ms-2 text-muted small" style="width: 40px;">{{ $twoStars }}</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="me-2" style="width: 60px;">1 star</div>
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar bg-danger" style="width: {{ $oneStarPercentage }}%;"></div>
                                </div>
                                <div class="ms-2 text-muted small" style="width: 40px;">{{ $oneStar }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <h3>Customer Reviews ({{ $reviewCount }})</h3>
    
    @forelse($reviews as $review)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <i class="far fa-star text-warning"></i>
                                    @endif
                                @endfor
                            </div>
                            <strong>{{ $review->user->name }}</strong>
                            @if($review->user_id === Auth::id())
                                <span class="badge bg-info ms-2">Your Review</span>
                            @endif
                        </div>
                        <div class="text-muted small">
                            {{ $review->created_at->format('F j, Y') }}
                            @if($review->created_at != $review->updated_at)
                                <span>(Edited)</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($review->user_id === Auth::id())
                        <div>
                            <a href="{{ route('reviews.edit', $review->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('reviews.destroy', $review->id) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this review?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
                
                <p class="card-text">{{ $review->comment ?? 'No comment provided.' }}</p>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            No reviews yet. Be the first to review this product!
        </div>
    @endforelse
    
    <div class="d-flex justify-content-center mt-4">
        {{ $reviews->links() }}
    </div>
</div>
@endsection