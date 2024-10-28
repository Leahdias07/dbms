<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - New Bookings</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">NEW BOOKINGS</h3>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">

            <div class="text-end mb-4">
              <input type="text" oninput="get_bookings(this.value)" class="form-control shadow-none w-25 ms-auto" placeholder="Type to search...">
            </div>

            <div class="table-responsive">
              <table class="table table-hover border">
                <thead>
                  <tr class="bg-dark text-light">
                    <th scope="col">#</th>
                    <th scope="col">User Details</th>
                    <th scope="col">Room Details</th>
                    <th scope="col">Booking Details</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody id="table-data">
                </tbody>
              </table>
            </div>

            <div id="pagination">
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Assign Room Modal -->
  <div class="modal fade" id="assign-room" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
      <form id="assign_room_form">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Assign Room</h5>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-bold">Room Number</label>
              <input type="text" name="room_no" class="form-control shadow-none" required>
            </div>
            <span class="badge rounded-pill bg-light text-dark mb-3 text-wrap lh-base">
              Note: Assign room number only when user has arrived!
            </span>
            <input type="hidden" name="booking_id">
          </div>
          <div class="modal-footer">
            <button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">CANCEL</button>
            <button type="submit" class="btn custom-bg text-white shadow-none">ASSIGN</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>

  <script>
    function get_bookings(search=''){
      let xhr = new XMLHttpRequest();
      xhr.open("POST","ajax/new_bookings.php",true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function(){
        let data = JSON.parse(this.responseText);
        document.getElementById('table-data').innerHTML = data.table_data;
        document.getElementById('pagination').innerHTML = data.pagination;
      }

      xhr.send('get_bookings&search='+search);
    }

    function assign_room(booking_id){
      document.querySelector('input[name="booking_id"]').value = booking_id;
    }

    function cancel_booking(booking_id){
      if(confirm('Are you sure you want to cancel this booking?')){
        let xhr = new XMLHttpRequest();
        xhr.open("POST","ajax/new_bookings.php",true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function(){
          if(this.responseText==1){
            alert('success','Booking Cancelled!');
            get_bookings();
          }
          else{
            alert('error','Server Down!');
          }
        }

        xhr.send('cancel_booking&booking_id='+booking_id);
      }
    }

    let assign_room_form = document.getElementById('assign_room_form');

    assign_room_form.addEventListener('submit',function(e){
      e.preventDefault();

      let data = new FormData();
      data.append('room_no',assign_room_form.elements['room_no'].value);
      data.append('booking_id',assign_room_form.elements['booking_id'].value);
      data.append('assign_room','');

      let xhr = new XMLHttpRequest();
      xhr.open("POST","ajax/new_bookings.php",true);

      xhr.onload = function(){
        if(this.responseText == 1){
          alert('success','Room Number Alloted! Booking Finalized!');
          assign_room_form.reset();
          get_bookings();
        }
        else{
          alert('error','Server Down!');
        }
      }

      xhr.send(data);
    });

    window.onload = function(){
      get_bookings();
    }
  </script>

</body>
</html>