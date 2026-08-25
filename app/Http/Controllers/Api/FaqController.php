<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(): JsonResponse
    {
        $faqs = Faq::active()->with('category')->orderBy('ordre')->get();
        return response()->json(['success' => true, 'data' => $faqs]);
    }

    public function search(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', $request->query('search', '')));
        $faqs = Faq::active()->with('category')->when($term !== '', function ($query) use ($term) {
            $query->where(fn ($q) => $q->where('question', 'like', "%{$term}%")
                ->orWhere('reponse', 'like', "%{$term}%"));
        })->orderBy('ordre')->get();
        return response()->json(['success' => true, 'data' => $faqs]);
    }

    public function popular(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Faq::active()->with('category')->orderByDesc('vues')->orderBy('ordre')->limit(10)->get()]);
    }

    public function show(int $id): JsonResponse
    {
        $faq = Faq::active()->with('category')->findOrFail($id);
        $faq->incrementVues();
        return response()->json(['success' => true, 'data' => $faq->fresh('category')]);
    }
}