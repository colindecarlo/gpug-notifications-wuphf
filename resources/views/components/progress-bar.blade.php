@props(['route' => '/'])

<div class="w-full p-8 bg-slate-100">
    <div class="w-full h-8 border-2 border-slate-200 rounded-sm">
        <div class="amount-complete transition-all ease-in-out duration-300 bg-green-400 h-full w-0"></div>
    </div>
</div>


<button id="upload-button" class="block mx-auto text-lg font-medium text-slate-50 bg-green-700 px-4 py-2 rounded mt-4 hover:bg-green-600 hover:text-slate-100">
        {{ __('Upload') }}
</button>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const button = document.getElementById('upload-button');
        button.addEventListener('click', function () {
            fetch('{{ $route }}')
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                });
        });
    });
</script>

{{ $slot }}
