// js/clock.js
function updateClock() {
    var now = new Date();

    // Format the date: e.g., "Tuesday, August 1, 2023"
    var dateOptions = { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' };
    var dateString = now.toLocaleDateString('en-US', dateOptions);

    // Format time in 12-hour format with AM/PM.
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var seconds = now.getSeconds();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // Convert 0 to 12
    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;
    var timeString = hours + ":" + minutes + ":" + seconds + " " + ampm;

    // Combine date and time.
    var dateTimeString = dateString + " | " + timeString;

    // Update the clock element.
    var clockEl = document.getElementById("digitalClock");
    if (clockEl) {
        clockEl.innerHTML = dateTimeString;
    }
}

setInterval(updateClock, 1000);
updateClock();