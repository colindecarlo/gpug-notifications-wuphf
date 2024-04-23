<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Long Polling with JS') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-progress-bar>

                <script>
                  document.addEventListener('DOMContentLoaded', function () {
                    const poll = () => {
                      fetch('/long-polling/progress')
                        .then(response => response.json())
                        .then(data => {
                          let completed = document.querySelector('.amount-complete');
                          completed.style.width = `${data.progress}%`;

                          console.log(data);

                          if (data.progress != 100) {
                            setTimeout(poll, 0);
                          }
                        });
                    }
                    setTimeout(poll, 0);
                  });
                </script>

            </x-progress-bar>
        </div>
    </div>
</x-app-layout>
