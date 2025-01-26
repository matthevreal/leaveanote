<?php
$notes_file = 'notes.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_note = $_POST['note'] ?? '';
    $position = $_POST['position'] ?? '0,0';
    if (!empty($new_note)) {
        file_put_contents($notes_file, htmlspecialchars($new_note) . "|" . $position . "\n", FILE_APPEND);
    }
}

$notes = [];
if (file_exists($notes_file)) {
    $lines = file($notes_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode('|', $line);
        if (count($parts) === 2) {
            list($text, $position) = $parts;
            $notes[] = ['text' => $text, 'position' => $position];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>leaveanote</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 10000px; /* Infinite scroll effect */
            overflow-y: scroll;
            background-color: #f4f4f4;
            cursor: pointer;
        }
        .note {
            position: absolute;
            background-color: #fffae3;
            border: 1px solid #d4d4d4;
            padding: 8px;
            border-radius: 8px;
            max-width: 200px;
            word-wrap: break-word;
        }
        #form-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #note-form {
            background: white;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 8px;
        }
        button {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button[type="submit"] {
            background: #007BFF;
            color: white;
        }
        button[type="button"] {
            background: #d4d4d4;
            color: black;
        }
    </style>
</head>
<body onclick="openForm(event)">
    <?php foreach ($notes as $note): ?>
        <?php
        $position = explode(',', $note['position']);
        if (count($position) === 2) {
            list($top, $left) = $position;
        } else {
            $top = 0;
            $left = 0;
        }
        ?>
        <div class="note" style="top: <?= $top ?>px; left: <?= $left ?>px;"><?= $note['text'] ?></div>
    <?php endforeach; ?>
<div id="form-container">
    <form id="note-form" method="POST">
        <textarea name="note" placeholder="Type your note here..."></textarea>
        <input type="hidden" name="position" id="position">
        <button type="submit">Save</button>
        <button type="button" onclick="closeForm()">Cancel</button>
    </form>
</div>

<script>
    const formContainer = document.getElementById('form-container');
    const positionInput = document.getElementById('position');

    function openForm(event) {
        if (event.target.tagName === 'TEXTAREA' || event.target.tagName === 'BUTTON') return;
        const form = document.getElementById('note-form');
        formContainer.style.display = 'flex';
        positionInput.value = `${event.clientY + window.scrollY},${event.clientX}`;
    }

    function closeForm() {
        formContainer.style.display = 'none';
    }
</script>
</body>
</html>
