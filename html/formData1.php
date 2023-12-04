<html>
<body>
	<?php
		include 'dbConnection.php';
		
		if(isset($_POST['submit'])){
			$fullName = mysqli_real_escape_string($conn, $_POST["fullname"]);
			$email = mysqli_real_escape_string($conn, $_POST["email"]);
			$room = mysqli_real_escape_string($conn, $_POST["room"]);
			$roomCount = mysqli_real_escape_string($conn, $_POST["roomCount"]);
			$check_inDate = mysqli_real_escape_string($conn, $_POST["inDate"]);
			$check_outDate = mysqli_real_escape_string($conn, $_POST["outDate"]);
			$noAdults = mysqli_real_escape_string($conn, $_POST["adult"]);
			$noChild = mysqli_real_escape_string($conn, $_POST["child"]);
			
			//Submit form data to the database
			$sql = "INSERT INTO roomavailability (Check_In_Date, Check_Out_Date, Customer_Name, Customer_Email, Room_Type, Room_Count, No_of_Adults, No_of_Child) VALUES ('$check_inDate', '$check_outDate', '$fullName', '$email', '$room', '$roomCount', '$noAdults', '$noChild')";
				
			if(mysqli_query($conn, $sql)) 
			{
				echo "<script>alert('Please wait while we confirm your booking...')</script>";
			} 
			else{
				echo "Error inserting data: " . mysqli_error($conn);
			}
			
			//Check room availability

			// Query to retrieve the number of booked rooms for a specific room type and dates
			$sql = "SELECT SUM(Room_Count) AS total_booked_rooms 
					FROM roomavailability 
					WHERE Room_Type = '$room' 
					AND ((Check_In_Date >= '$check_inDate' AND Check_In_Date < '$check_outDate')
					OR (Check_Out_Date > '$check_inDate' AND Check_Out_Date <= '$check_outDate')
					OR (Check_In_Date <= '$check_inDate' AND Check_Out_Date >= '$check_outDate'))
					AND Room_ID NOT IN (SELECT MAX(Room_ID) FROM roomavailability)";

			$result = mysqli_query($conn, $sql);

			// If the query is successful, get the total number of booked rooms
			if ($result) 
			{
				$row = mysqli_fetch_assoc($result);
				$booked_rooms = $row['total_booked_rooms'];
			}
			else {
				echo "Error retrieving data: " . mysqli_error($conn);
			}
			
			//Retrieve total number of rooms of particular room type
			$sql = "SELECT Room_Count FROM roomdetails WHERE Room_Name = '" . $room . "'";

			$roomsResult = mysqli_query($conn, $sql);
			if ($roomsResult) 
			{
				$roomsRow = mysqli_fetch_assoc($roomsResult);
				$numRooms = $roomsRow["Room_Count"];

			}
			else{
				echo "Error retrieving data: " . mysqli_error($conn);
			}
			
			$available_rooms = $numRooms - $booked_rooms;
			
			//Give message to the user according to available rooms
			if($available_rooms < $roomCount)
			{
				//Gives an error if the room is not available in the given period of time
				$message = "Sorry, there are only $available_rooms $room rooms available from $check_inDate to $check_outDate. Please select a different date range or choose a different room type.";
				echo "<script>alert('$message')</script>";
				
				//Delete inserted entry if booking is not successfull
				$sql = "DELETE FROM roomavailability
						WHERE Room_ID = (SELECT MAX(Room_ID) FROM roomavailability limit1)";
				
				$result = mysqli_query($conn, $sql);
				if($result)
				{
					echo "Last added data row deleted successfully!";
				} 
				else {
					echo "Error deleting last added data row: " . mysqli_error($conn);
				}
			}
			else{
				echo "Your booking was successful!!! Thank You for connecting with us...";
			}
			
			mysqli_close($conn);
		}
		
		/* if(isset($_POST['submit'])){
			$fullName = $_POST["fullname"];
			$email = $_POST["email"];
			$room = $_POST["room"];
			$check_inDate = $_POST["inDate"];
			$check_outDate = $_POST["outDate"];
			$noAdults = $_POST["adult"];
			$noChild = $_POST["child"];
			
			$to = $email;
			$subject = "Booking Confirmation Details";
			$headers = "From: Araliya Resort";
			$email_body = "You booking details are as follows,\n Customer Name: " . $fullName . "\nRoom Type: " . $room . "\nCheck-in Date: " . $check_inDate . "\nCheck-out Date: " . $check_outDate . "\nNo of adults: " . $noAdults . "\nNo of Children: " . $noChild;
			$result = mail($to,$subject,$email_body,$headers);
			
			if (1 == 1) {
				echo '<script type="text/javascript">alert("Your Booking was Successful!");</script>';
				echo '<script type="text/javascript">window.location.href = window.location.href;</script>';
			}
			else{
				echo '<script type="text/javascript">alert("Sorry! Booking was unsccessful, Try Again Later.");</script>';
				echo '<script type="text/javascript">window.location.href = window.location.href;</script>';
			}
		} */
		
		
		$email_form = "amayaresortbooking@gmail.com";
		$email_subject = "New Booking";
		$email_body = "You booking details are as follows,\n Customer Name: $fullName\nRoom Type: $room\n
		Check-in Date: $check_inDate\nCheck-out Date: $check_outDate\nNo of adults: $noAdults\nNo of Children: $noChild";
		$to = $email;
		
		mail($to,$email_subject,$email_body);
		
	?>
</body>
</html>