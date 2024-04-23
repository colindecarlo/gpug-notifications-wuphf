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
@endif
