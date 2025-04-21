document.addEventListener('DOMContentLoaded', () => {
    const project_pdf_modal = document.getElementById('wtviewpdf')

    if (project_pdf_modal) {
        project_pdf_modal.addEventListener('show.bs.modal', event => {
            // Button that triggered the modal
            const button = event.relatedTarget;
            // Extract info from data-bs-* attributes
            const file_url = button.getAttribute('data-file-url');
            console.log(file_url);

            // Update the modal's content.
            const pdfContainer = project_pdf_modal.querySelector('.modal-body > .pdf-container');
            let modalLoader = document.getElementById('modal-loader');
            showLoader(modalLoader);

            var {pdfjsLib} = globalThis;
            let url = window.location.origin + '/' + file_url;

            let scale = 2;

            async function loadPDF(pdfUrl) {
                const loadingTask = pdfjsLib.getDocument(pdfUrl);
                const pdf = await loadingTask.promise;

                const canvases = [];

                for (let i = 1; i <= pdf.numPages; i++) {
                    const page = await pdf.getPage(i);
                    const viewport = page.getViewport({ scale: 2 });
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    canvas.setAttribute('data-page-num', i);
                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };

                    await page.render(renderContext).promise;
                    canvases.push(canvas); // Добавляем канвас в массив по порядку
                }

                return canvases;
            }

            // Использование функции
            loadPDF(url).then(canvases => {
                // canvases теперь содержит все страницы в правильном порядке
                canvases.forEach((canvas, index) => {
                    pdfContainer.appendChild(canvas);
                    console.log(`Page ${index + 1} appended.`);
                });
            }).catch(error => {
                console.error('Error loading PDF: ', error);
            }).then(() => {
                hideLoader(modalLoader);
                initProgressBar();
            });

        });// if wt quick lnks

        project_pdf_modal.addEventListener('hidden.bs.modal', event => {
            const pdfContainer = project_pdf_modal.querySelector('.modal-body > .pdf-container');
            pdfContainer.innerHTML = '';
        });

        function showLoader(modalLoader) {

            modalLoader.classList.remove('d-none');
            modalLoader.classList.add('z-1');

        }

        function hideLoader(modalLoader) {
            modalLoader.classList.add('d-none');
            modalLoader.classList.remove('z-1');
        }

        function initProgressBar()
        {
            const pdfContainer = project_pdf_modal.querySelector('.modal-body > .pdf-container');
            const modalBody = project_pdf_modal.querySelector('.modal-body');
            const progressBar = project_pdf_modal.querySelector('.progress');
            const progressBarLine = project_pdf_modal.querySelector('.progress .progress-bar');
            progressBar.setAttribute('aria-valuenow', 0);
            progressBarLine.style.width = '0%';

            let fulloffsetHeight = pdfContainer.offsetHeight - modalBody.offsetHeight;
            let currentProgress = 0;

            modalBody.addEventListener('scroll', () => {
                currentProgress = modalBody.scrollTop * 100 / fulloffsetHeight;
                progressBar.setAttribute('aria-valuenow', currentProgress);
                progressBarLine.style.width = currentProgress + '%';
            });
        }
    }

});