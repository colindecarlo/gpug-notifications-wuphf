<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Server Sent Events - Laravel Wave') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-progress-bar>

                <script>
                  document.addEventListener('DOMContentLoaded', function () {
                    window.Echo.private('sse-wave:progress-updates:user:{{ Auth::user()->id }}')
                        .listen('UploadProgressEvent', function (event) {
                          let completed = document.querySelector('.amount-complete');
                          completed.style.width = `${event.progress}%`;
                        })
                  });
                </script>

            </x-progress-bar>
        </div>
    </div>
</x-app-layout>
