<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>" type="image/x-icon">
    <title>Logo Stamp | DGC</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/main-style.css'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

    <nav>
        <div class="logo">
            <a href="<?php echo base_url('logo_stamp'); ?>">
                <img src="<?php echo base_url('assets/images/logo.png'); ?>" alt="DGC Feedback Logo" />
            </a>
        </div>

        <div class="nav-toggle" onclick="toggleNav()">&#9776;</div>
        <ul id="nav-menu">
            <li><a href="https://diredawacommunication.org/" target="_blank">Home</a></li>
            <li><a href="https://diredawacommunication.org/public_opinion" target="_blank">Public Opinion</a></li>
            <li><a href="http://197.156.113.12:8080/ebook" target="_blank">E-Library</a></li>
        </ul>
    </nav>



    <!-- Main Content -->
    <div class="container">
        <div class="logo-adder-container">
            <div class="dropbox" id="dropbox">Drop images here or click to select your images</div>
            <input type="file" id="fileInput" multiple accept="image/*">
            <div class="image-preview" id="imagePreview"></div>
            <div class="buttons-container">
                <button onclick="uploadImages()">Create</button>
                <button class="clear-button" onclick="clearImages()">Clear</button>
            </div>
            <div id="messageContainer"></div>
        </div>
    </div>


    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>About Us</h3>
                <p>Dire Dawa Government Communiation Affairs Bureau</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="https://diredawacommunication.org/" target="_blank">News</a></li>
                    <li><a href="https://diredawacommunication.org/public_opinion" target="_blank">Public Opinion</a>
                    </li>
                    <li><a href="http://197.156.113.12:8080/ebook" target="_blank">E-Library</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <ul>
                    <li><i class="bi bi-geo-alt-fill"></i>Dire Dawa, Finance Building</li>
                    <li><i class="bi bi-telephone-fill"></i>+25 111 6061</li>
                    <li><i class="bi bi-envelope-at-fill"></i>menegestcomm@gmail.com</li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="https://web.facebook.com/DGCOMU" target="_blank"><i class='bi bi-facebook'></i></a>
                    <a href="https://t.me/DDGCAB" target="_blank"><i class='bi bi-telegram'></i></a>
                    <a href="https://www.youtube.com/@Direcommunication" target="_blank"><i
                            class='bi bi-youtube'></i></a>
                    <a href="https://www.tiktik.com/@Direcommunication" target="_blank"><i class='bi bi-tiktok'></i></a>

                    <a href="https://twitter.com/DawaOffice" target="_blank"><i class='bi bi-twitter-x'></i></a>

                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <span id="year"></span> DGC Logo Stamp. All rights reserved.</p>
            <a href="https://www.linkedin.com/in/yeabsira-asaminew/" target="_blank"
                style="text-decoration: none; color: white; font-size: 10px;">
                Developed by <span style="color: rgb(241, 182, 5); transition: color 0.3s ease;"
                    onmouseover="this.style.color='rgb(250, 246, 233)'"
                    onmouseout="this.style.color='rgb(241, 182, 5)'">Yeabsira A.</span>
            </a><br>
            <a href="#" class="back-to-top">Back to Top</a>
        </div>




    </footer>
    <script>
        document.getElementById("year").textContent = new Date().getFullYear();
        document.addEventListener('paste', handlePaste);

        const dropbox = document.getElementById('dropbox');
        const fileInput = document.getElementById('fileInput');
        const imagePreview = document.getElementById('imagePreview');
        const messageContainer = document.getElementById('messageContainer');

        let filesArray = []; // Array to hold all uploaded files

        // Event listeners for dropbox and file input
        dropbox.addEventListener('click', () => fileInput.click());
        dropbox.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropbox.style.backgroundColor = '#e0e0e0';
        });
        dropbox.addEventListener('dragleave', () => {
            dropbox.style.backgroundColor = '';
        });
        dropbox.addEventListener('drop', (event) => {
            event.preventDefault();
            const newFiles = event.dataTransfer.files;
            addFilesToArray(newFiles);
            dropbox.style.backgroundColor = '';
            showPreview();
        });
        fileInput.addEventListener('change', () => {
            const newFiles = fileInput.files;
            addFilesToArray(newFiles);
            showPreview();
        });

        function handlePaste(event) {
            const items = (event.clipboardData || event.originalEvent.clipboardData).items;
            for (let item of items) {
                if (item.type.indexOf('image') !== -1) {
                    const blob = item.getAsFile();
                    if (blob) {
                        filesArray.push(blob);
                        showPreview();
                    }
                }
            }
        }

        function addFilesToArray(newFiles) {
            for (let i = 0; i < newFiles.length; i++) {
                filesArray.push(newFiles[i]);
            }
        }

        function showPreview() {
            imagePreview.innerHTML = '';
            for (let i = 0; i < filesArray.length; i++) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(filesArray[i]);
                imagePreview.appendChild(img);
            }
        }

        function clearImages() {
            filesArray = [];
            imagePreview.innerHTML = '';
            fileInput.value = '';
        }

        //  Updated upload function to handle base64 downloads
        async function uploadImages() {
            if (filesArray.length === 0) {
                showMessage('No images selected.', 'error');
                return;
            }

            const formData = new FormData();
            for (let i = 0; i < filesArray.length; i++) {
                formData.append('images[]', filesArray[i]);
            }

            try {
                showMessage('Processing images...', 'info');

                const response = await fetch('<?php echo site_url('logo_stamp/upload_images'); ?>', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Server error');
                }

                if (result.status === 'success') {
                    showMessage(result.message, 'success');

                    // Trigger individual downloads with "stamped/" in filename
                    result.images.forEach(img => {
                        const link = document.createElement('a');
                        link.href = img.url;

                        // This may create a "stamped" folder inside Downloads on most browsers
                        link.download = `stamped/${img.name}`;

                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    });
                } else {
                    throw new Error(result.message || 'Unknown error occurred');
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage(error.message, 'error');
            }
        }

        function showMessage(message, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            messageDiv.textContent = message;
            messageContainer.appendChild(messageDiv);

            setTimeout(() => {
                messageDiv.remove();
            }, 3000);
        }

        // Navigation functions (unchanged)
        function toggleNav() {
            const navMenu = document.getElementById('nav-menu');
            navMenu.classList.toggle('active');
        }

        document.getElementById('current-year').textContent = new Date().getFullYear();
        document.querySelector('.back-to-top').addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>



</body>

</html>