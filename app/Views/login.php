    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Connexion - Mobile Money</title>
    </head>
    <body>
        <form action="<?= site_url('login') ?>" method="POST">
            <?= /* csrf_field() */ 1?>
            <div>
                <label for="role">Type de compte :</label>
                <select name="role" id="role" onchange="changerFormulaire(this.value)">
                    <option value="admin">Administrateur</option>
                    <option value="client">Client</option>
                </select>
            </div>

            <br>

            <div id="bloc-email">
                <label for="email">Email :</label><br>
                <input type="email" name="email" id="email" required>
            </div>

            <div id="bloc-telephone" style="display: none;">
                <label for="telephone">Numéro de téléphone :</label><br>
                <input type="text" name="telephone" id="telephone">
            </div>

            <br>

            <div id="champ-mdp">
                <label for="mdp">Mot de passe :</label><br>
                <input type="password" name="mdp" id="mdp" required>
            </div>

            <br>

            <button type="submit">Se connecter</button>
        </form>

        <script>
            function changerFormulaire(role) {
                const blocEmail = document.getElementById('bloc-email');
                const inputEmail = document.getElementById('email');
                
                const blocTelephone = document.getElementById('bloc-telephone');
                const inputTelephone = document.getElementById('telephone');
                
                const champMdp = document.getElementById('champ-mdp');
                const inputMdp = document.getElementById('mdp');

                if (role === 'client') {
                    blocEmail.style.display = 'none';
                    inputEmail.required = false;

                    blocTelephone.style.display = 'block';
                    inputTelephone.required = true;

                    champMdp.style.display = 'none';
                    inputMdp.required = false;
                } else {
                    blocEmail.style.display = 'block';
                    inputEmail.required = true;

                    blocTelephone.style.display = 'none';
                    inputTelephone.required = false;

                    champMdp.style.display = 'block';
                    inputMdp.required = true;
                }
            }
        </script>
    </body>
    </html>