<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            レシピ記事
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('articles.index') }}" class="mb-6 flex gap-2">
                <select name="category" class="rounded-md border-gray-300">
                    <option value="">すべて</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="keyword" value="{{ request('keyword') }}" class="rounded-md border-gray-300">
                <button type="submit" class="rounded-md bg-orange-500 px-4 text-white hover:bg-orange-600">検索</button>
            </form>
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
                    <p class="text-gray-500">該当する記事がありません。</p>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $articles->links() }}
            </div>
        </div>
    </div>
</x-app-layout>