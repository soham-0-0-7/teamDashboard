<?php
    session_start();
    if (!isset($_SESSION['uid'])) {
        header("Location: index.php");
        exit;
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "web_dev_project";
    $conn = new mysqli($servername, $username, $password, $dbname);

    $uid = $_SESSION['uid'];
    $uname = $_SESSION['uname'];

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_contribution'])) {
        $sql = "UPDATE contributions SET contris = NULL WHERE uid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $uid);
        
        if ($stmt->execute()) {
            echo "<p class='success'>Contribution deleted successfully!</p>";
        } else {
            echo "<p class='error'>Error deleting contribution.</p>";
        }
        $stmt->close();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_contribution'])) {
        $check_sql = "SELECT contris FROM contributions WHERE uid = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $uid);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row && !empty($row['contris'])) {
            echo "<p class='error'>You already have a contribution. Please delete your existing contribution before adding a new one.</p>";
        } else {
            $new_contribution = $_POST['new_contribution'];
            $sql = "UPDATE contributions SET contris = ? WHERE uid = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_contribution, $uid);
            
            if ($stmt->execute()) {
                echo "<p class='success'>Contribution added successfully!</p>";
            } else {
                echo "<p class='error'>Error adding contribution.</p>";
            }
            $stmt->close();
        }
        $check_stmt->close();
    }

    $sql = "SELECT contris FROM contributions WHERE uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $contribution = "";
    if ($row = $result->fetch_assoc()) {
        $contribution = $row['contris'];
    }
    $stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Contribution</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            color: #2c3e50;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: white;
            border-radius: 15px;
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            
        }

        .contributions-header {
            background: lightgreen;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }

        .contributions-header h2 {
            color: white;
            margin: 0;
            font-size: 1.5em;
        }

        .contribution-item {
            /* color: white;  */
            background: lightgreen;
            padding: 20px;
            margin: 15px 0;
            border-radius: 10px;
            color: #2c3e50;
            font-size: 1.1em;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
        }

        .no-contributions {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
            font-style: italic;
            background-color: #f8f9fa;
            border-radius: 10px;
            margin: 20px 0;
            border: 2px dashed #ddd;
        }

        .contribution-form {
            margin-top: 30px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            display: <?php echo empty($contribution) ? 'block' : 'none'; ?>;
        }

        textarea {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            min-height: 100px;
            resize: vertical;
            font-family: inherit;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        button {
            background: lightgreen;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 1.1em;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .delete-form {
            display: inline;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        .delete-button {
            background: lightgreen;
            padding: 8px 15px;
            font-size: 0.9em;
            width: auto;
            margin: 0;
        }

        .success {
            background: lightgreen;
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 10px 0;
        }

        .error {
            background: lightgreen;
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 10px 0;
        }

        nav {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }

        nav a {
            text-decoration: none;
            color: #2c3e50;
            font-weight: bold;
            padding: 12px 24px;
            border-radius: 8px;
            background: #f8f9fa;
        }

        nav a:hover {
            background:lightgreen;
            color: white;
            
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Contribution</h1>
        
        <div class="contributions-header">
            <h2>Welcome, <?php echo htmlspecialchars($uname); ?>!</h2>
            <div class="contributions-count">
                <?php echo empty($contribution) ? "No contribution yet" : "You have shared your contribution"; ?>
            </div>
        </div>

        <?php if (empty($contribution)): ?>
            <div class="no-contributions">
                You haven't made your contribution yet. Share your work below!
            </div>
        <?php else: ?>
            <div class="contribution-item">
                <?php echo htmlspecialchars($contribution); ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="delete-form">
                    <button type="submit" name="delete_contribution" class="delete-button">Delete 🗑️</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="contribution-form">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <textarea name="new_contribution" placeholder="Share your contribution here..." required></textarea>
                <button type="submit" name="add_contribution">Add Contribution ✨</button>
            </form>
        </div>

        <nav>
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="contributions.php">🎯 Contributions</a>
            <a href="index.php">👋 Logout</a>
        </nav>
    </div>
</body>
</html>