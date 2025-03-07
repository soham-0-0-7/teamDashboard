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

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_skill'])) {
        $skill_to_delete = $_POST['skill_to_delete'];
        $skill_column = $_POST['skill_column'];
        
        $sql = "UPDATE skills SET $skill_column = NULL WHERE uid = $uid";
        if ($conn->query($sql) === TRUE) {
            $sql = "SELECT skill1, skill2, skill3, skill4, skill5 FROM skills WHERE uid = $uid";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $skills = array();
                for ($i = 1; $i <= 5; $i++) {
                    if (!empty($row["skill$i"])) {
                        $skills[] = $row["skill$i"];
                    }
                }
                
                $sql = "UPDATE skills SET skill1 = NULL, skill2 = NULL, skill3 = NULL, skill4 = NULL, skill5 = NULL WHERE uid = $uid";
                $conn->query($sql);
                
                for ($i = 0; $i < count($skills); $i++) {
                    $column = "skill" . ($i + 1);
                    $skill_value = $skills[$i];
                    $sql = "UPDATE skills SET $column = '$skill_value' WHERE uid = $uid";
                    $conn->query($sql);
                }
            }
            echo "<p class='success'>Skill deleted successfully!</p>";
        } else {
            echo "<p class='error'>Error deleting skill.</p>";
        }
    }

    // Handle skill addition
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_skill'])) {
        $new_skill = $_POST['new_skill'];
        
        $sql = "SELECT skill1, skill2, skill3, skill4, skill5 FROM skills WHERE uid = $uid";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            for ($i = 1; $i <= 5; $i++) {
                if (empty($row["skill$i"])) {
                    $sql = "UPDATE skills SET skill$i = '$new_skill' WHERE uid = $uid";
                    if ($conn->query($sql) === TRUE) {
                        echo "<p class='success'>Skill added successfully!</p>";
                    } else {
                        echo "<p class='error'>Error adding skill.</p>";
                    }
                    break;
                }
                if ($i == 5 && !empty($row["skill5"])) {
                    echo "<p class='error'>Maximum skills limit reached (5 skills).</p>";
                }
            }
        }
    }

    $sql = "SELECT skill1, skill2, skill3, skill4, skill5 FROM skills WHERE uid = $uid";
    $result = $conn->query($sql);
    $skills = array();
    $skill_columns = array();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        for ($i = 1; $i <= 5; $i++) {
            if (!empty($row["skill$i"])) {
                $skills[] = $row["skill$i"];
                $skill_columns[] = "skill$i";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style> body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
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

        h2 {
            color: #34495e;
            margin-top: 30px;
        }

        .skill-count {
            background: purple;
            color: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            font-size: 1.2em;
            margin: 20px 0;
        }

        .skill-list {
            list-style-type: none;
            padding: 0;
        }

        .skill-item {
            background: purple;
            padding: 20px;
            margin: 15px 0;
            border-radius: 10px;
            color: white;
            font-weight: bold;
            font-size: 1.1em;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .skill-item:hover {
            transform: translateY(-5px);
        }

        .no-skills {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
            font-style: italic;
            background-color: #f8f9fa;
            border-radius: 10px;
            margin: 20px 0;
        }

        form {
            margin-top: 30px;
        }

        input[type="text"] {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
        }

        input[type="text"]:focus {
            border-color: #667eea;
            outline: none;
        }

        button {
            background: purple;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 1.1em;
            margin-top: 10px;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
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
            padding: 10px 20px;
            border-radius: 5px;
        }

        nav a:hover {
            background-color: #667eea;
            color: white;
        }

        .success {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 10px 0;
        }

        .error {
            background-color: #f44336;
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 10px 0;
        }
        .skill-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: purple;
            padding: 20px;
            margin: 15px 0;
            border-radius: 10px;
            color: white;
            font-weight: bold;
            font-size: 1.1em;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .delete-button {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .delete-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .skill-text {
            flex-grow: 1;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($uname); ?>! 👋</h1>
        
        <div class="skill-count">
            Skills Progress: <?php echo count($skills); ?>/5
        </div>

        <h2>🌟 Your Skills</h2>
        <?php if (empty($skills)): ?>
            <div class="no-skills">No skills added yet. Start adding your expertise!</div>
        <?php else: ?>
            <ul class="skill-list">
                <?php for ($i = 0; $i < count($skills); $i++): ?>
                    <li class="skill-item">
                        <span class="skill-text">🔸 <?php echo htmlspecialchars($skills[$i]); ?></span>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="display: inline; margin: 0;">
                            <input type="hidden" name="skill_to_delete" value="<?php echo htmlspecialchars($skills[$i]); ?>">
                            <input type="hidden" name="skill_column" value="<?php echo htmlspecialchars($skill_columns[$i]); ?>">
                            <button type="submit" name="delete_skill" class="delete-button">❌ Delete</button>
                        </form>
                    </li>
                <?php endfor; ?>
            </ul>
        <?php endif; ?>

        <?php if (count($skills) < 5): ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <input type="text" name="new_skill" placeholder="Enter a new skill..." required>
                <button type="submit" name="add_skill">Add Skill ✨</button>
            </form>
        <?php endif; ?>

        <nav>
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="contributions.php">🎯 Contributions</a>
            <a href="index.php">👋 Logout</a>
        </nav>
    </div>
</body>
</html>