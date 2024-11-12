<?php
include 'languages.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $strings[$lang]['title']; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="language-selector">
        <form action="" method="get" id="languageForm">
            <label><?php echo $strings[$lang]['language']; ?>:</label>
            <select name="lang" onchange="document.getElementById('languageForm').submit()">
                <option value="en" <?php echo $lang === 'en' ? 'selected' : ''; ?>>English</option>
                <option value="pt" <?php echo $lang === 'pt' ? 'selected' : ''; ?>>Português</option>
                <option value="ru" <?php echo $lang === 'ru' ? 'selected' : ''; ?>>Русский</option>
                <option value="zh" <?php echo $lang === 'zh' ? 'selected' : ''; ?>>中文</option>
            </select>
        </form>
    </div>

    <div class="container">
        <h2><?php echo $strings[$lang]['title']; ?></h2>

        <div id="changePassword" class="tab-content active">
            <form id="changeForm" enctype="multipart/form-data">
                <label for="p12File"><?php echo $strings[$lang]['select_file']; ?>:</label>
                <input type="file" name="p12File" id="p12File" required>

                <label for="currentPassword"><?php echo $strings[$lang]['current_password']; ?>:</label>
                <input type="password" name="currentPassword" id="currentPassword" required>

                <label for="newPassword"><?php echo $strings[$lang]['new_password']; ?>:</label>
                <input type="password" name="newPassword" id="newPassword" required>

                <button type="button" onclick="changePassword()"><?php echo $strings[$lang]['change']; ?></button>
            </form>
        </div>

        <div id="result"><?php echo $strings[$lang]['result']; ?></div>
    </div>

    <script>
        function changePassword() {
            const formData = new FormData(document.getElementById('changeForm'));
            fetch('api.php?action=change-password', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('result').innerHTML = `<div class="error">Error: ${data.error}</div>`;
                } else {
                    document.getElementById('result').innerHTML = `
                        <div class="success">
                            <p>${data.message}</p>
                            <a href="changed_certificates/${data.file}" class="download-button" download>${data.file}</a>
                        </div>
                    `;
                }
            })
            .catch(error => {
                document.getElementById('result').innerHTML = `<div class="error">Error changing password.</div>`;
            });
        }
    </script>
</body>
</html>
