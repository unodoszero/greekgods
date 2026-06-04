document.addEventListener("DOMContentLoaded", () => {
    // Smooth scrolling for internal links
    const links = document.querySelectorAll("section a");

    links.forEach(link => {
        link.addEventListener("click", (event) => {
            event.preventDefault();
            const targetId = link.getAttribute("href").substring(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            }
        });
    });

    // Print functionality with fallback
    const printButton = document.querySelector(".print-button");

    printButton.addEventListener("click", () => {
        if (typeof jsPDF !== "undefined") {
            try {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                doc.html(document.body, {
                    callback: function (doc) {
                        doc.save("terms-of-use.pdf");
                    },
                    margin: [10, 10, 10, 10],
                    x: 10,
                    y: 10,
                    html2canvas: { scale: 2 },
                });
            } catch (error) {
                console.error("jsPDF error:", error);
                notify("warning", "Failed to generate PDF. Opening browser print instead.");
                window.print();
            }
        } else {
            console.warn("jsPDF not available. Using browser print as fallback.");
            notify("info", "PDF tools are unavailable. Opening browser print instead.");
            window.print();
        }
    });

    const headings = document.querySelectorAll("main article h2");

    headings.forEach(heading => {
        heading.addEventListener("click", (event) => {
            const isGoBackIcon = event.target === heading; // Ensure we only trigger on clicks meant for the heading
            if (isGoBackIcon) {
                // Scroll back to the top or a specific element
                document.querySelector("body").scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            }
        });
    });
});

function notify(type, message) {
    const toaster = window.GreekGodsToast;

    if (message && typeof toaster?.[type] === "function") {
        toaster[type](message);
    }
}
