<?php
session_start();
$authenticated = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'upload';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $activeTab = 'gallery';
    $correctPassword = 'mariage2024'; // À modifier avec votre mot de passe
    
    if ($_POST['password'] === $correctPassword) {
        $_SESSION['authenticated'] = true;
        $authenticated = true;
    } else {
        $error = "Mot de passe incorrect";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📸 Album Mariage - Les Éternels</title>
    <style>
        :root {
            --primary: #e75480;
            --secondary: #4a4e69;
            --light: #f2e9e4;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: var(--light);
            color: var(--secondary);
            line-height: 1.6;
        }

        .header {
            text-align: center;
            padding: 30px 20px;
            background: white;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }

        .upload-container, .gallery-container, .password-form {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .tabs {
            display: flex;
            margin-bottom: 20px;
            overflow-x: auto;
            padding-bottom: 5px;
        }

        .tab {
            padding: 12px 25px;
            cursor: pointer;
            background: #f1f1f1;
            margin-right: 8px;
            border-radius: 30px;
            white-space: nowrap;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .tab.active {
            background: var(--primary);
            color: white;
            transform: scale(1.05);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .tab-content.active {
            display: block;
        }

        #drop-zone {
            border: 2px dashed #ccc;
            padding: 40px 20px;
            text-align: center;
            margin: 20px 0;
            cursor: pointer;
            transition: all 0.3s;
            border-radius: 8px;
        }

        #drop-zone:hover {
            background: #f9f9f9;
            border-color: var(--primary);
        }

        #file-input {
            display: none;
        }

        .button {
            background: var(--primary);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
            font-weight: 500;
            display: inline-block;
            margin-top: 10px;
        }

        .button:hover {
            background: #d43d6d;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .loading {
            display: none;
            margin: 20px 0;
            text-align: center;
        }

        .password-form {
            max-width: 400px;
            margin: 30px auto;
            text-align: center;
        }

        .password-form h2 {
            color: var(--primary);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .error {
            color: #d32f2f;
            margin-top: 15px;
        }

        .password-message {
            margin: 20px auto;
            padding: 15px;
            max-width: 400px;
            background: #ffebee;
            color: #d32f2f;
            border-radius: 5px;
            text-align: center;
        }

        .carousel {
            position: relative;
            width: 100%;
            max-width: 800px;
            margin: 30px auto;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }

        .carousel-inner {
            display: flex;
            transition: transform 0.5s ease;
        }

        .carousel-item {
            min-width: 100%;
            position: relative;
        }

        .carousel-item img {
            width: 100%;
            display: block;
            max-height: 500px;
            object-fit: contain;
            background: #f8f8f8;
        }

        .carousel-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.6);
            color: white;
            padding: 15px;
            text-align: center;
        }

        .carousel-control {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            padding: 15px;
            cursor: pointer;
            z-index: 10;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: all 0.3s;
        }

        .carousel-control:hover {
            background: rgba(0,0,0,0.8);
        }

        .carousel-control.prev {
            left: 15px;
        }

        .carousel-control.next {
            right: 15px;
        }

        .carousel-indicators {
            display: flex;
            justify-content: center;
            margin-top: 15px;
        }

        .indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #ccc;
            margin: 0 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .indicator.active {
            background: var(--primary);
            transform: scale(1.2);
        }

        .thumbnails {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 20px;
            max-height: 200px;
            overflow-y: auto;
            padding: 5px;
        }

        .thumbnail {
            cursor: pointer;
            border-radius: 5px;
            overflow: hidden;
            opacity: 0.7;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .thumbnail:hover, .thumbnail.active {
            opacity: 1;
            transform: scale(1.05);
        }

        .thumbnail.active {
            border-color: var(--primary);
        }

        .thumbnail img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            display: block;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .header {
                padding: 20px 10px;
            }

            .upload-container, .gallery-container, .password-form {
                padding: 15px;
            }

            .tab {
                padding: 10px 15px;
                font-size: 14px;
            }

            .carousel {
                margin: 15px auto;
            }

            .carousel-item img {
                max-height: 300px;
            }

            .thumbnails {
                grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            }
        }

        @media (max-width: 480px) {
            .carousel-control {
                width: 30px;
                height: 30px;
                padding: 10px;
                font-size: 16px;
            }

            .thumbnails {
                grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            }

            .thumbnail img {
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📸 Album Photo du Mariage</h1>
        <p>Partagez et revivez les plus beaux moments de notre journée</p>
    </div>

    <div class="tabs">
        <div class="tab <?php echo $activeTab === 'upload' ? 'active' : ''; ?>" onclick="openTab('upload')">Envoyer des photos</div>
        <div class="tab <?php echo $activeTab === 'gallery' ? 'active' : ''; ?>" onclick="openTab('gallery')">Galerie Photos</div>
    </div>

    <div id="upload" class="tab-content <?php echo $activeTab === 'upload' ? 'active' : ''; ?>">
        <div class="upload-container">
            <div id="drop-zone" ondragover="event.preventDefault()" ondrop="handleDrop(event)">
                <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#e75480" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <p>Glissez-déposez vos photos ici<br>ou</p>
                <input type="file" id="file-input" multiple accept="image/*" onchange="handleFiles(this.files)">
                <button onclick="document.getElementById('file-input').click()" class="button">
                    Choisir des photos
                </button>
            </div>

            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Envoi en cours...</p>
                <progress id="progress-bar" max="100" value="0"></progress>
            </div>

            <div id="message"></div>
        </div>
    </div>

    <div id="gallery" class="tab-content <?php echo $activeTab === 'gallery' ? 'active' : ''; ?>">
        <?php if (!$authenticated): ?>
            <div class="password-form">
                <h2>Accès protégé</h2>
                <?php if (isset($error)): ?>
                    <div class="password-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="POST" action="?tab=gallery">
                    <div class="form-group">
                        <label for="password">Entrez le mot de passe :</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="button">Accéder à la galerie</button>
                </form>
            </div>
        <?php else: ?>
            <div class="gallery-container">
                <h2>Nos souvenirs en images</h2>
                
                <div class="carousel">
                    <div class="carousel-inner" id="carousel-inner">
                        <!-- Les images seront ajoutées ici par JavaScript -->
                    </div>
                    
                    <button class="carousel-control prev" onclick="prevSlide()">❮</button>
                    <button class="carousel-control next" onclick="nextSlide()">❯</button>
                    
                    <div class="carousel-caption">
                        <span id="photo-counter">0/0</span>
                    </div>
                </div>
                
                <div class="carousel-indicators" id="indicators">
                    <!-- Les indicateurs seront ajoutés ici -->
                </div>
                
                <div class="thumbnails" id="thumbnails">
                    <!-- Les miniatures seront ajoutées ici -->
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        let currentSlide = 0;
        let photos = [];
        let touchStartX = 0;
        let touchEndX = 0;

        function handleFiles(files) {
            if (files.length === 0) return;
            
            const formData = new FormData();
            for(let i = 0; i < files.length; i++) {
                if (files[i].size > 5 * 1024 * 1024) {
                    showMessage(`Le fichier ${files[i].name} est trop volumineux (max 5MB)`, 'error');
                    continue;
                }
                formData.append('photos[]', files[i]);
            }

            if (formData.has('photos[]')) {
                uploadFiles(formData);
            }
        }

        function handleDrop(e) {
            e.preventDefault();
            e.stopPropagation();
            handleFiles(e.dataTransfer.files);
        }

        async function uploadFiles(formData) {
            const loading = document.getElementById('loading');
            const message = document.getElementById('message');
            const progressBar = document.getElementById('progress-bar');
            
            loading.style.display = 'block';
            message.textContent = '';
            progressBar.value = 0;

            try {
                const response = await fetch('upload.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if(result.success) {
                    showMessage('✅ Photos envoyées avec succès ! Merci pour votre contribution.', 'success');
                    document.getElementById('file-input').value = '';
                } else {
                    showMessage('❌ Erreur: ' + result.error, 'error');
                }
            } catch (error) {
                showMessage('❌ Erreur de connexion au serveur', 'error');
            } finally {
                loading.style.display = 'none';
                progressBar.value = 0;
            }
        }

        function showMessage(text, type) {
            const message = document.getElementById('message');
            message.innerHTML = text;
            message.style.color = type === 'error' ? '#d32f2f' : '#388e3c';
            message.style.padding = '10px';
            message.style.borderRadius = '4px';
            message.style.backgroundColor = type === 'error' ? '#ffebee' : '#e8f5e9';
            message.style.textAlign = 'center';
            message.style.margin = '10px 0';
        }

        async function loadGallery() {
            try {
                const response = await fetch('gallery.php');
                if (!response.ok) throw new Error('Erreur d\'authentification');
                
                photos = await response.json();

                if(photos.length > 0) {
                    initCarousel();
                } else {
                    document.getElementById('carousel-inner').innerHTML = 
                        '<div class="carousel-item"><p style="text-align:center;padding:50px;">Aucune photo partagée pour le moment. Soyez le premier !</p></div>';
                }
            } catch (error) {
                console.error('Erreur:', error);
                document.getElementById('carousel-inner').innerHTML = 
                    '<div class="carousel-item"><p style="text-align:center;padding:50px;color:red;">Erreur lors du chargement de la galerie</p></div>';
            }
        }

        function initCarousel() {
            const carouselInner = document.getElementById('carousel-inner');
            const indicators = document.getElementById('indicators');
            const thumbnails = document.getElementById('thumbnails');
            
            carouselInner.innerHTML = '';
            indicators.innerHTML = '';
            thumbnails.innerHTML = '';
            
            photos.forEach((photo, index) => {
                const item = document.createElement('div');
                item.className = 'carousel-item';
                
                const img = document.createElement('img');
                img.src = 'uploads/' + photo;
                img.alt = 'Photo de mariage ' + (index + 1);
                img.loading = 'lazy';
                
                item.appendChild(img);
                carouselInner.appendChild(item);
                
                const indicator = document.createElement('div');
                indicator.className = 'indicator' + (index === 0 ? ' active' : '');
                indicator.onclick = () => goToSlide(index);
                indicators.appendChild(indicator);
                
                const thumbnail = document.createElement('div');
                thumbnail.className = 'thumbnail' + (index === 0 ? ' active' : '');
                thumbnail.onclick = () => goToSlide(index);
                
                const thumbImg = document.createElement('img');
                thumbImg.src = 'uploads/' + photo;
                thumbImg.alt = 'Miniature ' + (index + 1);
                
                thumbnail.appendChild(thumbImg);
                thumbnails.appendChild(thumbnail);
            });
            
            updateCounter();
            
            carouselInner.addEventListener('touchstart', handleTouchStart, false);
            carouselInner.addEventListener('touchend', handleTouchEnd, false);
        }

        function updateCounter() {
            document.getElementById('photo-counter').textContent = 
                `${currentSlide + 1}/${photos.length}`;
        }

        function goToSlide(index) {
            if (index < 0 || index >= photos.length) return;
            
            currentSlide = index;
            const carouselInner = document.getElementById('carousel-inner');
            carouselInner.style.transform = `translateX(-${currentSlide * 100}%)`;
            
            document.querySelectorAll('.indicator').forEach((ind, i) => {
                ind.classList.toggle('active', i === currentSlide);
            });
            
            document.querySelectorAll('.thumbnail').forEach((thumb, i) => {
                thumb.classList.toggle('active', i === currentSlide);
                
                if (i === currentSlide) {
                    thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }
            });
            
            updateCounter();
        }

        function nextSlide() {
            if (currentSlide < photos.length - 1) {
                goToSlide(currentSlide + 1);
            } else {
                goToSlide(0);
            }
        }

        function prevSlide() {
            if (currentSlide > 0) {
                goToSlide(currentSlide - 1);
            } else {
                goToSlide(photos.length - 1);
            }
        }

        function handleTouchStart(e) {
            touchStartX = e.changedTouches[0].screenX;
        }

        function handleTouchEnd(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }

        function handleSwipe() {
            const diff = touchStartX - touchEndX;
            if (diff > 50) {
                nextSlide();
            } else if (diff < -50) {
                prevSlide();
            }
        }

        function openTab(tabName, preventHistory = false) {
            const tabs = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }

            const tabButtons = document.getElementsByClassName('tab');
            for (let i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove('active');
            }

            document.getElementById(tabName).classList.add('active');
            document.querySelector(`.tab[onclick="openTab('${tabName}')"]`).classList.add('active');

            if(tabName === 'gallery') {
                fetch('check-auth.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.authenticated) {
                            loadGallery();
                        }
                    });
            }

            if (!preventHistory && window.history && window.history.pushState) {
                const newUrl = window.location.pathname + `?tab=${tabName}`;
                window.history.pushState({tab: tabName}, '', newUrl);
            }
        }

        let slideInterval;
        function startAutoSlide() {
            slideInterval = setInterval(nextSlide, 5000);
        }

        function stopAutoSlide() {
            clearInterval(slideInterval);
        }

        document.getElementById('gallery').addEventListener('mouseenter', stopAutoSlide);
        document.getElementById('gallery').addEventListener('mouseleave', startAutoSlide);

        window.addEventListener('popstate', function(event) {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab') || 'upload';
            openTab(tab, true);
        });

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab') || '<?php echo $activeTab; ?>';
            openTab(tab, true);
            
            if (tab === 'gallery' && <?php echo $authenticated ? 'true' : 'false'; ?>) {
                loadGallery();
            }
        });
    </script>
</body>
</html>