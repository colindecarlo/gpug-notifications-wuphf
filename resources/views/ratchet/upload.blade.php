<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('WebSockets with Ratchet') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-progress-bar>

                <script>
                  document.addEventListener('DOMContentLoaded', function () {
                    const conn = new window.ab.Session('ws://localhost:8080',
                      function () {
                        conn.subscribe('progress-updates:user:{{ Auth::user()->id }}', function (topic, data) {
                          let completed = document.querySelector('.amount-complete');
                          completed.style.width = `${data.progress}%`;

                          console.log({
                            topic: topic,
                            data: data
                          });
                        });
                      },
                      function () {
                        console.warn('WebSocket connection closed');
                      },
                      {'skipSubprotocolCheck': true}
                    );
                  });
                </script>

            </x-progress-bar>
        </div>
    </div>
</x-app-layout>
