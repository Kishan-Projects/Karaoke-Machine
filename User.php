<?php

// submission at: https://students.cs.niu.edu/~z2047813/


// Start webpage:
echo "<html><head><title>Karaoke Group Project</title></head><body>";
	echo "<h3>Karaoke User Interface</h3>";
	echo "<br/>";
	
	echo "<p><a href='Frontpage.php'>Home</a></p><br/>";
	
	// Connect to database:
	include("connect.php");
	// if successful, $pdo is a class object for connection
	
	// everything inside the form object now
	echo "<form action='' method='POST' >";
	
	
		// *** USER SELECTION ***
		//make query for all users
		$userquery = $pdo->query("SELECT Name, UserID FROM User;");
		if(!$userquery) {
				echo "Error in query!<br/>";
				die();
			}
		//has to be fetchall because its an array, with arrays inside that have 1 element...
		$query = $userquery->fetchAll(PDO::FETCH_ASSOC);
		
		
		//Radio buttons for user selection
		$item;
		$i;
		$n = "";
		foreach ($query as $row => $key) {
			
			$item = $query[$row];
			$n = ""; //empty string reset at start of loop
			
			//check if last selected value
			if ($_POST["userselect"] == $item['UserID']) {
				$n = "checked";
			}
			
			echo "<input type='radio' name='userselect' value=$item[UserID] required='required' $n /> $item[Name]";
			
			// newline after 6 users
			$i = ( ($row+1) % 6);
			if ( $i == 0 ) {
				echo "<br/>";
			}
		}
		echo "<br/><br/>";
		
		// *** PAY AMOUNT ***
		// okay this is easy for now, really it hooks into
		// the queue up functionality down below the search function
		echo "Pay to use the Premium Queue:<br/>";
		$p = "0.00";
		if ($_POST["payamount"] !== NULL) {
			$p = $_POST["payamount"];
		}
		echo "<input type='number' name='payamount' min='0.00' max='99.99' step= '1.00' value='".$p."'>";
		
		
		echo "<br/><br/>";
		// *** SEARCH FUNCTION ***
		
		// search by title
		echo "Search by Title:<br/>";
		echo "<input type='text' name='searchtitle' value='".$_POST["searchtitle"]."'>";
		echo "<br/>";
		// search by artist
		echo "Search by Artist:<br/>";
		echo "<input type='text' name='searchartist' value='".$_POST["searchartist"]."'>";
		echo "<br/>";
		// search by Contributor
		echo "Search by Contributor:<br/>";
		echo "<input type='text' name='searchcontributor' value='".$_POST["searchcontributor"]."'>";
		echo "<br/>";
		// each text box defaults back to their search input
		// actual search button
		echo "<input type='submit' name='submitsearch' value='Search' />";
		echo "<br/><br/>";
		
		// if search is initiated **OR** the order of the table is shifted: create table
		if ($_POST["submitsearch"] == "Search" || $_POST["sortby"] !== NULL) {
			
			$up = "\u{2191}";  //up arrow
			$down = "\u{2193}";  //down arrow
			
			$a = $_POST["searchartist"];
			$t = $_POST["searchtitle"];
			$c = $_POST["searchcontributor"];
			$g = $_POST["sortby"];
			
			// handle grouping statement
			if ($_POST["sortby"] == "Type" || $_POST["sortby"] == "Type ".$down."") {
				$g = "SongVersion.Type ASC";
			}
			else if ($_POST["sortby"] == "Type ".$up."") {
				$g = "SongVersion.Type DESC";
			}
			else if ($_POST["sortby"] == "Title" || $_POST["sortby"] == "Title ".$down."") {
				$g = "Song.Title ASC";
			}
			else if ($_POST["sortby"] == "Title ".$up."") {
				$g = "Song.Title DESC";
			}
			else if ($_POST["sortby"] == "Artist" || $_POST["sortby"] == "Artist ".$down."") {
				$g = "Song.Artist ASC";
			}
			else if ($_POST["sortby"] == "Artist ".$up."") {
				$g = "Song.Artist DESC";
			}
			else {
				//default sort
				$g = "Song.Title ASC";
				//might seem taboo to set a global var but I need to
				$_POST["sortby"] = "Title";
			}
			
			
			// Run Query
			// first, prepare a select statement
			$songquery = $pdo->prepare(
				"SELECT DISTINCT SongVersion.Type, Song.Title, Song.Artist, SongVersion.KaraokeID
				FROM Song, SongVersion, Contributor
				WHERE Song.SongID = SongVersion.SongID AND Song.SongID = Contributor.SongID AND SongVersion.SongID = Contributor.SongID 
				AND Song.Artist LIKE '%".$a."%' 
				AND Song.Title LIKE '%".$t."%' 
				AND Contributor.Name LIKE '%".$c."%' 
				ORDER BY ".$g.";"
			);
			/*
			//AND Song.Artist LIKE '%:artist%'
			//AND Song.Title LIKE '%:title%' 
			//AND Contributor.Name LIKE '%:contributor%'
			*/
			//$songquery->execute( array(":title" => $_POST["searchtitle"], ":artist" => $_POST["searchartist"], ":contributor" => $_POST["searchcontributor"]));
			// this wont work for some reason, maybe it doesn't like :variable between '%%'
			$songquery->execute();
			//error handler
			if(!$songquery) {
				echo "Error in query!";
				die();
			}
			//fetch to array
			$query = $songquery->fetchAll(PDO::FETCH_ASSOC);
			
			// search is done, time to generate the table:
			echo "<table border=1 cellspacing=1>";
	
				// declare header row
				echo "<tr>";
					// instead of loop, I want to declare these manually
					
					//Type
					if ($_POST["sortby"] == "Type" || $_POST["sortby"] == "Type ".$down."") {
						echo "<th><input type='submit' name='sortby' value='Type &#x2191' /></th>";
					}
					else if ($_POST["sortby"] == "Type ".$up."") {
						echo "<th><input type='submit' name='sortby' value='Type &#x2193' /></th>";
					}
					else {
						echo "<th><input type='submit' name='sortby' value='Type' /></th>";
					}
					
					//Title
					if ($_POST["sortby"] == "Title" || $_POST["sortby"] == "Title ".$down."") {
						echo "<th><input type='submit' name='sortby' value='Title &#x2191' /></th>";
					}
					else if ($_POST["sortby"] == "Title ".$up."") {
						echo "<th><input type='submit' name='sortby' value='Title &#x2193' /></th>";
					}
					else {
						echo "<th><input type='submit' name='sortby' value='Title' /></th>";
					}
					
					//Artist
					if ($_POST["sortby"] == "Artist" || $_POST["sortby"] == "Artist ".$down."") {
						echo "<th><input type='submit' name='sortby' value='Artist &#x2191' /></th>";
					}
					else if ($_POST["sortby"] == "Artist ".$up."") {
						echo "<th><input type='submit' name='sortby' value='Artist &#x2193' /></th>";
					}
					else {
						echo "<th><input type='submit' name='sortby' value='Artist' /></th>";
					}
					
					//Queue Up Button Column
					echo "<th>Queue Up?</th>";
					
				echo "</tr>";
				
				// defining variable before loop incase thats needed
				$item;
				
				// declare remaining rows
				foreach ($query as $row => $key) {
					echo "<tr>";
					
					$item = $query[$row];
					
					// Type, Title, Artist data
					echo "<td>$item[Type]</td>";
					echo "<td>$item[Title]</td>";
					echo "<td>$item[Artist]</td>";
					
					//Queue Up Button
					echo "<td><button type='submit' name='addsongqueue' value='$item[KaraokeID]'>Queue Up</button></td>";
					
					//get rid of this loop later to add queue up button
					/*
					foreach ($row as $key => $item) {
						echo "<td>$item</td>";
					}
					*/
					echo "</tr>";
				}
				
			echo "</table>";
			// done drawing table
			
		}
		
		
		
		echo "<br/><br/>";
		// *** QUEUE UP FUNCTION ***
		
		// get datetime - seems to be in GMT+0 I think?
		$timestamp = date("Y-m-d H:i:s");
		// get numberic timestamp in unix
		$numts = idate("U");
		if ($_POST["addsongqueue"] !== NULL) {
			//song has been queued up
			
			//first we must form the payment object
			/*which requires:
				- a PayID to be generated = numts
				- the UserID = _POST["userselect"]
				- the pay amount = _POST["payamount"]
				- the system time = timestamp
			*/
			
			//now we can create the payment object
			$paymentadd = $pdo->prepare(
				"INSERT INTO Payment 
					VALUES 
						('".$numts."','".$_POST["userselect"]."', ".$_POST["payamount"].", '".$timestamp."')
				;"
			);
			// look I'd want to use :variable for prepare statements properly if they worked
			$paymentadd->execute();
			//error handler
			if(!$paymentadd) {
				echo "Error in paymentadd query!";
				die();
			}
			
			
			//payment object done, now its time to add the song to the queue
			/* Things needed for QueueItem:
				QueueType = needs to be "Free" if _POST["payamount"] == 0.00, otherwise "Premium"
				KaraokeID = _POST["addsongqueue"];
				PayID = numts
				UserID = _POST["userselect"];
			*/
			
			//first, check if they paid at all
			$qt;
			if ($_POST["payamount"] == 0.00) {
				$qt = "Free";
			}
			else {
				$qt = "Premium";
			}
			
			//now we create a QueueItem object
			$queueadd = $pdo->prepare(
				"INSERT INTO QueueItem 
					VALUES 
						('".$qt."', '".$_POST["addsongqueue"]."', '".$numts."', '".$_POST["userselect"]."')
				;"
			);
			// look I'd want to use :variable for prepare statements properly if they worked
			$queueadd->execute();
			//error handler
			if(!$queueadd) {
				echo "Error in queueadd query!";
				die();
			}
			
			//and we're done, song is added to the queue
			//best to tell the user
			echo "Song added to queue!";
			
			//and we're all done yippie
			
		}
		
		//end of form
	echo "</form>";
	
	// echo "<br/>DONE\n"; // indicate end of code for end debugging
echo "</body></html>";
?>
