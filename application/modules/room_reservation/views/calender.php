<link rel="stylesheet" href="<?php echo MOD_URL . $module; ?>/assets/css/daypilot-all.min.css">
<style>
  .scheduler_default_corner div {
    display: none !important;
  }

  .div1 {
    width: 30px;
    height: 30px;
    text-align: center !important;
    border: 1px solid white;
    background-color: #EE4B2B;
  }

  .div2 {
    width: 30px;
    height: 30px;
    border: 1px solid white;
    background-color: #3CB043;
  }
  body{
    cursor: auto !important;
  }


</style>
<?php
$currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$currentYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

if($currentMonth < 12) {
  $nextMonthURL = "?month=" . ($currentMonth + 1) . "&year=" . $currentYear;
} else {
  $nextMonthURL = "?month=1&year=" . $currentYear + 1;
}


if($currentMonth > 1) {
  $prevMonthURL = "?month=" . ($currentMonth - 1) . "&year=" . $currentYear;
} else {
  $prevMonthURL = "?month=12&year=" . $currentYear - 1;
}

if(isset($_GET['month']) && isset($_GET['year'])) {
  if($_GET['month'] < 10) {
    $month = "0" . (int)$_GET['month'];
  } else {
    $month = (int)$_GET['month'];
  }

  $date = $_GET['year'] . "-" . $month . "-01";
} else {
  $date = date('Y-m')."-01";
}

?>
<div class="d-grid gap-2 d-md-flex justify-content-md-end">
<button class="btn btn-light"><a href="<?php echo base_url('room_reservation/room-calender') . $prevMonthURL; ?>">Previous Month</a></button>
<button class="btn btn-light"><a href="<?php echo base_url('room_reservation/room-calender') . $nextMonthURL; ?>">Next Month</a></button>
</div>
<div id="dp"></div>


<script src="<?php echo MOD_URL . $module; ?>/assets/js/daypilot-all.min.js"></script>
<script>
  var roomdata = <?php print_r(json_encode($roomlist)); ?>;
  var bookings = <?php print_r(json_encode($bookings)); ?>;
  
  var scheduler;
  var bookedRoomId = null;
  var date = '<?php echo $date; ?>';
  
  const rooms = roomdata.map(function(room, index) {
    return { name: "Room No. " + room.roomno, id: String.fromCharCode(65 + index) };
  });

  const dp = new DayPilot.Scheduler("dp", {
    startDate: date,
    days: 31,
    scale: "Day",
    timeHeaders: [{
        groupBy: "Month",
        format: "MMMM yyyy"
      },
      {
        groupBy: "Day",
        format: "d"
      }
    ],
    navigator: {
      selectMode: "month" 
    },
    treeEnabled: true,
    treePreventParentUsage: true,
    cellWidthSpec: 'Auto',
    heightSpec: "Max",
    height: 500,
    // eventMovingStartEndEnabled: true,
    // eventResizingStartEndEnabled: true,
    timeRangeSelectingStartEndEnabled: false,
    contextMenu: new DayPilot.Menu({
      items: [{
          text: "Edit",
          onClick: (args) => {
            dp.events.edit(args.source);
          }
        },
        {
          text: "Delete",
          onClick: (args) => {
            dp.events.remove(args.source);
          }
        },
        {
          text: "-"
        },
        {
          text: "Select",
          onClick: (args) => {
            dp.multiselect.add(args.source);
          }
        }
      ]
    }),
    eventMoveHandling: "Disabled",
    onTimeRangeSelected: async (args) => {

      var startTimestamp = args.start;
      var startTime = startTimestamp.value
      var startDate = startTime.split("T")[0];
      var modifiedStartDate = startDate.replace(/-/g, "/");

      var endTimestamp = args.end;
      var endTime = endTimestamp.value
      var endDate = endTime.split("T")[0];
      var currentDate = new Date(endDate); 
      currentDate.setDate(currentDate.getDate() - 1); 
      var previousDate = currentDate.toISOString().split("T")[0]; 
      var modifiedEndDate = previousDate.replace(/-/g, "/");
      var eventDate = modifiedStartDate + " - " + modifiedEndDate;

      const modal = await DayPilot.Modal.prompt("New Reservation:", eventDate)
      dp.clearSelection();
      if (modal.canceled) {
        return;
      }
      if (!modal.canceled) {

        var myurl = baseurl + "room_reservation/booking-list/" + "?";

        if (startDate) {
          myurl += "startdate=" + startDate;
        }

        if (endDate) {
          if (startDate) {
            myurl += "&";
          }
          myurl += "enddate=" + endDate;
        }
        window.location.replace(myurl);
        return;
      }
    },
  });

  dp.init();
  dp.scrollTo(date);

  const app = {
    barColor(i) {
      const colors = ["#3c78d8", "#6aa84f", "#f1c232", "#cc0000"];
      return colors[i % 4];
    },
    barBackColor(i) {
      const colors = ["#a4c2f4", "#b6d7a8", "#ffe599", "#ea9999"];
      return colors[i % 4];
    },
    loadData() {
      // const resources = [{
      //     name: "Rooms",
      //     id: "G1",
      //     expanded: true,
      //     children: rooms
      //   },
      // ];
      const resources = rooms;
      // const nextButton = document.getElementById("nextButton");
      // const prevButton = document.getElementById("prevButton");

      // nextButton.addEventListener("click", () => {
      //   const currentStartDate = dp.startDate;
      //   const nextMonthStartDate = currentStartDate.addMonths(1);
      //   dp.startDate = nextMonthStartDate;
      //   dp.update();
      //  });

      // prevButton.addEventListener("click", () => {
      //   const currentStartDate = dp.startDate;
      //   const previousMonthStartDate = currentStartDate.addMonths(-1);
      //   dp.startDate = previousMonthStartDate;
      //   dp.update();
      // });
      const events = [];
      for (let i = 0; i < bookings.length; i++) {

        let roomId = 0;
        for (let j = 0; j < roomdata.length; j++) {
          if(bookings[i].room_no == roomdata[j].roomno) {
            roomId = String.fromCharCode(65 + j);
          }
        };

        if(roomId) {
        const today = new DayPilot.Date(); 

        const e = {
          start: new DayPilot.Date(bookings[i].checkindate),
          end: new DayPilot.Date(bookings[i].checkoutdate),
          id: DayPilot.guid(),
          resource: roomId,
          text: today > new DayPilot.Date(bookings[i].checkoutdate) ? "Finish" : "Booked", 
          bubbleHtml: today > new DayPilot.Date(bookings[i].checkoutdate) ? "Finish" : "Booked", 
          barColor: app.barColor(i),
          barBackColor: app.barBackColor(i),
        };

          events.push(e);
        }
        
      }
      dp.update({
        resources,
        events
      });
    },
  };

  app.loadData();
</script>
