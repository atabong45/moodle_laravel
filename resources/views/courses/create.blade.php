@extends('layouts.app')

@section('content')
<div class="container w-4/5 mt-10 mx-auto flex flex-col justify-center">
    <h1 class='text-3xl font-bold'>
        <a href="javascript:window.history.back();">
            <x-button class="!py-1 !px-2">
                <i class="fa-regular fa-circle-left"></i>
            </x-button>
        </a>
        Create a course
    </h1>
    <hr class="w-full h-[2px] mt-2 mb-4 bg-black" />

    <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <fieldset class="flex flex-col gap-4">
            <!-- Image Field -->
            <div class="flex flex-col justify-center">
                <div id="imagePreview" class="w-36 mb-3 overflow-hidden" style="display: none;">
                    <img id="previewImg" src="" alt="Image Preview" class="rounded-md" style="max-width: 100%; height: auto;">
                </div>
                <div class="flex items-cexnter">
                    <label for="image" class="w-32">Image :</label>
                    <div class="relative">
                        <input type="file" name="image" class="absolute inset-0 opacity-0 cursor-pointer" id="image" required onchange="previewImage(event)">
                        <button class='border border-primary py-1 px-2 text-primary rounded-md'>
                            Choose an Image
                        </button>
                    </div>
                </div>
            </div>

            <!-- Fullname Field -->
            <div class="flex items-center">
                <label for="fullname" class="w-36">Fullname :</label>
                <input type="text" name="fullname" class="py-1 rounded-md w-full" id="fullname" required>
            </div>

            <!-- Shortname Field -->
            <div class="flex items-center">
                <label for="shortname" class="w-36">Shortname :</label>
                <input type="text" name="shortname" class="py-1 rounded-md w-full" id="shortname" required>
            </div>

            <!-- Category Field -->
            <div class="flex items-center">
                <label for="category" class="w-36">Category :</label>
                <select name="category" class="py-1 rounded-md w-full" id="category" required>
               @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
                </select>
            </div>

            <!-- Summary Field -->
            <div class="flex items-top">
                <label for="summary" class="w-36">Summary :</label>
                <textarea name="summary" class="py-1 rounded-md w-full" id="summary" placeholder="Optional summary..."></textarea>
            </div>

            <!-- Number of sections Field -->
            <div class="flex items-center">
                <label for="numsections" class="w-36">Number of sections :</label>
                <input type="number" name="numsections" class="py-1 rounded-md w-full" id="numsections" required>
            </div>

            <!-- Start Date Field -->
            <div class="flex items-center">
                <label for="startdate" class="w-36">Start Date :</label>
                <input type="date" name="startdate" class="py-1 rounded-md w-full" id="startdate" required>
            </div>

            <!-- End Date Field -->
            <div class="flex items-center">
                <label for="enddate" class="w-36">End Date :</label>
                <input type="date" name="enddate" class="py-1 rounded-md w-full" id="enddate">
            </div>
        </fieldset>

        <!-- Submit Button -->
        <x-button full='true' type="submit" class='mt-6'>
            Create
        </x-button>
    </form>
</div>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                const previewImg = document.getElementById('previewImg');
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
