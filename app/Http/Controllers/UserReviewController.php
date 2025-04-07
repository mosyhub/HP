<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserReviewController extends Controller
{
    public function index(Product $product)
    {
        $reviews = $product->reviews()->paginate(10); // Use paginate() instead of get()

        return view('reviews.index', [
            'product' => $product,
            'reviews' => $reviews,
        ]);
    }
    
    public function create($product_id)
    {
        $product = Product::findOrFail($product_id);
        return view('reviews.create', compact('product'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
        ]);
        
        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        return redirect()->route('reviews.index', $request->product_id)
                         ->with('success', 'Review added successfully!');
    }
    
    public function edit(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only edit your own reviews!');
        }
        
        $product = Product::findOrFail($review->product_id);
        return view('reviews.edit', compact('review', 'product'));
    }
    
    public function update(Request $request, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only update your own reviews!');
        }
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
        ]);
        
        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        return redirect()->route('reviews.index', $review->product_id)
                         ->with('success', 'Review updated successfully!');
    }
    
    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id() && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', 'You can only delete your own reviews!');
        }
        
        $product_id = $review->product_id;
        $review->delete();
        
        return redirect()->route('reviews.index', $product_id)
                         ->with('success', 'Review deleted successfully!');
    }
}