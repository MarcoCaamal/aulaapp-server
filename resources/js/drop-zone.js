import 'sweetalert2/dist/sweetalert2.min.css';
import Swal from "sweetalert2";


document.addEventListener('DOMContentLoaded', () => {
    inicarApp();
});

function inicarApp() {
    inicarDropZone();
}

function inicarDropZone() {
    document.querySelectorAll(".drop-zone__input").forEach((inputElement) => {
        const dropZoneElement = inputElement.closest(".drop-zone");

        dropZoneElement.addEventListener("click", (e) => {
            inputElement.click();
        });

        inputElement.addEventListener("change", (e) => {
            if (inputElement.files.length) {
                updateThumbnail(dropZoneElement, inputElement.files[0]);
            }
        });

        dropZoneElement.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropZoneElement.classList.add("drop-zone--over");
        });

        ["dragleave", "dragend"].forEach((type) => {
            dropZoneElement.addEventListener(type, (e) => {
                dropZoneElement.classList.remove("drop-zone--over");
            });
        });

        dropZoneElement.addEventListener("drop", (e) => {
            e.preventDefault();

            if (e.dataTransfer.files.length) {
                inputElement.files = e.dataTransfer.files;
                updateThumbnail(dropZoneElement, e.dataTransfer.files[0]);
            }

            dropZoneElement.classList.remove("drop-zone--over");
        });
    });
}

/**
 * Updates the thumbnail on a drop zone element.
 *
 * @param {HTMLElement} dropZoneElement
 * @param {File} file
 */
function updateThumbnail(dropZoneElement, file) {
	let thumbnailElement = dropZoneElement.querySelector(".drop-zone__thumb");

	// First time - remove the prompt
	if (dropZoneElement.querySelector(".drop-zone__prompt")) {
		dropZoneElement.querySelector(".drop-zone__prompt").style.display = 'none';
	}

	// First time - there is no thumbnail element, so lets create it
	if (!thumbnailElement) {
		thumbnailElement = document.createElement("div");
		thumbnailElement.classList.add("drop-zone__thumb");
		dropZoneElement.appendChild(thumbnailElement);
	}

	thumbnailElement.dataset.label = file.name;

	// Show thumbnail for image files
	// if (file.type.startsWith("image/")) {
	// 	const reader = new FileReader();

	// 	reader.readAsDataURL(file);
	// 	reader.onload = () => {
	// 		thumbnailElement.style.backgroundImage = `url('${reader.result}')`;
	// 	};
    // }
    if (file.type.includes('csv')) {
		thumbnailElement.style.backgroundImage = `url(https://www.svgrepo.com/show/424848/csv-file-type.svg)`;
    } else {
        thumbnailElement.style.backgroundImage = null;
        Swal.fire({
            title: 'Formato de archivo no permitido',
            text: 'Solo se permite archivos con formato .csv',
            icon: 'warning',
        })
        thumbnailElement.remove();
        dropZoneElement.querySelector(".drop-zone__prompt").style.display = 'block';
    }

    console.log(file.type);
}
