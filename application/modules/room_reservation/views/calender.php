<link rel="stylesheet" href="<?php echo MOD_URL.$module;?>/assets/css/daypilot-all.min.css">
<style>
.scheduler_default_corner div {
    display: none !important;
}
</style>

<div id="scheduler"></div>

<script src="<?php echo MOD_URL.$module;?>/assets/js/daypilot-all.min.js"></script>
<script>
    var roomdata = <?php print_r(json_encode($roomlist)); ?>;
    var scheduler = new DayPilot.Scheduler("scheduler", {
        startDate : new DayPilot.Date().firstDayOfMonth(),
        cellWidthSpec : 'Auto',
        cellWidthMin : 20,
        days: 31,
        timeHeaders: [
            { groupBy: "Month", format: "MMMM yyyy" },
            { groupBy: "Day", format: "d" }
        ],
        resources: roomdata.map(function(room) {
            return { name: "Room No. " + room.roomno, id: room.id };
        }),
        viewType: "Month",
        scale: "Day",
        onBeforeCellRender: function(args) {
            var date = args.cell.start;
            var label = '';
            for (let i = 0; i < roomdata.length; i++) {
            var room = roomdata[i];
            if (room.status == 2 && room.check_in_date === date.toString("yyyy-MM-dd")) {
                label = "C";
                break;
            } else {
                label = "";
            }
    }
    args.cell.backColor = (label == "C") ? "#FFDAB9" :  "#33cc33";
    args.cell.html = "<div>" + label + "</div>";
  }
    });
    scheduler.init();

</script>
