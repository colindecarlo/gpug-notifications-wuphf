<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Demos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <ul class="">
                <li class="text-slate-700 text-2xl font-medium py-6">
                    <a href="{{route('polling.index') }}" class="hover:text-slate-900">
                        {{ __('Polling') }}
                    </a>
                </li>
                <li class="text-slate-700 text-2xl font-medium py-6">
                    <a href="{{route('long-polling.index') }}" class="hover:text-slate-900">
                        {{ __('Loooong Polling') }}
                    </a>
                </li>
                <li class="text-slate-700 text-2xl font-medium py-6">
                    <a href="{{route('sse.upload') }}" class="hover:text-slate-900">
                        {{ __('Server Sent Events - No Package') }}
                    </a>
                </li>
                <li class="text-slate-700 text-2xl font-medium py-6">
                    <a href="{{route('ws.ratchet.index') }}" class="hover:text-slate-900">
                        {{ __('Web Sockets - No Package') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</x-app-layout>
