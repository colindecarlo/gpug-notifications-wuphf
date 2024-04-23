@props(['source' => ''])

<div class="w-full p-8 bg-slate-100">
    <div class="w-full h-8 border-2 border-slate-200 rounded-sm">
        <div class="amount-complete transition-all ease-in-out duration-300 bg-green-400 h-full w-0">

        </div>
    </div>
</div>

@if($source == 'ws-ratchet')
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
@elseif($source == 'polling')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let interval;
            interval = setInterval(() => {
                fetch('/polling/progress')
                    .then(response => response.json())
                    .then(data => {
                        let completed = document.querySelector('.amount-complete');
                        completed.style.width = `${data.progress}%`;

                        console.log(data);

                        if (data.progress == 100) {
                            clearInterval(interval);
                        }
                    });
            }, 200);
        });
    </script>
@elseif($source == 'long-polling')
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

@elseif($source == 'sse')
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
@endif
