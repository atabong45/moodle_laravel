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
            <span class="text-xl">Aucun cours actif</span>
        </div>
    </div>
    <div class="rounded-md border border-gray-300 p-4">
        <h2 class="text-2xl font-bold mb-4">Calendrier</h2>
        <div class="flex flex-col gap-2">
            <div class="flex justify-between">
                <select name="course-category" id="course-category" class="rounded-md text-gray-700">
                    <option value="all">Tous les cours</option>
                    <option value="geography">Geographie</option>
                    <option value="mathematics">Mathematiques</option>
                </select>
                <x-button full='true' href="{{ route('courses.create') }}">
                    Nouvel évènement
                </x-button>
            </div>
            <div>
                <div class="bg-gray-100 flex items-center justify-center">
                    <div class="lg:w-7/12 md:w-9/12 sm:w-10/12 mx-auto p-4">
                        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                            <div class="flex items-center justify-between px-6 py-3 bg-primary font-bold">
                                <button id="prevMonth" class="text-white">Précédent</button>
                                <h2 id="currentMonth" class="text-white"></h2>
                                <button id="nextMonth" class="text-white">Suivant</button>
                            </div>
                            <div class="grid grid-cols-7 gap-2 p-4" id="calendar">
                                <!-- Calendar Days Go Here -->
                            </div>
                            <div id="myModal" class="modal hidden fixed inset-0 flex items-center justify-center z-50">
                            <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>

                            <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
                                <div class="modal-content py-4 text-left px-6">
                                <div class="flex justify-between items-center pb-3">
                                    <p class="text-2xl font-bold">Selected Date</p>
                                    <button id="closeModal" class="modal-close px-3 py-1 rounded-full bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring">✕</button>
                                </div>
                                <div id="modalDate" class="text-xl font-semibold"></div>
                                </div>
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

@section('script')
<script>
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
        dayElement.className = 'text-center py-2 border cursor-pointer hover:bg-primary/50 hover:rounded-md';
        dayElement.innerText = day;

        // Check if this date is the current date
        const currentDate = new Date();
        if (year === currentDate.getFullYear() && month === currentDate.getMonth() && day === currentDate.getDate()) {
            dayElement.classList.add('bg-blue-500', 'text-white'); // Add classes for the indicator
        }

        calendarElement.appendChild(dayElement);
    }
    }

    // Initialize the calendar with the current month and year
    const currentDate = new Date();
    let currentYear = currentDate.getFullYear();
    let currentMonth = currentDate.getMonth();
    generateCalendar(currentYear, currentMonth);

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
</script>
@endsection
