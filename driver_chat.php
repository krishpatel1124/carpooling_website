<?php
session_start();
include 'db.php';

if (!isset($_SESSION['UserID']) || $_SESSION['Role'] != 'Driver') {
    header("Location: login.php");
    exit();
}

$driver_id = $_SESSION['UserID'];

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'], $_POST['passenger_id'], $_POST['ride_id'])) {
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $ride_id = intval($_POST['ride_id']);
    $passenger_id = intval($_POST['passenger_id']);

    $insert_query = "INSERT INTO chats (RideID, SenderID, ReceiverID, Message) 
                     VALUES ($ride_id, $driver_id, $passenger_id, '$message')";
    mysqli_query($conn, $insert_query);
    exit();
}

// Fetch all ride-passenger pairs
$contacts_query = "SELECT DISTINCT c.RideID, u.UserID AS PassengerID, u.Name, u.ProfilePicture
                   FROM chats c
                   JOIN users u ON (u.UserID = c.SenderID OR u.UserID = c.ReceiverID)
                   WHERE (c.SenderID = $driver_id OR c.ReceiverID = $driver_id)
                   AND u.UserID != $driver_id";

$contacts_result = mysqli_query($conn, $contacts_query);

include 'driver_header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f7fa; }
        .chat-container {
            display: flex;
            height: 90vh;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background-color: white;
        }
        .chat-list {
            width: 30%;
            background-color: #2e3a59;
            color: white;
            overflow-y: auto;
        }
        .chat-list .contact {
            padding: 15px;
            border-bottom: 1px solid #3e4b6c;
            cursor: pointer;
        }
        .chat-list .contact:hover {
            background-color: #3e4b6c;
        }
        .chat-window {
            width: 70%;
            padding: 20px;
            display: flex;
            flex-direction: column;
            background-color: #eaf0f6;
        }
        .chat-box {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 15px;
            padding-right: 10px;
        }
        .message {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
            max-width: 70%;
        }
        .sent { align-self: flex-end; background-color: #cbe8f6; }
        .received { align-self: flex-start; background-color: #ffffff; }
    </style>
</head>
<body>

<div class="container-fluid mt-4">
    <div class="chat-container mx-auto">
        <!-- Contact List -->
        <div class="chat-list">
            <h5 class="text-center p-3 mb-0 bg-dark text-white">Chats</h5>
            <?php while ($row = mysqli_fetch_assoc($contacts_result)): ?>
                <?php
                $profile = !empty($row['ProfilePicture']) ? "images/" . $row['ProfilePicture'] : "images/defult image.png";
                ?>
                <div class="contact" onclick="loadChat(<?php echo $row['RideID']; ?>, <?php echo $row['PassengerID']; ?>, '<?php echo htmlspecialchars(addslashes($row['Name'])); ?>', '<?php echo $profile; ?>')">
                    <img src="<?php echo $profile; ?>" class="rounded-circle me-2" width="30" height="30">
                    <strong><?php echo htmlspecialchars($row['Name']); ?></strong><br>
                    <small>Ride ID: <?php echo $row['RideID']; ?></small>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Chat Window -->
        <div class="chat-window">
            <div id="chat-header" class="mb-3"></div>
            <div id="chat-box" class="chat-box border rounded p-3"></div>
            <form id="chat-form" class="input-group" onsubmit="sendMessage(event)">
                <input type="hidden" name="ride_id" id="ride_id">
                <input type="hidden" name="passenger_id" id="passenger_id">
                <input type="text" name="message" id="message" class="form-control" placeholder="Type a message..." required>
                <button type="submit" class="btn btn-primary">Send</button>
            </form>
        </div>
    </div>
</div>

<script>
    function loadChat(rideID, passengerID, name, profilePic) {
        document.getElementById('ride_id').value = rideID;
        document.getElementById('passenger_id').value = passengerID;
        document.getElementById('chat-header').innerHTML = `
            <h5>
                <img src="${profilePic}" class="rounded-circle me-2" width="35" height="35">
                ${name} <small class="text-muted">(Ride ID: ${rideID})</small>
            </h5>`;

        fetch(`fetch_chat.php?ride_id=${rideID}&passenger_id=${passengerID}`)
            .then(res => res.text())
            .then(data => {
                document.getElementById('chat-box').innerHTML = data;
                document.getElementById('chat-box').scrollTop = document.getElementById('chat-box').scrollHeight;
            });
    }

    function sendMessage(event) {
        event.preventDefault();
        const formData = new FormData(document.getElementById('chat-form'));
        fetch('driver_chat.php', {
            method: 'POST',
            body: formData
        }).then(() => {
            loadChat(formData.get('ride_id'), formData.get('passenger_id'), document.querySelector("#chat-header h5").innerText.split(' ')[0], '');
            document.getElementById('message').value = '';
        });
    }
</script>


</body>
</html>


<?php include 'driver_footer.php'; ?>


