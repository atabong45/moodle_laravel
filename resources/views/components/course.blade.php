<div class="rounded-lg overflow-hidden border border-primary/20 shadow-md">
    <div class="h-24 overflow-hidden">
        <a href="">
            <img src="{{ $course->img ?? 'images/mathematics.jpeg' }}" alt="{{ $course->fullname ?? 'No name' }} image" />
        </a>
    </div>
    <div class="h-14 p-2 py-0">
        <div class="font-bold text-xl flex justify-between">
            <a href="{{ route('courses.show', $course) }}">
                <span>{{ $course->fullname ?? 'No name' }}</span>
            </a>
 
            <a href="{{ route('courses.edit', $course) }}" class="text-primary">
                <i class="fa-solid fa-pen"></i>
            </a>
        </div>
        <div class="italic text-base">{{ $course->category ?? 'No category' }}</div>
    </div>
</div>
