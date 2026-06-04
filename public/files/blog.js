// Wait for the DOM content to load
document.addEventListener("DOMContentLoaded", () => {
    // Select all articles
    const articles = document.querySelectorAll("article");

    // Add a click event listener to each article
    articles.forEach((article, index) => {
        article.addEventListener("click", () => {
            // Redirect to article.html with a query parameter (if you want unique pages)
            window.location.href = `/articles/article.html?article=${index + 1}`;
        });
    });
});

// Get query parameters
const params = new URLSearchParams(window.location.search);

// Get the article ID from the query string
const articleId = params.get("article");

if (articleId) {
    // Display article content based on the ID
    document.querySelector("h1").textContent = `Article ${articleId}`;
    // Add more logic here to load article content dynamically
}
