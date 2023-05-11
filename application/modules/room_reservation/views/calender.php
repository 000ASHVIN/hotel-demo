<link rel="stylesheet" href="<?php echo MOD_URL.$module;?>/assets/css/daypilot-all.min.css">
<style>
.scheduler_default_corner div {
    display: none !important;
}
</style>
<div id="scheduler"></div>
<script src="<?php echo MOD_URL.$module;?>/assets/js/daypilot-all.min.js"></script>
<script>
    var scheduler = new DayPilot.Scheduler("scheduler", {
        startDate : new DayPilot.Date().firstDayOfMonth(),
        cellWidthSpec : 'Auto',
        cellWidthMin : 20,
        days: 31,
        timeHeaders: [
            { groupBy: "Month", format: "MMMM yyyy" },
            { groupBy: "Day", format: "d" }
        ],
        resources: [
            { name: "Room 1", id: "R1" },
            { name: "Room 2", id: "R2" },
            { name: "Room 3", id: "R3" },
            { name: "Room 4", id: "R4" },
            { name: "Room 5", id: "R5" },
            { name: "Room 6", id: "R6" },
            { name: "Room 7", id: "R7" },
            { name: "Room 8", id: "R8" },
            { name: "Room 9", id: "R9" },
            { name: "Room 10", id: "R10" },
            { name: "Room 11", id: "R11" },
            { name: "Room 12", id: "R12" },
        ],
        viewType: "Month",
        scale: "Day",
    });
    scheduler.init();

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
        }
    };
    xhr.open("GET", "room_reservation/room_cal", true);
    xhr.send();
</script>
