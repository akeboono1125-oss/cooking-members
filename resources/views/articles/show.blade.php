<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            レシピ記事
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <article class="bg-white shadow-sm sm:rounded-lg p-6">
                <span class="inline-block rounded-full bg-orange-100 px-3 py-1 text-xs text-orange-700">
                    {{ $article->category->name }}
                </span>
                <p class="text-sm text-gray-500">{{ $article->published_at->format('Y年n月j日') }}</p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ $article->title }}</h1>
                <div class="mt-6 text-gray-700 leading-relaxed whitespace-pre-line">{{ $article->body }}</div>
            </article>

            <a href="{{ route('articles.index') }}" class="inline-block mt-6 text-sm text-gray-600 underline hover:text-gray-900">
                記事一覧へ戻る
            </a>
        </div>
    </div>
</x-app-layout>