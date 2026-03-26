<?php

// Function to clean user input
function clean_data(string $input): string
{
    $input = trim($input);
    $input = stripslashes($input);
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

// Initialize variables
$fullName = $userEmail = $userGender = "";
$contactNum = $siteLink = "";
$agreed = false;

$errName = $errEmail = $errGender = "";
$errPhone = $errSite = "";
$errPass = $errConfirm = "";
$errTerms = "";

$tryCount = 0;
$isSuccess = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Count how many times form is submitted
    $tryCount = isset($_POST["tryCount"]) ? (int)$_POST["tryCount"] : 0;
    $tryCount++;

    // Validate Full Name
    if (empty($_POST["fullName"])) {
        $errName = "Full name is required";
    } else {
        $tempName = trim(stripslashes($_POST["fullName"]));
        if ($tempName === "") {
            $errName = "Full name is required";
        } else {
            $fullName = clean_data($tempName);
        }
    }

    // Validate Email Address
    if (empty($_POST["userEmail"])) {
        $errEmail = "Email cannot be empty";
    } else {
        $tempEmail = trim(stripslashes($_POST["userEmail"]));
        $userEmail = ($tempEmail === "") ? "" : clean_data($tempEmail);

        if ($tempEmail === "" || !filter_var($tempEmail, FILTER_VALIDATE_EMAIL)) {
            $errEmail = "Please enter a valid email";
        }
    }

    // Validate Gender Selection
    if (
        empty($_POST["userGender"]) ||
        !in_array($_POST["userGender"], ["Male", "Female", "Other"], true)
    ) {
        $errGender = "Please select a gender";
    } else {
        $userGender = clean_data($_POST["userGender"]);
    }

    // Validate Contact Number
    if (empty($_POST["contactNum"])) {
        $errPhone = "Contact number is required";
    } else {
        $tempPhone = trim(stripslashes($_POST["contactNum"]));
        $contactNum = ($tempPhone === "") ? "" : clean_data($tempPhone);

        $phoneRegex = '/^\+?[0-9\s-]{7,15}$/';
        if ($tempPhone === "" || !preg_match($phoneRegex, $tempPhone)) {
            $errPhone = "Invalid contact number";
        }
    }

    // Validate Website (optional)
    if (isset($_POST["siteLink"]) && $_POST["siteLink"] !== "") {
        $tempSite = trim(stripslashes($_POST["siteLink"]));
        if ($tempSite === "" || !filter_var($tempSite, FILTER_VALIDATE_URL)) {
            $errSite = "Invalid website link";
            $siteLink = clean_data($tempSite);
        } else {
            $siteLink = clean_data($tempSite);
        }
    }

    // Validate Password & Confirmation
    $passInput = isset($_POST["userPass"]) ? trim(stripslashes($_POST["userPass"])) : "";
    $confirmInput = isset($_POST["confirmPass"]) ? trim(stripslashes($_POST["confirmPass"])) : "";

    if ($passInput === "") {
        $errPass = "Password is required";
    } elseif (strlen($passInput) < 8) {
        $errPass = "Password must be at least 8 characters";
    }

    if ($confirmInput === "") {
        $errConfirm = "Please confirm your password";
    } elseif ($confirmInput !== $passInput) {
        $errConfirm = "Passwords do not match";
    }

    // Validate Terms Agreement
    $agreed = isset($_POST["agreeTerms"]);
    if (!$agreed) {
        $errTerms = "You must accept the terms";
    }

    // Check if there are errors
    $hasError = (
        $errName || $errEmail || $errGender ||
        $errPhone || $errSite || $errPass ||
        $errConfirm || $errTerms
    );

    if (!$hasError) {
        $isSuccess = true;
    }
}

// Form action
$formAction = htmlspecialchars($_SERVER["PHP_SELF"] ?? '', ENT_QUOTES, 'UTF-8');

?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Validation Example</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .error { color: red; font-size: 0.9em; }
        .box { margin-bottom: 12px; }
        .success { background: #d4edda; padding: 10px; border: 1px solid green; margin-top: 15px; }
    </style>
</head>
<body>

<h2>Sample Form</h2>
<p><strong>Attempts:</strong> <?php echo $tryCount; ?></p>

<form method="post" action="<?php echo $formAction; ?>">
<input type="hidden" name="tryCount" value="<?php echo $tryCount; ?>">

<div class="box">
    Name:
    <input type="text" name="fullName" value="<?php echo $fullName; ?>">
    <span class="error"><?php echo $errName; ?></span>
</div>

<div class="box">
    Email:
    <input type="email" name="userEmail" value="<?php echo $userEmail; ?>">
    <span class="error"><?php echo $errEmail; ?></span>
</div>

<div class="box">
    Gender:
    <input type="radio" name="userGender" value="Male" <?php if($userGender=="Male") echo "checked"; ?>> Male
    <input type="radio" name="userGender" value="Female" <?php if($userGender=="Female") echo "checked"; ?>> Female
    <input type="radio" name="userGender" value="Other" <?php if($userGender=="Other") echo "checked"; ?>> Other
    <span class="error"><?php echo $errGender; ?></span>
</div>

<div class="box">
    Phone:
    <input type="text" name="contactNum" value="<?php echo $contactNum; ?>">
    <span class="error"><?php echo $errPhone; ?></span>
</div>

<div class="box">
    Website:
    <input type="url" name="siteLink" value="<?php echo $siteLink; ?>">
    <span class="error"><?php echo $errSite; ?></span>
</div>

<div class="box">
    Password:
    <input type="password" name="userPass">
    <span class="error"><?php echo $errPass; ?></span>
</div>

<div class="box">
    Confirm Password:
    <input type="password" name="confirmPass">
    <span class="error"><?php echo $errConfirm; ?></span>
</div>

<div class="box">
    <input type="checkbox" name="agreeTerms" <?php echo $agreed ? "checked" : ""; ?>>
    Accept Terms
    <div class="error"><?php echo $errTerms; ?></div>
</div>

<button type="submit">Submit</button>
</form>

<?php if ($isSuccess): ?>
<div class="success">
    <h3>Form Submitted!</h3>
    <p>Name: <?php echo $fullName; ?></p>
    <p>Email: <?php echo $userEmail; ?></p>
    <p>Gender: <?php echo $userGender; ?></p>
    <p>Phone: <?php echo $contactNum; ?></p>
    <?php if ($siteLink !== ""): ?>
        <p>Website: <?php echo $siteLink; ?></p>
    <?php endif; ?>
</div>
<?php endif; ?>

</body>
</html>