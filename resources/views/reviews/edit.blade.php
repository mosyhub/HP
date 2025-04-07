@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Edit Your Review</h2>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                @if ($product->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                         class="img-thumbnail" style="max-width: 100px;" alt="{{ $product->name }}">
                                @else
                                    <img src="{{ asset('images/no-image.png') }}" class="img-thumbnail" 
                                         style="max-width: 100px;" alt="No Image Available">
                                @endif
                            </div>
                            <div>
                                <h4>{{ $product->name }}</h4>
                                <p class="text-muted mb-0">${{ number_format($product->price, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('reviews.update', $review->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="form-label">Your Rating</label>
                            <div class="rating-input">
                                <div class="btn-group" role="group">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <input type="radio" class="btn-check" name="rating" id="rating{{ $i }}" 
                                               value="{{ $i }}" {{ old('rating', $review->rating) == $i ? 'checked' : '' }}>
                                        <label class="btn btn-outline-warning" for="rating{{ $i }}">
                                            {{ $i }} <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                                @error('rating')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="comment" class="form-label">Your Review (Optional)</label>
                            <textarea name="comment" id="comment" rows="5" class="form-control @error('comment') is-invalid @enderror"
                                      placeholder="Share your experience with this product...">{{ old('comment', $review->comment) }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('reviews.index', $product->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Review
                                </button>
                                <button type="button" class="btn btn-outline-danger ms-2" 
                                        onclick="document.getElementById('delete-form').submit();">
                                    <i class="fas fa-trash"></i> Delete Review
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <form id="delete-form" method="POST" action="{{ route('reviews.destroy', $review->id) }}" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection