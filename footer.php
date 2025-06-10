<?php

    session_start();

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thingiverse</title>
    <script src="script.js"></script>
    <script src="api.js"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gidole&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Orbitron:wght@400..900&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">  
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Gidole&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Orbitron:wght@400..900&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
</head>

    <footer>
        <div class="footer-top">
            <div class="left-item-footer">
                <a class="about-us">About us</a>
                <a class="faq">FAQ</a>
                <a class="cura">Cura</a>
                <a class="for-developers">For Developers</a>
            </div>
            <div class="social-button">
                <div class="like-button"><svg class="instagram" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M15.5 5H9.5C7.29086 5 5.5 6.79086 5.5 9V15C5.5 17.2091 7.29086 19 9.5 19H15.5C17.7091 19 19.5 17.2091 19.5 15V9C19.5 6.79086 17.7091 5 15.5 5Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.5 15C10.8431 15 9.5 13.6569 9.5 12C9.5 10.3431 10.8431 9 12.5 9C14.1569 9 15.5 10.3431 15.5 12C15.5 12.7956 15.1839 13.5587 14.6213 14.1213C14.0587 14.6839 13.2956 15 12.5 15Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <rect x="15.5" y="9" width="2" height="2" rx="1" transform="rotate(-90 15.5 9)" fill="#000000"></rect> <rect x="16" y="8.5" width="1" height="1" rx="0.5" transform="rotate(-90 16 8.5)" stroke="#000000" stroke-linecap="round"></rect> </g></svg>
                </div>
                <div class="like-button">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 50 50">
                        <path d="M 5.9199219 6 L 20.582031 27.375 L 6.2304688 44 L 9.4101562 44 L 21.986328 29.421875 L 31.986328 44 L 44 44 L 28.681641 21.669922 L 42.199219 6 L 39.029297 6 L 27.275391 19.617188 L 17.933594 6 L 5.9199219 6 z M 9.7167969 8 L 16.880859 8 L 40.203125 42 L 33.039062 42 L 9.7167969 8 z"></path>
                        </svg>
                </div>
                <div class="like-button"><svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 50 50">
                    <path d="M25,3C12.85,3,3,12.85,3,25c0,11.03,8.125,20.137,18.712,21.728V30.831h-5.443v-5.783h5.443v-3.848 c0-6.371,3.104-9.168,8.399-9.168c2.536,0,3.877,0.188,4.512,0.274v5.048h-3.612c-2.248,0-3.033,2.131-3.033,4.533v3.161h6.588 l-0.894,5.783h-5.694v15.944C38.716,45.318,47,36.137,47,25C47,12.85,37.15,3,25,3z"></path>
                </svg></div>
            </div>
            <div class="right-item-footer">
                <span class="newsletter">Thingiverse Newsletter</span>
                <p class="subscribe">Subscribe for news, community spotlights, and more!</p>
                <div class="subscribe-input">
                    <input type="text" class="filter-button" placeholder="Enter your email address">
                </input>
                <div class="subscribe-button">
                    <h1>Subscribe</h1>
                </div>
                </div>
                
            </div>
        </div>        
    </footer>