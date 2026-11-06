<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            お知らせ
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg divide-y">
                @forelse ($announcements as $announcement)
                    <a href="{{ route('announcements.show', $announcement) }}" class="block p-6 hover:bg-gray-50">
                        <p class="text-sm text-gray-500">{{ $announcement->published_at->format('Y年n月j日') }}</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $announcement->title }}</p>
                    </a>
                @empty
                    <p class="p-6 text-gray-500">お知らせはまだありません。</p>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $announcements->links() }}
            </div>
        </div>
    </div>
</x-app-layout>