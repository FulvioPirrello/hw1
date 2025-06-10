<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thingiverse</title>
    <script src="script.js" defer></script>
    <script src="profilo_utente.js" defer></script>
    <script src="gestione_like.js" defer></script>
    <script src="api.js" defer></script>
    <script src="lista_modelli.js" defer></script>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="item.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gidole&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Orbitron:wght@400..900&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">  
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Gidole&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Orbitron:wght@400..900&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
    <section>
        <div class="homepage">
            <div class="filter-bar">
                <div class="filter-button">
                    <button class="popular">Popular Last 30 Days</button>
                    <span class="dropdown-arrow"><img src="https://www.svgrepo.com/show/509905/dropdown-arrow.svg" alt=""></span>
                </div>
                <div class="filter-button">
                    <button class="all">All Things</button>
                    <span class="dropdown-arrow"><img src="https://www.svgrepo.com/show/509905/dropdown-arrow.svg" alt=""></span>
                </div>
                <div class="filter-button">
                    <button class="filter">Filter by</button>
                    <span class="dropdown-arrow"><img src="https://www.svgrepo.com/show/509905/dropdown-arrow.svg" alt=""></span>
                </div>
            </div>
            <div class="item-container">
                <div class="item"></div>
            </div>
        </div>
        <div class="fakestore-box">
            <h2 class="fakestore-title">Articoli per stampanti 3d</h2>
            <div class="gadget-container">
                <div class="gadget-item"></div>
                <div class="gadget-item"></div>
                <div class="gadget-item"></div>
                <div class="gadget-item"></div>
                <div class="gadget-item"></div>
                <div class="gadget-item"></div>
            </div>
        </div>
    </section>
    <?php include 'footer.php'; ?>
</body>
</html>