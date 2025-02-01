@extends('layouts.app')

@section('content')
<div class="container w-4/5 mt-10 mx-auto flex flex-col justify-center gap-4 bg-white p-6 rounded-2xl">
    <h1 class='text-3xl font-bold'>Tableau de bord</h1>
    <hr class="w-full h-[2px] mt-2 mb-4 bg-black" />
    <div class="rounded-md border border-gray-300 p-4">
        <div>
            <h2 class="text-2xl font-bold mb-4">Chronologie</h2>
            <div class="flex gap-2">
                <select name="duedate" id="duedate" class="rounded-md text-gray-700">
                    <optgroup label="General">
                        <option value="all">Toutes</option>
                        <option value="overdue">En retard</option>
                    </optgroup>
                    <optgroup label="Due date">
                        <option value="7d">7 prochains jours</option>
                        <option value="30d">30 prochains jours</option>
                        <option value="3m">3 prochains mois</option>
                        <option value="6m">6 prochains mois</option>
                    </optgroup>
                </select>
                <select name="dates" id="dates" class="rounded-md text-gray-700">
                    <option value="dates">Trier par dates</option>
                    <option value="courses">Trier par cours</option>
                </select>
                <input type="text" placeholder="Rechercher par type d'activité ou par nom" class="rounded-md text-gray-700 px-2 py-1 w-96 ml-auto">
            </div>
        </div>
        <div class="flex flex-col gap-2 justify-center items-center border-t border-gray-400 mt-8 py-8 text-gray-400">
            <div>
                <i class="fa-regular fa-file-lines text-8xl"></i>
            </div>
            <span class="text-xl">Aucune activité active</span>
        </div>
    </div>
    <div class="rounded-md border border-gray-300 p-4">
        <h2 class="text-2xl font-bold mb-4">Calendrier</h2>
        <div class="flex flex-col gap-2">
            <div class="flex justify-between">
                <select name="course-category" id="course-category" class="rounded-md text-gray-700">
                    <option value="all">Tous les cours</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->fullname }}</option>
                    @endforeach
                </select>
                <x-button id="openModal" full='true'>
                    Nouvel évènement
                </x-button>
            </div>
            <div>
                <div class="bg-gray-100 flex items-center justify-center">
                    <div class="w-11/12 mx-auto p-4">
                        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                            <div class="flex items-center justify-between px-6 py-3 bg-primary font-bold">
                                <button id="prevMonth" class="text-white">Précédent</button>
                                <h2 id="currentMonth" class="text-white"></h2>
                                <button id="nextMonth" class="text-white">Suivant</button>
                            </div>
                            <div class="grid grid-cols-7 gap-2 p-4" id="calendar">
                                <!-- Calendar Days Go Here -->
                            </div>

                            <!-- Modal -->
                            <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                                <div class="bg-white p-6 rounded shadow-lg relative w-96">
                                    <button id="closeModal" class="absolute top-2 right-2 text-gray-600 hover:text-black text-2xl">
                                        &times;
                                    </button>
                                    <form action="/events" method="POST" class="space-y-4" id="eventForm">
                                        @csrf
                                        <h2 class="text-xl font-bold">Create a New Event</h2>

                                        <div>
                                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                            <input type="text" id="title" name="title" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>

                                        <div>
                                            <label for="date" class="block text-sm font-medium text-gray-700">Date & Time</label>
                                            <input type="datetime-local" id="date" name="date" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>

                                        <div>
                                            <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                                            <select id="type" name="type" onchange="toggleFields()"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="utilisateur" selected>Utilisateur</option>
                                                <option value="cours">Cours</option>
                                                <option value="categorie">Catégorie</option>
                                                <option value="site">Site</option>
                                            </select>
                                        </div>

                                        <div id="courseField" style="display: none;">
                                            <label for="course_id" class="block text-sm font-medium text-gray-700">Course</label>
                                            <select id="course_id" name="course_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">Select Course</option>
                                                @foreach ($courses as $course)
                                                    <option value="{{ $course->id }}">{{ $course->fullname }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div id="categoryField" style="display: none;">
                                            <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                                            <select id="category_id" name="category_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">Select Category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <button type="submit"
                                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                                            Save
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@if(session('success'))
    <script>
        alert('Vous avez créé un nouvel évènement.');
    </script>
@endif

@section('script')
<script>
    let eventsElements = [];

    async function fetchEvents() {
        const response = await fetch('/events');
        const events = await response.json();

        console.log(events);

        events.forEach(event => {
            const eventElement = document.createElement('div');
            eventElement.className = `event event-${event.id} text-sm`;
            eventElement.innerText = event.title ?? event.name;

            const li = document.createElement('li');
            li.className = 'flex gap-1 items-center w-fit before:content-["•"] text-nowrap';
            li.appendChild(eventElement);

            let date = new Date(event.date ?? (event.timestart * 1000));

            eventsElements.push({
                'event': event,
                'day': date.getDate(),
                'month': date.getMonth(),
                'year': date.getFullYear(),
                'element': li,
            });
        });
    }

    // Function to generate the calendar for a specific month and year
    function generateCalendar(year, month) {
        const calendarElement = document.getElementById('calendar');
        const currentMonthElement = document.getElementById('currentMonth');

        // Create a date object for the first day of the specified month
        const firstDayOfMonth = new Date(year, month, 1);
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        // Clear the calendar
        calendarElement.innerHTML = '';

        // Set the current month text
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        currentMonthElement.innerText = `${monthNames[month]} ${year}`;

        // Calculate the day of the week for the first day of the month (0 - Sunday, 1 - Monday, ..., 6 - Saturday)
        const firstDayOfWeek = firstDayOfMonth.getDay();

        // Create headers for the days of the week
        const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        daysOfWeek.forEach(day => {
            const dayElement = document.createElement('div');
            dayElement.className = 'text-center font-semibold';
            dayElement.innerText = day;
            calendarElement.appendChild(dayElement);
        });

        // Create empty boxes for days before the first day of the month
        for (let i = 0; i < firstDayOfWeek; i++) {
            const emptyDayElement = document.createElement('div');
            calendarElement.appendChild(emptyDayElement);
        }

        // Create boxes for each day of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'h-32 text-center py-2 border cursor-pointer hover:bg-primary rounded-md hover:text-white hover:font-bold';
            dayElement.innerText = day;

            const ul = document.createElement('ul');
            ul.className = 'pl-4 overflow-hidden';

            for (let i = 0; i < eventsElements.length; i++) {
                const evtDay = eventsElements[i].day;
                const evtMonth = eventsElements[i].month;
                const evtYear = eventsElements[i].year;

                if (evtDay === day && evtMonth === month && evtYear === year) {
                    ul.appendChild(eventsElements[i].element);
                }
            }

            dayElement.appendChild(ul);

            // Check if this date is the current date
            const currentDate = new Date();
            if (year === currentDate.getFullYear() && month === currentDate.getMonth() && day === currentDate.getDate()) {
                dayElement.classList.add('bg-primary', 'text-white', 'hover:border', 'hover:border-black');
            }

            calendarElement.appendChild(dayElement);
        }
    }

    // Initialize the calendar with the current month and year
    const currentDate = new Date();
    let currentYear = currentDate.getFullYear();
    let currentMonth = currentDate.getMonth();

    fetchEvents().then(() => {
        generateCalendar(currentYear, currentMonth);
    });

    // Event listeners for previous and next month buttons
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        generateCalendar(currentYear, currentMonth);
    });

    document.getElementById('nextMonth').addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        generateCalendar(currentYear, currentMonth);
    });

    // Function to show the modal with the selected date
    function showModal(selectedDate) {
        const modal = document.getElementById('myModal');
        const modalDateElement = document.getElementById('modalDate');
        modalDateElement.innerText = selectedDate;
        modal.classList.remove('hidden');
    }

    // Function to hide the modal
    function hideModal() {
        const modal = document.getElementById('myModal');
        modal.classList.add('hidden');
    }

    // Event listener for date click events
    const dayElements = document.querySelectorAll('.cursor-pointer');
    dayElements.forEach(dayElement => {
        dayElement.addEventListener('click', () => {
            const day = parseInt(dayElement.innerText);
            const selectedDate = new Date(currentYear, currentMonth, day);
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = selectedDate.toLocaleDateString(undefined, options);
            showModal(formattedDate);
        });
    });

    // Event listener for closing the modal
    document.getElementById('closeModal').addEventListener('click', () => {
        hideModal();
    });

    // Courses filter in calender
    const courseCategory = document.getElementById('course-category');

    courseCategory.addEventListener('change', function () {
        const selectedCourseId = this.value;
        const events = document.querySelectorAll(`.event-${selectedCourseId}`);

        if (isNaN(selectedCourseId)) {
            eventsElements.forEach(event => {
                event.element.style.display = 'flex';
            })
        } else {
            eventsElements.forEach(event => {
                if (event.event && event.event.course_id && event.event.course_id == selectedCourseId) {
                    event.element.style.display = 'flex';
                } else {
                    event.element.style.display = 'none';
                }
            })
        }
    });
</script>

<script>
    const openModal = document.getElementById('openModal');
    const closeModal = document.getElementById('closeModal');
    const modal = document.getElementById('modal');

    // Event listeners for opening and closing the modal
    openModal.addEventListener('click', () => modal.classList.remove('hidden'));
    closeModal.addEventListener('click', () => modal.classList.add('hidden'));

    // Function to toggle the display of course and category fields
    function toggleFields() {
        var type = document.getElementById("type").value;
        document.getElementById("courseField").style.display = (type === "cours") ? "block" : "none";
        document.getElementById("categoryField").style.display = (type === "categorie") ? "block" : "none";
    }

    // Event listener to toggle fields on page load
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modal');
        const closeModalButton = document.getElementById('closeModal');
        const form = document.getElementById('eventForm');

        function resetForm() {
            form.reset();
        }

        closeModalButton.addEventListener('click', function () {
            modal.classList.add('hidden');
            resetForm();
        });
    });
</script>
@endsection
