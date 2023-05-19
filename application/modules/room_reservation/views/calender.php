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
</style>
<div class="d-grid gap-2 d-md-flex justify-content-md-end">
  <div style="display: flex; margin: 5px;">
    <div class="div1"></div>
    <div style="margin-top: 5px;">Booked</div>
  </div>
  <div style="display: flex; margin: 5px;">
    <div class="div2"></div>
    <div style="margin-top: 5px;">Available</div>
  </div>
</div>
<div class="d-grid gap-2 d-md-block">

</div>
<?php
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
$currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

$previousMonth = date('m', strtotime("-1 month", strtotime($currentMonth . '/01/' . $currentYear)));
$previousYear = date('Y', strtotime("-1 month", strtotime($currentMonth . '/01/' . $currentYear)));

$nextMonth = date('m', strtotime("+1 month", strtotime($currentMonth . '/01/' . $currentYear)));
$nextYear = date('Y', strtotime("+1 month", strtotime($currentMonth . '/01/' . $currentYear)));

$nextMonthURL = "?month=" . $nextMonth . "&year=" . $nextYear;

$date = date('Y-m')."-01";
?>
<!-- <a href="?month=<?php echo $previousMonth; ?>&year=<?php echo $previousYear; ?>">Previous Month</a>
<a href="<?php echo $nextMonthURL; ?>">Next Month</a> -->

<a href="?month=<?php echo date('m', strtotime("-1 month", strtotime($currentMonth . '/01/' . $currentYear))); ?>&year=<?php echo date('Y', strtotime("-1 month", strtotime($currentMonth . '/01/' . $currentYear))); ?>">Previous Month</a>
<a href="?month=<?php echo date('m', strtotime("+1 month", strtotime($currentMonth . '/01/' . $currentYear))); ?>&year=<?php echo date('Y', strtotime("+1 month", strtotime($currentMonth . '/01/' . $currentYear))); ?>">Next Month</a>

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
    treeEnabled: true,
    treePreventParentUsage: true,
    cellWidthSpec: 'Auto',
    heightSpec: "Max",
    height: 500,
    eventMovingStartEndEnabled: true,
    eventResizingStartEndEnabled: true,
    timeRangeSelectingStartEndEnabled: true,
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
    onEventMoved: (args) => {
      dp.message("Moved: " + args.e.text());
    },
    onEventMoving: (args) => {
      if (args.e.resource() === "A" && args.resource === "B") { // don't allow moving from A to B
        args.left.enabled = false;
        args.right.html = "You can't move an event from Room 1 to Room 2";

        args.allowed = false;
      } else if (args.resource === "B") { // must start on a working day, maximum length one day
        while (args.start.getDayOfWeek() === 0 || args.start.getDayOfWeek() === 6) {
          args.start = args.start.addDays(1);
        }
        args.end = args.start.addDays(1); // fixed duration
        args.left.enabled = false;
        args.right.html = "Events in Room 2 must start on a workday and are limited to 1 day.";
      }

      if (args.resource === "C") {
        const except = args.e.data;
        const events = dp.rows.find(args.resource).events.all();

        let start = args.start;
        let end = args.end;
        let overlaps = events.some(item => item.data !== except && DayPilot.Util.overlaps(item.start(), item.end(), start, end));

        while (overlaps) {
          start = start.addDays(1);
          end = end.addDays(1);
          overlaps = events.some(item => item.data !== except && DayPilot.Util.overlaps(item.start(), item.end(), start, end));
        }

        if (args.start !== start) {
          args.start = start;
          args.end = end;

          args.left.enabled = false;
          args.right.html = "Start automatically moved to " + args.start.toString("d MMMM, yyyy");
        }

      }
    },
    onEventResized: (args) => {
      dp.message("Resized: " + args.e.text());
    },
    onTimeRangeSelected: async (args) => {

      var startTimestamp = args.start;
      var startTime = startTimestamp.value
      var startDate = startTime.split("T")[0];
      var modifiedStartDate = startDate.replace(/-/g, "/");

      var endTimestamp = args.end;
      var endTime = endTimestamp.value
      var endDate = endTime.split("T")[0];
      var modifiedEndDate = endDate.replace(/-/g, "/");

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
    onEventMove: (args) => {
      if (args.ctrl) {
        dp.events.add({
          start: args.newStart,
          end: args.newEnd,
          text: "Copy of " + args.e.text(),
          resource: args.newResource,
          id: DayPilot.guid() // generate random id
        });

        // notify the server about the action here
        args.preventDefault(); // prevent the default action - moving event to the new location
      }
    },
    onEventClick: (args) => {
      DayPilot.Modal.alert(args.e.data.text);
    }
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
      const resources = [{
          name: "Locations",
          id: "G1",
          expanded: true,
          children: rooms
        },
      ];

      const events = [];
      for (let i = 0; i < bookings.length; i++) {

        let roomId = 0;
        for (let j = 0; j < roomdata.length; j++) {
          if(bookings[i].room_no == roomdata[j].roomno) {
            roomId = String.fromCharCode(65 + j);
          }
        };

        if(roomId) {
          const e = {
            start: new DayPilot.Date(bookings[i].checkindate),
            end: new DayPilot.Date(bookings[i].checkoutdate),
            id: DayPilot.guid(),
            resource: roomId,
            text: "Booked",
            bubbleHtml: "Booked",
            barColor: app.barColor(i),
            barBackColor: app.barBackColor(i)
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
