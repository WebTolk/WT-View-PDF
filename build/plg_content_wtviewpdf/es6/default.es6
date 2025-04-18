document.addEventListener('DOMContentLoaded', () => {
    const elements = document.querySelectorAll('.wtviewpdf-default');

    elements.forEach((element) => {
            const file_url = element.getAttribute('data-file-url');
            const pdfContainer = element.querySelector('.pdf-container');
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
                    canvases.push(canvas);
                }

                return canvases;
            }

            loadPDF(url).then(canvases => {
                canvases.forEach((canvas, index) => {
                    pdfContainer.appendChild(canvas);
                    console.log(`Page ${index + 1} appended.`);
                });
            }).catch(error => {
                console.error('Error loading PDF: ', error);
            }).then(() => {
            });

        });
    });
