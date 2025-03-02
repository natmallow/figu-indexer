<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JavaScript Text to Speech</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }

        .container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <textarea id="text" rows="5" cols="30"></textarea>
        <select id="voices"></select>
        <input id="rate" type="range" min="0.5" max="2" value="1" step="0.1" />
        <button id="speak">Speak</button>
    </div>

    <script src="app.js"></script>
</body>

</html>