
$query = "SELECT bo.*, bd.* FROM `booking_order` bo
    INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
    WHERE bo.user_id = ? 
    ORDER BY bo.booking_id DESC";

$result = select($query, [$_SESSION['uId']], 'i');
