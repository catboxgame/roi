<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catbox Price</title>
</head>
<body>
    <h1>Catbox Price in USD</h1>
    <div id="price">Loading...</div>

    <script>
        async function getCatboxPrice() {
            const url = "https://api.dexscreener.com/latest/dex/pairs/ton/EQDq1cQKb6vQItJgBhwn7bS2FpJNnDLOKDUIBF8hHJgQwqlD";
            
            try {
                const response = await fetch(url);
                const data = await response.json();
                const priceUsd = data.pair?.priceUsd;

                document.getElementById('price').textContent = priceUsd ? `$${priceUsd}` : 'Price not available';
            } catch (error) {
                console.error("Error fetching price:", error);
                document.getElementById('price').textContent = 'Error fetching price';
            }
        }

        getCatboxPrice();
    </script>
</body>
</html>
