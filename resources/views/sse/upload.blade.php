<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Server Sent Events') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-progress-bar>

                <script>
                  document.addEventListener('DOMContentLoaded', function () {
                    // const eventSource = new EventSource('/sse.php');

                    const eventSource = new EventSource('/server-sent-events/progress');
                    eventSource.onopen = function () {
                      console.log("Connection to server opened.")
                    }

                    eventSource.onerror = function() {
                      console.log("EventSource failed.");
                    };

                    eventSource.onmessage = function (event) {
                      console.log(`Message: ${event.data}`);

                      const data = JSON.parse(event.data);

                      let completed = document.querySelector('.amount-complete');
                      completed.style.width = `${data.progress}%`;
                    }
                  });
                </script>

            </x-progress-bar>
        </div>
    </div>
</x-app-layout>
