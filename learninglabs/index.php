<?php
$db = mysqli_connect('localhost', 'root', '', 'learninglabs');
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

$errors = "";

if (isset($_POST['submit'])) {
    $task = mysqli_real_escape_string($db, $_POST['task']);
    $deadline = mysqli_real_escape_string($db, $_POST['deadline']);

    if (empty($task)) {
        $errors = "Tugas tidak boleh kosong.";
    } else {
        mysqli_query($db, "INSERT INTO tasks (task, deadline) VALUES ('$task', '$deadline')");
        header('Location: index.php');
        exit();
    }
}

if (isset($_GET['delTask'])) {
    $id = (int)$_GET['delTask'];
    mysqli_query($db, "DELETE FROM tasks WHERE id = $id");
    header('Location: index.php');
    exit();
}

if (isset($_POST['editTask'])) {
    $id = (int)$_POST['task_id'];
    $task = mysqli_real_escape_string($db, $_POST['task']);
    $deadline = mysqli_real_escape_string($db, $_POST['deadline']);
    mysqli_query($db, "UPDATE tasks SET task='$task', deadline='$deadline' WHERE id=$id");
    header('Location: index.php');
    exit();
}

$tasks = mysqli_query($db, "SELECT * FROM tasks");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="heading">
        <h2>⋆˚࿔ To-Do List 𝜗𝜚˚⋆</h2>
    </div>

    <form method="POST" action="index.php" id="taskForm">
        <?php if ($errors) { ?>
            <p class="error"><?php echo $errors; ?></p>
        <?php } ?>
        <input type="text" name="task" id="taskInput" class="taskInput" placeholder="Tambahkan tugas baru...">
        <input type="date" name="deadline" class="taskInput" placeholder="Tambahkan deadline">
        <button type="submit" class="add_btn" name="submit">Tambahkan tugas</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tugas</th>
                <th>Deadline</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php $i = 1; while ($row = mysqli_fetch_array($tasks)) { ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td class="task"><?php echo $row['task']; ?></td>
                <td class="deadline"><?php echo $row['deadline']; ?></td>
                <td class="actions">
                    <a href="index.php?delTask=<?php echo $row['id']; ?>" class="delete">✖</a>
                    <span class="edit" data-id="<?php echo $row['id']; ?>" data-task="<?php echo $row['task']; ?>" data-deadline="<?php echo $row['deadline']; ?>">✎</span>
                </td>
            </tr>
        <?php $i++; } ?>
        </tbody>
    </table>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form method="POST" action="index.php">
                <input type="hidden" name="task_id" id="task_id">
                <input type="text" name="task" id="edittaskInput" class="taskInput" placeholder="Ubah nama tugas...">
                <input type="date" name="deadline" id="edit_deadline_input" class="taskInput">
                <button type="submit" name="editTask" class="add_btn">Simpan</button>
            </form>
        </div>
    </div>

    <script>
        var modal = document.getElementById('editModal');
        var closeModal = document.getElementsByClassName('close')[0];

        document.querySelectorAll('.edit').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('task_id').value = this.getAttribute('data-id');
                document.getElementById('edittaskInput').value = this.getAttribute('data-task');
                document.getElementById('edit_deadline_input').value = this.getAttribute('data-deadline');
                modal.style.display = "block";
            });
        });

        closeModal.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
