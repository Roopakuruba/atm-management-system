<?php 
// Database connection
$con = mysqli_connect("localhost", "root", "", "atm") or die(mysqli_errno($con));
session_start();
$pin = $_SESSION['Pin'];

// Fetch account type and daily limit
$select_query = "SELECT account_type, daily_limit FROM account WHERE user_id = (SELECT user_id FROM card WHERE card_pin = $pin)";
$select_query_result = mysqli_query($con, $select_query) or die(mysqli_error($con));
$row = mysqli_fetch_array($select_query_result);
$account_type = "current";
$daily_limit = $row['daily_limit'];

// Fetch current balance
$select_balance_query = "SELECT balance FROM card WHERE card_pin = $pin";
$select_balance_result = mysqli_query($con, $select_balance_query) or die(mysqli_error($con));
$row_balance = mysqli_fetch_array($select_balance_result);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get withdrawal amount
    $withdrawal_amount = $_POST['cash1'];

    // Check if withdrawal amount exceeds the daily limit
    if ($withdrawal_amount <= $daily_limit) {
        // Check if withdrawal amount exceeds the current balance
        if ($withdrawal_amount <= $row_balance['balance']) {
            // Proceed with withdrawal
            // Deduct withdrawal amount from balance
            $new_balance = $row_balance['balance'] - $withdrawal_amount;
            // Update balance in the database
            $update_balance_query = "UPDATE card SET balance = $new_balance WHERE card_pin = $pin";
            mysqli_query($con, $update_balance_query) or die(mysqli_error($con));

            // Deduct withdrawal amount from daily limit
            $new_daily_limit = $daily_limit - $withdrawal_amount;
            // Update daily limit in the database
            $update_daily_limit_query = "UPDATE account SET daily_limit = $new_daily_limit WHERE user_id = (SELECT user_id FROM card WHERE card_pin = $pin)";
            mysqli_query($con, $update_daily_limit_query) or die(mysqli_error($con));

            // Display success message
            $message = "Transaction Successful. Please collect Your Money";
        } else {
            // Display insufficient balance message
            $message = "Insufficient Balance. Unable to complete the transaction.";
        }
    } else {
        // Display error message for exceeding daily limit
        $message = "Exceeded daily withdrawal limit. Unable to complete the transaction.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Account</title>
    <link  rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >
    <link href="style.css" rel="stylesheet" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" ></script>
</head>
<body style="background-image:url(img1/atm4.jpg)">
    <link href="style.css" rel="stylesheet" type="text/css"/>
    <div class="header">
        <div class="inner-header">
            <div class="logo">
                <p><center>
                    CoderBank ATM</center><br><br><br> <br><br><br>
                </p>
            </div>
        </div>
    </div>
    <div class="container">
            <div class="row">
                <br><Br><BR> <h7> <b><div class="col-xs-2">Remaining Balance: </div>
                        <div class="col-xs-10"><?php echo $row_balance['balance']; ?> </div><br><br></b></h7> &emsp;&emsp;</h7>
     <center>
        <div class="container">
            <div class="row">
                <div class="col-xs-10"><center>
                        <form method="post" action="current.php">
                            <br><br><br><input type="text" placeholder="Enter the amount to Withdraw"  class="form-control input-lg" name="cash1"><br><br>
                 
                    
                 <input type="submit" class="button" value="Submit">&emsp;
                        <input type="submit" class="button" formaction="pin.php" value="Clear">
                            </form>
                          
                    </center>
            </div>
                    
                </div>
    </center>
