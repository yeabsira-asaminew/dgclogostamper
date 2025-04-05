const dropbox = document.getElementById('dropbox');
const fileInput = document.getElementById('fileInput');
const imagePreview = document.getElementById('imagePreview');
const messageContainer = document.getElementById('messageContainer');

let filesArray = []; // Array to hold all uploaded files
let selectedFolderHandle = null; // Variable to store the selected folder handle

// Retrieve the selected folder handle from localStorage
const storedFolderHandle = localStorage.getItem('selectedFolderHandle');
if (storedFolderHandle) {
    selectedFolderHandle = JSON.parse(storedFolderHandle);
    showMessage('Folder already selected.', 'success');
}

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

// Function to add files to the array
function addFilesToArray(newFiles) {
    for (let i = 0; i < newFiles.length; i++) {
        filesArray.push(newFiles[i]);
    }
}

// Function to show image preview
function showPreview() {
    imagePreview.innerHTML = '';
    for (let i = 0; i < filesArray.length; i++) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(filesArray[i]);
        imagePreview.appendChild(img);
    }
}

// Function to clear images
function clearImages() {
    filesArray = []; // Clear the files array
    imagePreview.innerHTML = ''; // Clear the preview
    fileInput.value = ''; // Clear the file input
}

// Function to select a folder
async function selectFolder() {
    try {
        selectedFolderHandle = await window.showDirectoryPicker();
        localStorage.setItem('selectedFolderHandle', JSON.stringify(selectedFolderHandle)); // Save folder handle to localStorage
        showMessage('Folder selected successfully.', 'success');
    } catch (error) {
        showMessage('Failed to select folder.', 'error');
    }
}

// Function to upload images
async function uploadImages() {
    if (filesArray.length === 0) {
        showMessage('Please select at least one image.', 'error');
        return;
    }

    const formData = new FormData();
    for (let i = 0; i < filesArray.length; i++) {
        formData.append('images[]', filesArray[i]);
    }

    try {
        const response = await fetch('<?php echo site_url("logo_stamp/upload_images"); ?>', {
            method: 'POST',
            body: formData,
        });

        if (!response.ok) {
            const errorData = await response.json();
            showMessage(errorData.message || 'An error occurred while processing images.', 'error');
            return;
        }

        const blob = await response.blob();
        const zipFileHandle = await selectedFolderHandle.getFileHandle('zipped_images.zip', { create: true });
        const writable = await zipFileHandle.createWritable();
        await writable.write(blob);
        await writable.close();

        showMessage('Images processed and saved successfully!', 'success');
    } catch (error) {
        showMessage('An error occurred while processing images: ' + error.message, 'error');
        console.error(error);
    }
}


// Function to show messages
function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    messageContainer.appendChild(messageDiv);

    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}

// Additional functions for navigation and back-to-top button
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