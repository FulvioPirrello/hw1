document.addEventListener("DOMContentLoaded", function () {
    const exploreButton = document.querySelector(".explore");
    const exploreMenu = document.querySelector(".explore-menu");
    const loginButton = document.querySelector(".login");
    const loginMenu = document.querySelector(".login-dropdown");
    
    fetch('profilo_utente.php?check_login_errors=true')
        .then(response => response.json())
        .then(data => {
            if (data.login_errors && data.login_errors.length > 0) {
                if (loginMenu) loginMenu.style.display = "flex";
                
                const loginForm = document.querySelector('.login-dropdown form');
                if (loginForm) {
                    const email_input = loginForm.querySelector('input[name="email"]');
                    if (email_input) {
                        const messaggio_errore = document.createElement('div');
                        messaggio_errore.className = 'input-error';
                        messaggio_errore.style.color = '#000000';
                        messaggio_errore.style.fontSize = '12px';
                        messaggio_errore.style.marginTop = '5px';
                        messaggio_errore.textContent = data.login_errors[0];
                        email_input.parentNode.insertBefore(messaggio_errore, email_input.nextSibling);
                        email_input.style.borderColor = '#e74c3c';
                    }
                }
            }
        })
        .catch(error => console.error('Errore controllo login:', error));
    
    function chiudiMenu() {
        if(exploreMenu) exploreMenu.style.display = "none";
        if(loginMenu) loginMenu.style.display = "none";
        const usernameDropdown = document.querySelector(".username_dropdown");
        if(usernameDropdown) usernameDropdown.style.display = "none";
    }

    if(exploreMenu) {
        exploreMenu.addEventListener("click", function (e) {
            e.stopPropagation();
        });
    }
    
    if(loginMenu) {
        loginMenu.addEventListener("click", function (e) {
            e.stopPropagation();
        });
    }
    
    document.addEventListener("click", function() {
        chiudiMenu();
    });

    if(exploreButton && exploreMenu) {
        exploreButton.addEventListener("click", function (e) {
            e.stopPropagation();
            const isOpen = exploreMenu.style.display === "block";
            chiudiMenu();
            if (!isOpen) {
                exploreMenu.style.display = "block";
            }
        });
    }
    
    if(loginButton && loginMenu) {
        loginButton.addEventListener("click", function (e) {
            e.stopPropagation();
            const isOpen = loginMenu.style.display === "flex";
            chiudiMenu();
            if (!isOpen) {
                loginMenu.style.display = "flex";
            }
        });
    }
    
    document.addEventListener("click", function(e) {
        if(e.target.closest(".username_button")) {
            e.stopPropagation();
            const dropdown = document.querySelector(".username_dropdown");
            if(dropdown) {
                const isOpen = dropdown.style.display === "block";
                chiudiMenu();
                if(!isOpen) {
                    dropdown.style.display = "block";
                }
            }
        }
        
        if(e.target.closest(".username_dropdown")) {
            e.stopPropagation();
        }
    });

    const logoElement = document.querySelector('.logo');
    if (logoElement) {
        logoElement.addEventListener('click', function(e) {
            e.preventDefault(); 
            window.location.href = 'index.php';
        });
    }
});
