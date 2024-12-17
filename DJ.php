<?php
include 'connect.php';

error_reporting(E_ALL); ini_set('display_errors', 1);


$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'Payment.TimePaid';
$order_dir = isset($_GET['order_dir']) ? $_GET['order_dir'] : 'ASC';

$next_order_dir = $order_dir === 'ASC' ? 'DESC' : 'ASC';

$freeQueue = $pdo->query("
SELECT Song.Title, SongVersion.KaraokeID, Song.Artist, SongVersion.Type, Payment.TimePaid, Payment.Amount, User.Name
FROM Song, SongVersion, QueueItem, Payment, User 
WHERE QueueItem.QueueType = 'Free'
AND Song.SongID = SongVersion.SongID 
AND SongVersion.KaraokeID = QueueItem.KaraokeID 
AND Payment.PayID = QueueItem.PayID 
AND QueueItem.UserID = User.UserID
ORDER BY Payment.TimePaid ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Premium Queue
$premiumQueue = $pdo->query("
SELECT Song.Title, SongVersion.KaraokeID, Song.Artist, SongVersion.Type, Payment.TimePaid, Payment.Amount, User.Name
FROM Song, SongVersion, QueueItem, Payment, User
WHERE QueueItem.QueueType = 'Premium' 
AND Song.SongID = SongVersion.SongID 
AND SongVersion.KaraokeID = QueueItem.KaraokeID 
AND Payment.PayID = QueueItem.PayID 
AND QueueItem.UserID = User.UserID
ORDER BY $order_by $order_dir
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kishan Peddi - Z1966779</title>
</head>
<body>
    <h1>DJ Interface</h1>

    <p><a href="Frontpage.php">Home</a></p>


    <!-- Free Queue Table -->
    <h2>Free Queue</h2>
    <table border="1">
	<tr>
        <th>Title</th>
        <th>KaraokeID</th>
        <th>Artist</th>
        <th>Type</th>
        <th>Time Paid</th>
        <th>Name</th>
    </tr>
        <?php foreach ($freeQueue as $row): ?>
	    <tr>
           <td><?= htmlspecialchars($row['Title'] ?? 'N/A') ?></td>
	       <td><?= htmlspecialchars($row['KaraokeID'] ?? 'N/A') ?></td>
	       <td><?= htmlspecialchars($row['Artist'] ?? 'N/A') ?></td> 
	       <td><?= htmlspecialchars($row['Type'] ?? 'N/A') ?></td> 
	       <td><?= htmlspecialchars($row['TimePaid'] ?? 'N/A') ?></td> 
           <td><?= htmlspecialchars($row['Name'] ?? 'N/A') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>


    <!-- Priority Queue Table -->
    <h2>Premium Queue</h2>
    <form method="GET" action="">
    <input type="hidden" name="order_dir" value="<?= htmlspecialchars($next_order_dir) ?>">
    <button type="submit" name="order_by" value="Payment.TimePaid">Sort by Time Paid (<?= htmlspecialchars($order_dir) ?>)</button>
    <button type="submit" name="order_by" value="Payment.Amount">Sort by Payment (<?= htmlspecialchars($order_dir) ?>)</button>
</form>
    <table border="1">
	<tr>
        <th>Title</th>
        <th>KaraokeID</th>
        <th>Artist</th>
        <th>Type</th>
        <th>Time Paid</th>
        <th>Payment</th>
        <th>Name</th>
	</tr>
        <?php foreach ($premiumQueue as $row): ?> 
            <tr>
	       <td><?= htmlspecialchars($row['Title'] ?? 'N/A') ?></td> 
	       <td><?= htmlspecialchars($row['KaraokeID'] ?? 'N/A') ?></td> 
	       <td><?= htmlspecialchars($row['Artist'] ?? 'N/A') ?></td> 
	       <td><?= htmlspecialchars($row['Type'] ?? 'N/A') ?></td> 
	       <td><?= htmlspecialchars($row['TimePaid'] ?? 'N/A') ?></td> 
	       <td><?= htmlspecialchars($row['Amount'] ?? 'N/A') ?></td> 
	       <td><?= htmlspecialchars($row['Name'] ?? 'N/A') ?></td>            
           </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

