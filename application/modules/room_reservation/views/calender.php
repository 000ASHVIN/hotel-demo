<link rel="stylesheet" href="<?php echo MOD_URL.$module;?>/assets/css/daypilot-all.min.css">
<style>
.scheduler_default_corner div {
    display: none !important;
}
.div1{
    width: 30px;
    height: 30px;
    text-align: center !important;
    border: 1px solid white;
    background-color: #EE4B2B;
}
.div2{
    width: 30px;
    height: 30px;
    border: 1px solid white;
    background-color: #3CB043;
}
</style>
<div class="d-grid gap-2 d-md-flex justify-content-md-end">
    <div style="display: flex; margin: 5px;"><div class="div1"></div><div style="margin-top: 5px;">Booked</div></div>
    <div style="display: flex; margin: 5px;"><div class="div2"></div><div style="margin-top: 5px;">Available</div></div>
</div>
<div class="d-grid gap-2 d-md-block">

</div>
<?php
    $currentMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
    $currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

    $previousMonth = date('m', strtotime("-1 month", strtotime($currentMonth.'/01/'.$currentYear)));
    $previousYear = date('Y', strtotime("-1 month", strtotime($currentMonth.'/01/'.$currentYear)));

    $nextMonth = date('m', strtotime("+1 month", strtotime($currentMonth.'/01/'.$currentYear)));
    $nextYear = date('Y', strtotime("+1 month", strtotime($currentMonth.'/01/'.$currentYear)));

    $nextMonthURL = "?month=" . $nextMonth . "&year=" . $nextYear;
?>
<!-- <a href="?month=<?php echo $previousMonth; ?>&year=<?php echo $previousYear; ?>">Previous Month</a>
<a href="<?php echo $nextMonthURL; ?>">Next Month</a> -->

<a href="?month=<?php echo date('m', strtotime("-1 month", strtotime($currentMonth.'/01/'.$currentYear))); ?>&year=<?php echo date('Y', strtotime("-1 month", strtotime($currentMonth.'/01/'.$currentYear))); ?>">Previous Month</a>
<a href="?month=<?php echo date('m', strtotime("+1 month", strtotime($currentMonth.'/01/'.$currentYear))); ?>&year=<?php echo date('Y', strtotime("+1 month", strtotime($currentMonth.'/01/'.$currentYear))); ?>">Next Month</a>

<div id="scheduler"></div>


<script src="<?php echo MOD_URL.$module;?>/assets/js/daypilot-all.min.js"></script>
<script>
   var roomdata = <?php print_r(json_encode($roomlist)); ?>;
   var bookings = <?php print_r(json_encode($bookings)); ?>;
   var scheduler;
   var bookedRoomId = null;

function initializeScheduler() {
  scheduler = new DayPilot.Scheduler("scheduler", {
    startDate: new DayPilot.Date().firstDayOfMonth(),
    cellWidthSpec: 'Auto',
    cellWidthMin: 20,
    days: 31,
    timeHeaders: [
      { groupBy: "Month", format: "MMMM yyyy",}, 
      { groupBy: "Day", format: "d" }
    ],
    resources: roomdata.map(function(room) {
      return { name: "Room No. " + room.roomno, id: room.id };
    }),
    viewType: "Month",
    scale: "Day",
    onTimeRangeSelected: function(args) {
      var selectedDate = args.start;
      openPopup(selectedDate);
    },
    onBeforeCellRender: function(args) {
      var date = args.cell.start;
      var label = '';

      for (let i = 0; i < roomdata.length; i++) {
        var room = roomdata[i];
        var booking = bookings[i];
        var bookingStatus = booking && booking.bookingstatus;
        var checkindate = booking && booking.checkindate;
        var checkoutdate = booking && booking.checkoutdate;
    
          // if (room.status == 2 && bookingStatus == 4 && checkindate < date && checkoutdate > date && booking.roomid == room.roomid) {
          //   label = "";
          //   break;
          // } else {
          //   label = "";
          // }
          if (room.status == 2 && bookingStatus == 4 && checkindate < date && checkoutdate > date && booking.roomid == room.roomid) {
            if (typeof room.roomid !== 'undefined') {
              if (bookedRoomId == null || bookedRoomId == room.roomid) {
                  label = "";
                  bookedRoomId = room.roomid;
              } else {
                  label = "";
              }
              break;
            }
          } else {
              label = "";
          }
        }

      args.cell.backColor = (room.status == 2) ? "#EE4B2B" : "#3CB043";
      args.cell.html = "<div>" + label + "</div>";
    }
  });
  scheduler.init();
}
initializeScheduler();
    function openPopup(date) {
        var selectedDate = date.toString("yyyy-MM-dd");
        var resources = scheduler.resources;

        var popup = document.getElementById("popup");
        if (popup) {
            popup.parentNode.removeChild(popup);
        }

        var popupDiv = document.createElement("div");
        popupDiv.id = "popup";
        popupDiv.style.position = "absolute";
        popupDiv.style.left = "50%";
        popupDiv.style.top = "300px";
        popupDiv.style.width = "130px";
        popupDiv.style.background = "white";
        popupDiv.style.border = "1px solid #ccc";

        var linksDiv = document.createElement("div");
        // linksDiv.style.padding = "10px";
        linksDiv.style.paddingLeft = "10px !important";

            var link = document.createElement("a");

            var url = baseurl + "room_reservation/booking-list#";
            link.href = url;

            link.style.textAlign = "center";
            link.textContent = "New Reservation";
            link.style.display = "block";
            link.style.marginBottom = "10px";
            linksDiv.appendChild(link);

        popupDiv.appendChild(linksDiv);
        document.body.appendChild(popupDiv);
        }
</script>
