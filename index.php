<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Agent</title>
   <link rel="stylesheet" href="statics/css/login.css">

</head>
<body>
    <div class="container">
        <h1>Connexion Agent</h1>
        <div id="alert"></div>
        <form id="loginForm">
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="text" id="email" name="email" value="jean.rabe@banque.mg ">
            </div>
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" value="password123">
            </div>
            <button type="submit">Se connecter</button>
        </form>
    </div>
    <script>
        function showAlert(message, type) {
            const alertDiv = document.getElementById('alert');
            alertDiv.innerHTML = '';
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.textContent = message;
            alertDiv.appendChild(alert);
            setTimeout(() => { alert.remove(); }, 4000);
        }

        document.getElementById('loginForm').onsubmit = function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const data = `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`;
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'http://localhost/www/Ocy/finance/ws/authentification', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    try {
                        const res = JSON.parse(xhr.responseText);
                        if (xhr.status >= 200 && xhr.status < 300 && res.success) {
                            showAlert('Connexion réussie !', 'success');
                            setTimeout(() => { window.location.href = 'interet.php'; }, 1000);
                        } else {
                            showAlert(res.message || 'Erreur de connexion', 'error');
                        }
                    } catch (e) {
                        showAlert('Erreur serveur ou format de réponse.', 'error');
                    }
                }
            };
            xhr.send(data);
        };
    </script>
</body>
</html>