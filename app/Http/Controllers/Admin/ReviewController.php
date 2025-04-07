<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index(Product $product)
    {
        $reviews = Review::with('user')
            ->where('product_id', $product->id)
            ->latest()
            ->paginate(10);
            
        return view('reviews.index', compact('reviews', 'product'));
    }
    
    public function create(Product $product)
    {
        // Check if user has already reviewed this product
        $existingReview = Review::where('user_id', Auth::id())
                                ->where('product_id', $product->id)
                                ->first();
                                
        if ($existingReview) {
            return redirect()->route('reviews.edit', $existingReview->id)
                            ->with('info', 'You already have a review for this product. You can edit it below.');
        }
        
        return view('reviews.create', compact('product'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);
        
        // Check if user has already reviewed this product
        $existingReview = Review::where('user_id', Auth::id())
                                ->where('product_id', $request->product_id)
                                ->first();
                                
        if ($existingReview) {
            return redirect()->route('reviews.edit', $existingReview->id)
                            ->with('info', 'You already have a review for this product. You can edit it below.');
        }
        
        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        return redirect()->route('reviews.index', $request->product_id)
                        ->with('success', 'Your review has been submitted!');
    }
    
    public function edit(Review $review)
    {
        $this->authorize('update', $review);
        
        $product = $review->product;
        return view('reviews.edit', compact('review', 'product'));
    }
    
    public function update(Request $request, Review $review)
    {
        $this->authorize('update', $review);
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);
        
        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        return redirect()->route('reviews.index', $review->product_id)
                        ->with('success', 'Your review has been updated!');
    }
    
    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        
        $productId = $review->product_id;
        $review->delete();
        
        return redirect()->route('reviews.index', $productId)
                        ->with('success', 'Your review has been deleted!');
    }
}