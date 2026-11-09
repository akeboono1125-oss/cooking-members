<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            記事の管理
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">タイトル</th>
                            <th class="px-4 py-3">カテゴリー</th>
                            <th class="px-4 py-3">状態</th>
                            <th class="px-4 py-3">公開日時</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($articles as $article)
                            <tr>
                                <td class="px-4 py-3">{{ $article->title }}</td>
                                <td class="px-4 py-3">{{ $article->category->name }}</td>
                                <td class="px-4 py-3">
                                    @if($article->status === 'draft')
                                    下書き
                                    @elseif($article->isPublished())
                                    公開中
                                    @else
                                    公開予約
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    {{ $article->published_at?->format('Y年n月j日 H:i') ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8">
                {{ $articles->links() }}
            </div>
        </div>
    </div>
</x-app-layout>