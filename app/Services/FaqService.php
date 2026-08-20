<?php

namespace App\Services;

use App\Models\Faq;
use App\Models\FaqCategory;

class FaqService
{
    /**
     * Récupérer toutes les FAQ par catégorie
     */
    public function getAllFaqs(): array
    {
        $categories = FaqCategory::active()
            ->with(['faqs' => function ($query) {
                $query->active()->orderBy('ordre');
            }])
            ->orderBy('ordre')
            ->get();

        return $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'nom' => $category->nom,
                'icone' => $category->icone,
                'faqs' => $category->faqs->map(function ($faq) {
                    return [
                        'id' => $faq->id,
                        'question' => $faq->question,
                        'reponse' => $faq->reponse,
                    ];
                }),
            ];
        })->toArray();
    }

    /**
     * Rechercher dans les FAQ
     */
    public function search(string $query): array
    {
        $faqs = Faq::active()
            ->where(function ($q) use ($query) {
                $q->where('question', 'LIKE', "%{$query}%")
                  ->orWhere('reponse', 'LIKE', "%{$query}%");
            })
            ->with('category')
            ->limit(10)
            ->get();

        return $faqs->map(function ($faq) {
            return [
                'id' => $faq->id,
                'question' => $faq->question,
                'reponse' => $faq->reponse,
                'category' => [
                    'id' => $faq->category->id,
                    'nom' => $faq->category->nom,
                ],
            ];
        })->toArray();
    }

    /**
     * Récupérer une FAQ et incrémenter les vues
     */
    public function getFaq(int $id): array
    {
        $faq = Faq::with('category')->findOrFail($id);
        $faq->incrementVues();

        return [
            'id' => $faq->id,
            'question' => $faq->question,
            'reponse' => $faq->reponse,
            'category' => [
                'id' => $faq->category->id,
                'nom' => $faq->category->nom,
            ],
        ];
    }

    /**
     * Récupérer les FAQ les plus consultées
     */
    public function getPopular(int $limit = 5): array
    {
        $faqs = Faq::active()
            ->with('category')
            ->orderByDesc('vues')
            ->limit($limit)
            ->get();

        return $faqs->map(function ($faq) {
            return [
                'id' => $faq->id,
                'question' => $faq->question,
                'reponse' => $faq->reponse,
                'category' => [
                    'id' => $faq->category->id,
                    'nom' => $faq->category->nom,
                ],
            ];
        })->toArray();
    }
}
