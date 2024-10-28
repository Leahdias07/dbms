<?php 
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

if(isset($_POST['get_bookings'])) {
    
    $query = "SELECT bo.*, bd.* FROM `booking_order` bo
        INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
        WHERE bo.booking_status='booked' AND bo.arrival=1
        ORDER BY bo.booking_id DESC";
        
    $res = mysqli_query($con, $query);
    
    $table_data = "";
    $i = 1;

    while($data = mysqli_fetch_assoc($res)) {
        $date = date("d-m-Y", strtotime($data['datentime']));
        $checkin = date("d-m-Y", strtotime($data['check_in']));
        $checkout = date("d-m-Y", strtotime($data['check_out']));

        $status_bg = ($data['booking_status'] == 'booked') ? 'bg-success' : 'bg-danger';

        $table_data .= "
            <tr>
                <td>$i</td>
                <td>
                    <span class='badge bg-primary'>Order ID: $data[order_id]</span><br>
                    <b>Name:</b> $data[user_name]<br>
                    <b>Phone No:</b> $data[phonenum]
                </td>
                <td>
                    <b>Room:</b> $data[room_name]<br>
                    <b>Room No:</b> $data[room_no]<br>
                    <b>Price:</b> ₹$data[price]
                </td>
                <td>
                    <b>Check-in:</b> $checkin<br>
                    <b>Check-out:</b> $checkout<br>
                    <b>Date:</b> $date
                </td>
                <td>
                    <span class='badge $status_bg'>$data[booking_status]</span>
                </td>
                <td>
                    <button type='button' onclick='download($data[booking_id])' class='btn btn-outline-success btn-sm fw-bold shadow-none'>
                        <i class='bi bi-file-earmark-arrow-down-fill'></i>
                    </button>
                </td>
            </tr>
        ";
        $i++;
    }

    echo json_encode(["table_data" => $table_data, "pagination" => ""]);
}