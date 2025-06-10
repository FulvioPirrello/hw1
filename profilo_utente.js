document.addEventListener('DOMContentLoaded', function() 
{
    const login_button = document.getElementById('login-button');

    function trova_errore(input, message) 
    {
        const messaggio_errore = document.createElement('div');
        messaggio_errore.className = 'input-error';
        messaggio_errore.style.color = '#000000';
        messaggio_errore.style.fontSize = '12px';
        messaggio_errore.style.marginTop = '5px';
        messaggio_errore.textContent = message;
        input.parentNode.insertBefore(messaggio_errore, input.nextSibling);
        input.style.borderColor = '#e74c3c';
    }

    if (login_button) {
        login_button.addEventListener('click', function() {
            const form = this.closest('form');
            const email_input = form.querySelector('input[name="email"]');
            const password_input = form.querySelector('input[name="password"]');
            let controlla_errore = false;

            const rimuovi_errori = form.querySelectorAll('.input-error, .form-error');
            rimuovi_errori.forEach(error => error.remove());
            
            if (email_input.value === '') {
                trova_errore(email_input, 'Inserire Email o Username');
                controlla_errore = true;
            }
            
            if (password_input.value === '') {
                trova_errore(password_input, 'Password obbligatoria');
                controlla_errore = true;
            }
            
            if (!controlla_errore) {
                const controllo_form = new FormData();
                controllo_form.append('email', email_input.value);
                controllo_form.append('password', password_input.value);
                controllo_form.append('login', 'true');
                controllo_form.append('ajax', 'true');
                
                fetch('account.php', {
                    method: 'POST',
                    body: controllo_form
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        login_username(data.username);
                        const loginMenu = document.querySelector('.login-dropdown');
                        if (loginMenu) loginMenu.style.display = "none";
                    } else {
                        trova_errore(email_input, data.errors ? data.errors[0] : 'Credenziali non valide');
                    }
                })
            }
        });
    }

    const registerButton = document.getElementById('register-button');

    if (registerButton) 
    {
        registerButton.addEventListener('click', function() 
        {
            const form = this.closest('form');
            const username_input = form.querySelector('input[name="username"]');
            const email_input = form.querySelector('input[name="email"]');
            const password_input = form.querySelector('input[name="password"]');
            const pw_confirm_input = form.querySelector('input[name="password_confirm"]');
            let controlla_errore = false;
            
            const rimuovi_errori = form.querySelectorAll('.input-error, .form-error');
            rimuovi_errori.forEach(controlla_errore => controlla_errore.remove());

            if (username_input.value === '')     
            {
                trova_errore(username_input, 'Username obbligatorio');
                controlla_errore = true;
            } 
            else if (username_input.value.length < 3) {
                trova_errore(username_input, 'Username troppo corto (min 3 caratteri)');
                controlla_errore = true;
            }
            
            const controlla_email = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email_input.value === '') 
            {   
                trova_errore(email_input, 'Email obbligatoria');
                controlla_errore = true;
            } 
            else if (!controlla_email.test(email_input.value.trim())) 
            {
                trova_errore(email_input, 'Email non valida');
                controlla_errore = true;
            }

            if (password_input.value === '') 
            {
                trova_errore(password_input, 'Password obbligatoria');
                controlla_errore = true;
            } 
            else if (password_input.value.length < 8 || password_input.value.length > 16) 
            {
                trova_errore(password_input, 'La password deve essere di minimo 8 e massimo 16 caratteri');
                controlla_errore = true;
            } 
            else if (!/[A-Z]/.test(password_input.value)) 
            {
                trova_errore(password_input, 'La password deve contenere almeno un carattere maiuscolo');
                controlla_errore = true;
            } 
            else if (!/[0-9]/.test(password_input.value)) 
            {
                trova_errore(password_input, 'La password deve contenere almeno un numero');
                controlla_errore = true;
            } 
            else if (!/[\@\£\$\!\?]/.test(password_input.value)) 
            {
                trova_errore(password_input, 'La password deve contenere almeno un carattere speciale (@£$!?)');
                controlla_errore = true;
            }
            
            if (pw_confirm_input.value === '') 
            {
                trova_errore(pw_confirm_input, 'Ripeti la password');
                controlla_errore = true;
            } 
            else if (password_input.value !== pw_confirm_input.value) 
            {
                trova_errore(pw_confirm_input, 'La password non coincide');
                controlla_errore = true;
            }
            
            if (!controlla_errore) 
            {
                const controllo_form = new FormData();
                controllo_form.append('username', username_input.value);
                controllo_form.append('email', email_input.value);
                controllo_form.append('password', password_input.value);
                controllo_form.append('password_confirm', pw_confirm_input.value);
                controllo_form.append('register', 'true');
                controllo_form.append('ajax', 'true');
                
                fetch('account.php', {
                    method: 'POST',
                    body: controllo_form
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        login_username(data.username);
                        const loginMenu = document.querySelector('.login-dropdown');
                        if (loginMenu) loginMenu.style.display = "none";
                    } else {
                        if (data.errors && data.errors.length > 0) {
                            trova_errore(username_input, data.errors[0]);
                        }
                    }
                })
            }
        });
    }
    
    controlla_login();
    
    function controlla_login() {
        fetch('profilo_utente.php')
            .then(response => response.json())
            .then(data => {
                if (data.logged_in) {
                    login_username(data.username);
                }
            })
            .catch(error => console.error('Errore controllo login:', error));
    }
    
    function login_username(username) {
        const login_item = document.querySelector('.login-item');
        if (!login_item) return;
        
        const profilo_utente = `
        <div class="profilo_utente">
            <button class="username_button">
                <a class="user_pic"><img src="https://img.thingiverse.com/cdn-cgi/image/fit=cover,quality=90,width=48,height=48/https://cdn.thingiverse.com/assets/8f/0c/6a/cd/0d/neil_avatar.png"></a>
                <span class="username">${username}</span>
            </button>
            <div class="username_dropdown">
                <ul class="dropdown-list">
                    <li class="dropdown-item"><a href="profilo.php" class="dropdown-link">Il tuo profilo</a></li>
                    <li class="dropdown-item"><a href="preferiti.php" class="dropdown-link">Preferiti</a></li>
                    <li class="dropdown-item"><a href="account.php?logout=true" class="dropdown-link">Logout</a></li>
                </ul>
            </div>
        </div>
        `;
        
        login_item.innerHTML = profilo_utente;
        
        const username_button = login_item.querySelector(".username_button");
        const username_dropdown = login_item.querySelector(".username_dropdown");

        function chiudiMenu() {
            if(username_dropdown) username_dropdown.style.display = "none";
        }
        
        if(username_button && username_dropdown) {
            username_button.addEventListener("click", function(e) {
                e.stopPropagation();
                const isOpen = username_dropdown.style.display === "flex";
                
                chiudiMenu();
                
                if(!isOpen) {
                    username_dropdown.style.display = "flex";
                }
            });
        }

        if(username_dropdown) {
            username_dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
        
        document.addEventListener("click", function() {
            chiudiMenu();
        });
    }    
});