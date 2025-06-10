<?php
session_start();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thingiverse</title>
    <script src="script.js" defer></script>
    <script src="gestione_like.js" defer></script>
    <script src="api.js" defer></script>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gidole&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Orbitron:wght@400..900&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">  
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Gidole&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Orbitron:wght@400..900&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <nav>
        <div class="mobile-nav-item">
            <div class="mobile-left">
                <a class="T-mobile" href="index.html">
                    <svg class="T-svg-mobile" xmlns="http://www.w3.org/2000/svg" viewbox ="0 0 70 70">
                        <path d="M35 70C15.67 70 0 54.33 0 35C0 15.67 15.67 0 35 0C54.33 0 70 15.67 70 35C70 54.33 54.33 70 35 70ZM35 65.0001C51.5685 65.0001 65.0001 51.5685 65.0001 35C65.0001 18.4315 51.5685 4.99994 35 4.99994C18.4315 4.99994 4.99994 18.4315 4.99994 35C4.99994 51.5685 18.4315 65.0001 35 65.0001ZM39.4737 28.1579V58.1578H30.5263V28.1579H17.8948V19.2104H52.1053V28.1578L39.4737 28.1579Z"></path>
                    </svg>
                </a>
            </div>
            <div class="mobile-center-item">
                <div class="search-bar">
                    <span class="search">Search</span>
                    <span class="search-svg">
                        <img src="https://www.svgrepo.com/show/7109/search.svg" alt="search" class="search-icon">
                    </span>
                </div>
            </div>
            <div class="mobile-right">
                <a class="exp-blog">
                    <svg class="exp-svg-mobile" xmlns="http://www.w3.org/2000/svg"viewbox ="0 0 70 70">
                        <path d="M 0 7.5 L 0 12.5 L 50 12.5 L 50 7.5 Z M 0 22.5 L 0 27.5 L 50 27.5 L 50 22.5 Z M 0 37.5 L 0 42.5 L 50 42.5 L 50 37.5 Z"></path>
                    </svg>
                </a>
                <a class="login-mobile">
                    <svg  class="login-svg-mobile" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 4C7.50555 4 7.0222 4.14662 6.61108 4.42133C6.19995 4.69603 5.87952 5.08648 5.6903 5.54329C5.50108 6.00011 5.45158 6.50277 5.54804 6.98773C5.6445 7.47268 5.88261 7.91814 6.23224 8.26777C6.58187 8.6174 7.02733 8.8555 7.51228 8.95196C7.99723 9.04843 8.4999 8.99892 8.95671 8.8097C9.41353 8.62048 9.80397 8.30005 10.0787 7.88893C10.3534 7.4778 10.5 6.99445 10.5 6.5C10.5 5.83696 10.2366 5.20107 9.76777 4.73223C9.29893 4.26339 8.66304 4 8 4ZM8 8C7.70333 8 7.41332 7.91203 7.16665 7.7472C6.91997 7.58238 6.72772 7.34811 6.61418 7.07403C6.50065 6.79994 6.47095 6.49834 6.52882 6.20736C6.5867 5.91639 6.72956 5.64912 6.93934 5.43934C7.14912 5.22956 7.4164 5.0867 7.70737 5.02882C7.99834 4.97094 8.29994 5.00065 8.57403 5.11418C8.84812 5.22771 9.08239 5.41997 9.24721 5.66664C9.41203 5.91332 9.5 6.20333 9.5 6.5C9.49955 6.89769 9.34137 7.27896 9.06017 7.56016C8.77896 7.84137 8.39769 7.99955 8 8Z"></path>
                        <path d="M8 1C6.61553 1 5.26216 1.41054 4.11101 2.17971C2.95987 2.94888 2.06266 4.04213 1.53285 5.32122C1.00303 6.6003 0.86441 8.00776 1.13451 9.36563C1.4046 10.7235 2.07129 11.9708 3.05026 12.9497C4.02922 13.9287 5.2765 14.5954 6.63437 14.8655C7.99224 15.1356 9.3997 14.997 10.6788 14.4672C11.9579 13.9373 13.0511 13.0401 13.8203 11.889C14.5895 10.7378 15 9.38447 15 8C14.9979 6.14413 14.2597 4.36486 12.9474 3.05256C11.6351 1.74026 9.85588 1.00209 8 1ZM5 13.1882V12.5C5.00044 12.1023 5.15862 11.721 5.43983 11.4398C5.72104 11.1586 6.10231 11.0004 6.5 11H9.5C9.89769 11.0004 10.279 11.1586 10.5602 11.4398C10.8414 11.721 10.9996 12.1023 11 12.5V13.1882C10.0896 13.7199 9.05426 14 8 14C6.94574 14 5.91042 13.7199 5 13.1882ZM11.9963 12.4629C11.9863 11.807 11.7191 11.1813 11.2521 10.7206C10.7852 10.2599 10.156 10.0011 9.5 10H6.5C5.84405 10.0011 5.2148 10.2599 4.74786 10.7206C4.28093 11.1813 4.01369 11.807 4.00375 12.4629C3.09703 11.6533 2.45762 10.5873 2.17017 9.40623C1.88272 8.22513 1.9608 6.98457 2.39407 5.84883C2.82734 4.71309 3.59536 3.73573 4.59644 3.04618C5.59751 2.35663 6.78442 1.98741 8 1.98741C9.21558 1.98741 10.4025 2.35663 11.4036 3.04618C12.4046 3.73573 13.1727 4.71309 13.6059 5.84883C14.0392 6.98457 14.1173 8.22513 13.8298 9.40623C13.5424 10.5873 12.903 11.6533 11.9963 12.4629Z"></path>
                    </svg>
                </a>
            </div>
        </div>
        <div class="nav-item">
            <div class="left-item">
                <button class="logo">Nothingiverse</button>
                <div class="menu-dropdown">
                    <span class="explore">Explore</span>
                    <div class="explore-menu">                                    
                        <div class="menu-item-content">
                            <div class="menu-item">
                                <a class="customizer">
                                    <div class="titolo">Customizer</div>
                                    <div class="sottotitolo">
                                        Explore custimizable 3D design tailored just for you!
                                    </div>
                                    <div class="img-explore-menu-item">
                                        <img src="https://cdn.thingiverse.com/site/assets/page_header/page-header__customizer.png">
                                    </div>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="discover">
                                    <div class="titolo">Discover</div>
                                    <div class="sottotitolo">Discover Things, Creators, Tags and more!</div>
                                    <div class="img-explore-menu-item">
                                        <img src="https://cdn.thingiverse.com/site/assets/page_header/page-header__discover.png">
                                    </div>
                                </a>
                            </div>
                            <div class="right-item-menu">
                                <div class="menu-item">
                                    <a class="groups">
                                        <div class="titolo">Groups</div>
                                        <div class="sottotitolo">Learn, discuss and share with other makers</div>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="education">
                                        <div class="titolo">Thingiverse Education</div>
                                        <div class="sottotitolo">Curated free lessons, curriculum and projects</div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>             
                </div>
                <button class="blog">Blog</button>
            </div>
            <div class="center-item">
                <div class="search-bar">
                    <input type="text" class="search-input" placeholder="Search">
                    <span class="search-svg">
                        <img src="https://www.svgrepo.com/show/7109/search.svg" alt="search" class="search-icon">
                    </span>
                </div>
            </div>
            <div class="right-item">
                <span class="login-item">
                    <button class="login">
                        <h1>Log in</h1>
                    </button>
                    <div class="login-dropdown">
                        <div class="login-box">
                            <form action="account.php" method="post">
                                <input type="email" name="email" class="email-input" placeholder="Email" required>
                                <input type="password" name="password" class="password-input" placeholder="Password" required>
                                <h1 class="password-text">Hai già un account?</h1>
                                <button type="button" id="login-button" class="log-in">
                                    <h1>Accedi</h1>
                                </button>
                            </form>
                        </div>
                        <div class="register-box">
                            <form action="account.php" method="post">
                                <input type="text" name="username" class="email-input" placeholder="Username" required>
                                <input type="email" name="email" class="email-input" placeholder="Email" required>
                                <input type="password" name="password" class="password-input" placeholder="Password" required>
                                <input type="password" name="password_confirm" class="password-input" placeholder="Ripeti la Password" required>
                                <button type="button" id="register-button" class="sign-up">
                                    <h1>Registrati su Thingiverse!</h1>
                                </button>
                            </form>
                        </div>
                    </div>
                </span>
            </div>
        </div>
        <div class="welcome">
            <img class="gearspng" src="https://cdn.thingiverse.com/site/assets/hero_images/gears.png">
            <h1 class="benvenuto">Welcome to Thingiverse</h1>    
            <h1 class="digital">Digital Design for Physical Objects</h1>
        </div>
    </nav>
</body>
</html>