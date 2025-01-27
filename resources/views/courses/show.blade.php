@extends('layouts.app')

@section('content')
<div class="container w-4/5 mt-10 mx-auto flex flex-col justify-center">
    <h1 class='text-2xl font-bold'><span class="text-primary text-3xl">{{ $course->fullname }}</span> Course</h1>
    <hr class="w-full h-[2px] mt-2 mb-4 bg-black" />
    <div class='flex w-full justify-between'>
        <section class="w-1/5">
            <h2 class="text-xl text-primary font-bold border-b-2 border-primary pb-1 mb-2">Sections ({{ $sections->count() }})</h2>
            <ul class="flex flex-col pl-2 divide-y divide-black">
                @foreach($sections as $section)
                    <li class="hover:cursor-pointer hover:font-bold hover:text-primary py-1">
                        <a href="route('sections.show', $course, $section)">
                            <span class="inline-block w-3 text-center">{{ $loop->iteration }}</span>: $section->name
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class='flex w-full'>
                <a class="cursor-pointer" href="{{ route('sections.create', $course) }}">
                    <x-button full='true' class="mt-10">
                        Add Section
                    </x-button>
                </a>
            </div>
        </section>
        <section class="w-full">
        </section>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var infoPanelToggles = document.querySelectorAll('.info-panel-toggle');
    var infoPanelIcons = document.querySelectorAll('.info-panel-icon');
    var infoPanelContents = document.querySelectorAll('.info-panel-content');

    infoPanelToggles.forEach(function(toggle, index) {
        toggle.addEventListener('click', function() {
            infoPanelContents[index].classList.toggle('hidden');
            infoPanelIcons[index].classList.add('{{ $iconActiveClasses }}');
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    var addSectionBtn = document.getElementById('add-section-btn');
    var addSectionForm = document.getElementById('add-section-form');

    addSectionBtn.addEventListener('click', function () {
        addSectionBtn.style.display = 'none';
        addSectionForm.style.display = 'block';
    });

    var accordionButtons = document.querySelectorAll('.accordion-button');

    accordionButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var icon = this.querySelector('i');
            var target = this.getAttribute('data-target');
            var targetElement = document.querySelector(target);

            targetElement.addEventListener('shown.bs.collapse', function () {
                icon.classList.add('{{ $iconActiveClasses }}');
            });

            targetElement.addEventListener('hidden.bs.collapse', function () {
                icon.classList.remove('{{ $iconActiveClasses }}');
            });
        });
    });
});
</script>

<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

@endsection
