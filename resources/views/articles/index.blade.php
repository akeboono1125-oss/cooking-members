<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            レシピ記事
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($articles as $article)
                    <a href="{{ route('articles.show', $article) }}" class="block bg-white shadow-sm sm:rounded-lg p-6 hover:shadow-md transition">
                        <span class="inline-block rounded-full bg-orange-100 px-3 py-1 text-xs text-orange-700">
                            {{ $article->category->name }}
                        </span>
                        <p class="mt-3 font-semibold text-gray-900">{{ $article->title }}</p>
                        <p class="mt-2 text-sm text-gray-500">{{ $article->published_at->format('Y年n月j日') }}</p>
                    </a>
                @empty
                    <p class="text-gray-500">記事はまだありません。</p>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $articles->links() }}
            </div>
        </div>
    </div>
</x-app-layout>