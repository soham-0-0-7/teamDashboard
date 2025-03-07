<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "web_dev_project";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "CREATE TABLE IF NOT EXISTS users (
        uid INT PRIMARY KEY,
        uname VARCHAR(50) NOT NULL UNIQUE,
        pass VARCHAR(50) NOT NULL
    )";
    $conn->query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS skills (
        uid INT PRIMARY KEY,
        skill1 VARCHAR(50),
        skill2 VARCHAR(50),
        skill3 VARCHAR(50),
        skill4 VARCHAR(50),
        skill5 VARCHAR(50),
        FOREIGN KEY (uid) REFERENCES users(uid)
    )";
    $conn->query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS contributions (
        uid INT PRIMARY KEY,
        contris VARCHAR(1000),
        FOREIGN KEY (uid) REFERENCES users(uid)
    )";
    $conn->query($sql);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['signup'])) {
            $uname = $_POST['uname'];
            $pass = $_POST['pass'];

            $sql = "SELECT * FROM users WHERE uname = '$uname'";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                echo "<p class='error'>Username already exists. Please sign in.</p>";
            } else {
                $sql = "SELECT MAX(uid) as max_uid FROM users";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                $max_uid = $row['max_uid'];
                
                if ($max_uid === null) {
                    $uid = 0;
                } else {
                    $uid = $max_uid + 1;
                }
                
                $sql = "INSERT INTO users (uid, uname, pass) VALUES ($uid, '$uname', '$pass')";
                if ($conn->query($sql) === TRUE) {
                    $sql = "INSERT INTO skills (uid) VALUES ($uid)";
                    $conn->query($sql);
                    
                    $sql = "INSERT INTO contributions (uid) VALUES ($uid)";
                    $conn->query($sql);
                    
                    echo "<p class='success'>Sign up successful! Your UID is: " . $uid . "</p>";
                } else {
                    echo "<p class='error'>Error: " . $sql . "<br>" . $conn->error . "</p>";
                }
            }
        } elseif (isset($_POST['signin'])) {
            $uname = $_POST['uname'];
            $pass = $_POST['pass'];

            
            $sql = "SELECT uid FROM users WHERE uname = '$uname' AND pass = '$pass'";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                session_start();
                $_SESSION['uname'] = $uname;
                $_SESSION['uid'] = $row['uid']; 
                header("Location: dashboard.php");
                exit;
            } else {
                echo "<p class='error'>Invalid username or password.</p>";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Development Project</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 30px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        .error {
            color: #f44336;
            text-align: center;
            padding: 10px;
            margin: 10px 0;
            background-color: #fee;
            border-radius: 4px;
        }

        .success {
            color: #4CAF50;
            text-align: center;
            padding: 10px;
            margin: 10px 0;
            background-color: #efe;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <h1>Welcome to the Web Development Project</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" autocomplete="off">
            <input type="text" name="uname" placeholder="Username" required autocomplete="off">
            <input type="password" name="pass" placeholder="Password" required autocomplete="new-password">
            <button type="submit" name="signin">Sign In</button>
            <button type="submit" name="signup">Sign Up</button>
        </form>
    </div>
</body>
</html>