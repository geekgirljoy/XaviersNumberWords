<!DOCTYPE html>
<html>
<head>
<title>Xaviers Number Words</title>

<style>

@font-face {
  font-family: roboto;
  src: url(Fonts/Roboto/Roboto-Regular.ttf);
}

body {
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
}

.topnav {
  overflow: hidden;
  background-color: #333;
}

.topnav p {
  float: left;
  color: #f2f2f2;
  text-align: center;
  padding: 14px 16px;
  margin: 0px;
  text-decoration: none;
  font-size: 17px;
}

.topnav p.label {
  background-color: #04AA6D;
  color: white;
}

.pullRight {
  float: right;
}

#Current{
    font-size:84px;
    font-family: roboto;
}


table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
th, td {
  padding: 5px;
  text-align: middle;    
}

/* The container */
.container {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default radio button */
.container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

/* Create a custom radio button */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
  border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.container:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.container input:checked ~ .checkmark {
  background-color: #2196F3;
}

.correct{
  background-color: #4CAF50 !important;
}

.incorrect{
  background-color: #f44336 !important;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.container input:checked ~ .checkmark:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.container .checkmark:after {
     top: 9px;
    left: 9px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: white;
}

button {
  color: white;
  background-color: #2196F3;
  text-align: center;
  padding: 12px 20px;
  border: none;
  margin: 0px;
  text-decoration: none;
  font-size: 17px;
  cursor: pointer;
}

button:disabled {
  background-color: #cccccc;
  cursor: default;
}

</style>



</head>
<body>


 <?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "XaviersNumberWords";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM users WHERE `user` = 'Xavier' LIMIT 1";
$result = $conn->query($sql);

$output ='';

        
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
      
        $min = 0;
        $max = 10;
        if($row["level"] == 2){
            $max = 99;
        }
        elseif($row["level"] == 3){
            $max = 999;
        }
        elseif($row["level"] == 4){
            $max = 9999;
        }
     
         // If SubmitAnswer    
        if(isset($_POST['SubmitAnswer'])){

            // Update Score
            if($_POST['SubmitAnswer'] == 'true'){
                $sql = "UPDATE `users` SET `correct` = `correct` + 1 WHERE `users`.`user` = 'Xavier';";
                $row["correct"]+=1;
            }
            else{
                $sql = "UPDATE `users` SET `incorrect` = `incorrect` + 1 WHERE `users`.`user` = 'Xavier';";
                $row["incorrect"]+=1;
            }
            $conn->query($sql);    
            
            // New current
            $new_current = random_int($min, $max);
            $row["current"] = $new_current;
            $sql = "UPDATE `users` SET `current` = '$new_current' WHERE `users`.`user` = 'Xavier'";
            $conn->query($sql);    
            
        }
        else{
        //$output .= '<p>SubmitAnswer: NO</p>';
        }
        
        // Top Bar
        $output = '<div class="topnav">';
        $output .= '<p class="label" href="#home">Player:</p>';
        $output .= '<p href="#">'.$row["user"].'</p>';
        $output .= '<div class="pullRight">';
        $output .= '<p class="label" href="#home">Score:</p>';
        $output .= '<p href="#">'. ( $row["correct"] - $row["incorrect"] ) .'</p>';
        $output .= '</div>';
        $output .= '</div><br>';

        
        // Form
        $output .= '<form id="NumberWords" method="post" action="' . $_SERVER['PHP_SELF'] . '">';
        $output .= '<center>';
        $output .= '<table style="width:60%">';
        $output .= '  <tr>';
        $output .= '    <th colspan="1"><h1 id="Current">'.$row["current"].'</h1></th>';
        $output .= '  </tr>';
        
        $correct_answer_position = random_int(1, ($row["level"]+1));
        
        $formatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        
        // Answers
        for($i = 1; $i <= ($row["level"]+1); $i++){
            
            $value = $row["current"];
            if($i != $correct_answer_position){
                while($value == $row["current"]){
                    $value = random_int($min, $max);
                }
            }
                        
            $output .= '  <tr>';
            $output .= '    <td>';
            $output .= '    <label class="container">' . ucwords($formatter->format($value));
            $output .= '      <input type="radio" name="radio" value="'.$value.'">';
            $output .= '      <span class="checkmark"></span>';
            $output .= '    </label>';
            $output .= '    </td>';
            $output .= '  </tr>';
        }
        
  }
} else {
  $output = 'No User named "Xavier"';
}
$conn->close();

echo $output;


?>

  <tr>
    <td>
        <button id="SubmitAnswer" name="SubmitAnswer" class="pullRight" type="submit" name="next" value="" disabled hidden>Next</button>
        <button id="CheckAnswer" type="button" class="pullRight" disabled>Check</button>
    </td>
  </tr>
</table>
</center>
</form>

<script>

var radio = document.forms["NumberWords"].elements["radio"];
var chkBtn = document.forms["NumberWords"].elements["button"];
for(var i = 0, max = radio.length; i < max; i++) {
    radio[i].onclick = function() {
        // Enable Check Button
        document.getElementById('CheckAnswer').disabled = false;
    }
}


document.getElementById("CheckAnswer").onclick = function () {
    
    // Disable and hide check button
    document.getElementById('CheckAnswer').disabled = true;
    document.getElementById('CheckAnswer').hidden = true;

    // get player answer and the actual answer
    var player_answer = document.querySelector('input[name="radio"]:checked').value;
    var answer = document.getElementById('Current').innerHTML;
    

    var element = document.querySelector('input[name="radio"]:checked');
    element = element.parentElement.querySelector('span.checkmark');
        
        
    //console.log();
    var evaluation = player_answer === answer;
    if(evaluation === true){
        element.classList.add("correct");
        document.getElementById('SubmitAnswer').classList.add("correct");
        document.getElementById('win').play();
    }else{
        element.classList.add("incorrect");
        document.getElementById('SubmitAnswer').classList.add("incorrect");
        document.getElementById('lose').play();
    }
    
    
    // Activate and and show Next button 
    document.getElementById('SubmitAnswer').value = evaluation;
    document.getElementById('SubmitAnswer').disabled = false;
    document.getElementById('SubmitAnswer').hidden = false;
}

</script>



<!-- https://freesound.org/people/florianreichelt/sounds/412427/ -->
<audio id="lose" >
  <source src="./Sounds/lose.mp3" type="audio/mp3">
Your browser does not support the audio element.
</audio>

<!-- https://freesound.org/people/LittleRobotSoundFactory/sounds/274181/ -->
<audio id="win" >
  <source src="./Sounds/win.wav" type="audio/wav">
Your browser does not support the audio element.
</audio>
  


</body>
</html> 